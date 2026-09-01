<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Entities;

use App\Base\Entity;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use App\Domain\Purchase\ValueObjects\SupplierPurchaseValue;
use App\Domain\Purchase\ValueObjects\UserPurchaseValue;
use DateTimeInterface;
use InvalidArgumentException;
use Override;

class PurchaseEntity extends Entity
{
    /**
     * ID
     */
    public readonly string $id;

    /**
     * User yang membuat
     */
    public readonly UserPurchaseValue $user;

    /**
     * Supplier
     */
    public readonly ?SupplierPurchaseValue $supplier;

    /**
     * Tanggal pembelian
     */
    public readonly DateTimeInterface $purchaseDate;

    /**
     * Nomor invoice
     */
    public readonly ?string $invoiceNumber;

    /**
     * Toatal quantity
     */
    public readonly int $totalQuantity;

    /**
     * Total price
     */
    public readonly int $totalPrice;

    /**
     * Status pembayaran
     */
    public readonly ?PaymentStatusEnum $paymentStatus;

    /**
     * Catatan
     */
    public readonly ?int $notes;

    /**
     * Daftar produk yang dibeli.
     *
     * @var PurchaseDetailEntity[]
     */
    public readonly array $details;

    /**
     * Waktu dibuat
     */
    public readonly ?DateTimeInterface $createdAt;

    /**
     * Waktu diubah
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
        ?SupplierPurchaseValue $supplier,
        DateTimeInterface $purchaseDate,
        ?string $invoiceNumber,
        int $totalQuantity,
        int $totalPrice,
        ?PaymentStatusEnum $paymentStatus,
        ?int $notes,
        array $details,
        ?DateTimeInterface $createdAt,
        ?DateTimeInterface $updatedAt,
        ?DateTimeInterface $deletedAt,
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->supplier = $supplier;
        $this->purchaseDate = $purchaseDate;
        $this->invoiceNumber = $invoiceNumber;
        $this->totalQuantity = $totalQuantity;
        $this->totalPrice = $totalPrice;
        $this->paymentStatus = $paymentStatus;
        $this->notes = $notes;
        $this->details = $this->detailsValidator($details);
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
            'user' => $this->user->toArray(),
            'supplier' => $this->supplier?->toArray(),
            'purchase_date' => $this->purchaseDate,
            'invoice_number' => $this->invoiceNumber,
            'total_quantity' => $this->totalQuantity,
            'total_price' => $this->totalPrice,
            'payment_status' => $this->paymentStatus?->value,
            'notes' => $this->notes,
            'details' => array_map(fn ($detail) => $detail->toArray(), $this->details),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Validasi argument details
     *
     * @return PurchaseDetailEntity[]
     */
    private function detailsValidator(array $details): array
    {
        $result = [];

        foreach ($details as $detail) {
            if ($detail instanceof PurchaseDetailEntity) {
                $result[] = $detail;

                continue;
            }

            throw new InvalidArgumentException(
                sprintf('Argument details harus merupakan instansiasi dari %s', PurchaseDetailEntity::class),
            );
        }

        return $result;
    }
}
