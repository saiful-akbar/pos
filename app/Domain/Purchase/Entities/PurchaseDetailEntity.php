<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Entities;

use App\Base\Entity;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use App\Domain\Purchase\ValueObjects\ProductPurchaseValue;
use App\Domain\Purchase\ValueObjects\SupplierPurchaseValue;
use App\Domain\Purchase\ValueObjects\UserPurchaseValue;
use DateTimeInterface;
use InvalidArgumentException;
use Override;

class PurchaseDetailEntity extends Entity
{
    /**
     * ID
     */
    public readonly string $id;

    /**
     * Supplier
     */
    public readonly ?SupplierPurchaseValue $supplier;

    /**
     * User yang membuat
     */
    public readonly ?UserPurchaseValue $user;

    /**
     * Nomor invoice
     */
    public readonly ?string $invoiceNumber;

    /**
     * Jumlah total
     */
    public readonly int $totalAmount;

    /**
     * Sttaus pembayaran
     */
    public readonly ?PaymentStatusEnum $paymentStatus;

    /**
     * Catatan
     */
    public readonly ?string $notes;

    /**
     * Daftar produk
     *
     * @var ProductPurchaseValue[]
     */
    public readonly array $products;

    /**
     * Waktu dibuat
     */
    public readonly ?DateTimeInterface $createdAt;

    /**
     * Waktu diperbaiki
     */
    public readonly ?DateTimeInterface $updatedAt;

    /**
     * Waktu dihapus.
     */
    public readonly ?DateTimeInterface $deletedAt;

    /**
     * Create a new class instance.
     */
    public function __construct(
        string $id,
        UserPurchaseValue $user,
        ?SupplierPurchaseValue $supplier = null,
        ?string $invoiceNumber = null,
        int $totalAmount = 0,
        ?PaymentStatusEnum $paymentStatus = null,
        ?string $notes = null,
        array $products = [],
        ?DateTimeInterface $createdAt = null,
        ?DateTimeInterface $updatedAt = null,
        ?DateTimeInterface $deletedAt = null,
    ) {
        $this->id = $id;
        $this->supplier = $supplier;
        $this->user = $user;
        $this->invoiceNumber = $invoiceNumber;
        $this->totalAmount = $totalAmount;
        $this->paymentStatus = $paymentStatus;
        $this->notes = $notes;
        $this->products = $this->productsValidator(products: $products);
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->deletedAt = $deletedAt;
    }

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'supplier' => $this->supplier?->toArray(),
            'user' => $this->user?->toArray(),
            'invoice_number' => $this->invoiceNumber,
            'total_amount' => $this->totalAmount,
            'payment_status' => $this->paymentStatus,
            'notes' => $this->notes,
            'products' => array_map(fn ($products) => $products->toArray(), $this->products),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Validasi properti products.
     *
     * @return ProductPurchaseValue[]
     */
    private function productsValidator(array $products): array
    {
        $result = [];

        foreach ($products as $product) {
            if ($product instanceof ProductPurchaseValue) {
                $result = $products;

                continue;
            }

            throw new InvalidArgumentException(
                sprintf('Properti products harus merupakan instansiasi dari %s', ProductPurchaseValue::class)
            );
        }

        return $result;
    }
}
