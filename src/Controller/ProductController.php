<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\UseCase\GetProductsUseCase;
use App\Application\DTO\GetProductsRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    public function __construct(
        private readonly GetProductsUseCase $getProductsUseCase,
        private readonly ValidatorInterface $validator
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        // Create and validate request DTO
        $getProductsRequest = new GetProductsRequest(
            $request->query->get('category'),
            $request->query->get('priceLessThan'),
            (int) $request->query->get('page', 1),
            min((int) $request->query->get('itemsPerPage', 5), 20)
        );

        $violations = $this->validator->validate($getProductsRequest);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            $response = $this->getProductsUseCase->execute($getProductsRequest);

            return new JsonResponse($response->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'An error occurred while fetching products.',
                'message' => $this->getParameter('kernel.environment') === 'dev' ? $e->getMessage() : null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}