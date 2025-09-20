<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Symfony\Component\Serializer\Annotation\Groups;

class Price
{
    public const DEFAULT_CURRENCY = 'EUR';

    #[Groups(['product:read'])]
    public readonly int $original;

    #[Groups(['product:read'])]
    public readonly int $final;

    #[Groups(['product:read'])]
    public readonly ?string $discount_percentage;

    #[Groups(['product:read'])]
    public readonly string $currency;

    public function __construct(
        int $original,
        int $final,
        ?string $discountPercentage = null,
        string $currency = self::DEFAULT_CURRENCY
    ) {
        $this->original = $original;
        $this->final = $final;
        $this->discount_percentage = $discountPercentage;
        $this->currency = $currency;
    }

    public function hasDiscount(): bool
    {
        return $this->original !== $this->final;
    }

    public function getDiscountAmount(): int
    {
        return $this->original - $this->final;
    }
}