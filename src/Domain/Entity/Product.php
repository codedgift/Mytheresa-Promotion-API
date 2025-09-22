<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use App\Controller\ProductController;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
#[ORM\Index(columns: ['category'], name: 'idx_product_category')]
#[ORM\Index(columns: ['price'], name: 'idx_product_price')]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/products',
            controller: ProductController::class,
            name: 'get_products_with_discounts',
            openapi: new Operation(
                summary: 'Get products with discounts applied',
                description: 'Returns a list of products with discounts applied. Can be filtered by category and price (before discounts).',
                parameters: [
                    new Parameter(
                        name: 'category',
                        in: 'query',
                        description: 'Filter products by category (e.g., boots, sandals, sneakers)',
                        required: false,
                        schema: ['type' => 'string', 'example' => 'boots']
                    ),
                    new Parameter(
                        name: 'priceLessThan',
                        in: 'query',
                        description: 'Filter products with price less than or equal to this value (before discounts applied)',
                        required: false,
                        schema: ['type' => 'integer', 'example' => 80000]
                    )
                ]
            )
        )
    ],
    normalizationContext: ['groups' => ['product:read']],
    paginationEnabled: true,
    paginationItemsPerPage: 5,
    paginationMaximumItemsPerPage: 20
)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[Groups(['product:read'])]
    private string $sku = '';

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Groups(['product:read'])]
    private string $name = '';

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(['product:read'])]
    private string $category = '';

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    #[Groups(['product:read'])]
    private int $price = 0;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): self
    {
        $this->sku = $sku;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}