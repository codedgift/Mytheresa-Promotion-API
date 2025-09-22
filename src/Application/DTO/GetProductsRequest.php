<?php

declare(strict_types=1);

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GetProductsRequest
{
    #[Assert\Length(max: 100)]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9_-]+$/',
        message: 'Category can only contain letters, numbers, underscores and hyphens'
    )]
    public readonly ?string $category;

    #[Assert\Type('integer')]
    #[Assert\GreaterThan(0, message: 'Price must be greater than 0')]
    public readonly ?int $priceLessThan;

    #[Assert\Type('integer')]
    #[Assert\GreaterThan(0, message: 'Page must be greater than 0')]
    public readonly int $page;

    #[Assert\Type('integer')]
    #[Assert\Range(min: 1, max: 20, notInRangeMessage: 'Items per page must be between 1 and 20')]
    public readonly int $itemsPerPage;

    public function __construct(
        ?string $category,
        ?string $priceLessThan,
        int $page = 1,
        int $itemsPerPage = 5
    ) {
        $this->category = $category;
        $this->priceLessThan = $priceLessThan !== null ? (int) $priceLessThan : null;
        $this->page = $page;
        $this->itemsPerPage = $itemsPerPage;
    }
}