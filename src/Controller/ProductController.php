<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Service\ProductServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductServiceInterface $productService,
        private readonly SerializerInterface $serializer
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $category = $request->query->get('category');
        $priceLessThan = $request->query->get('priceLessThan');
        $limit = min((int) $request->query->get('itemsPerPage', 5), 20);

        // Validate priceLessThan parameter
        if ($priceLessThan !== null) {
            $priceLessThan = filter_var($priceLessThan, FILTER_VALIDATE_INT);
            if ($priceLessThan === false || $priceLessThan < 0) {
                return new JsonResponse([
                    'error' => 'Invalid priceLessThan parameter. Must be a positive integer.'
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        try {
            $products = $this->productService->getProductsWithDiscounts(
                $category,
                $priceLessThan,
                $limit
            );

            $jsonData = $this->serializer->serialize($products, 'json', [
                'groups' => ['product:read']
            ]);

            return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'An error occurred while fetching products.',
                'message' => $this->getParameter('kernel.environment') === 'dev' ? $e->getMessage() : null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}