<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\GetProductsRequest;
use App\Application\DTO\GetProductsResponse;
use App\Application\Service\ProductServiceInterface;

class GetProductsUseCase
{
    public function __construct(
        private readonly ProductServiceInterface $productService
    ) {
    }

    public function execute(GetProductsRequest $request): GetProductsResponse
    {
        $offset = ($request->page - 1) * $request->itemsPerPage;
        
        $products = $this->productService->getProductsWithDiscounts(
            $request->category,
            $request->priceLessThan,
            $request->itemsPerPage,
            $offset
        );

        $totalItems = $this->productService->getTotalCount(
            $request->category,
            $request->priceLessThan
        );

        $totalPages = (int) ceil($totalItems / $request->itemsPerPage);

        return new GetProductsResponse(
            $products,
            $totalItems,
            $request->page,
            $request->itemsPerPage,
            $totalPages
        );
    }
}