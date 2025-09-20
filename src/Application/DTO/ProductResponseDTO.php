<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\ValueObject\Price;
use Symfony\Component\Serializer\Annotation\Groups;

class ProductResponseDTO
{
    #[Groups(['product:read'])]
    public readonly string $sku;

    #[Groups(['product:read'])]
    public readonly string $name;

    #[Groups(['product:read'])]
    public readonly string $category;

    #[Groups(['product:read'])]
    public readonly Price $price;

    public function __construct(
        string $sku,
        string $name,
        string $category,
        Price $price
    ) {
        $this->sku = $sku;
        $this->name = $name;
        $this->category = $category;
        $this->price = $price;
    }
}