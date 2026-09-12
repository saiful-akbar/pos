<?php

declare(strict_types=1);

namespace App\Domain\Sale\Entities;

use App\Base\Entity;
use App\Domain\Purchase\Enums\PaymentMethodEnum;
use App\Domain\Sale\ValueObjects\CustomerSaleValue;
use App\Domain\Sale\ValueObjects\UserSaleValue;
use DateTimeInterface;
use InvalidArgumentException;
use Override;

class SaleWithDetailEntity extends Entity
{
    /**
     * ID sales
     */
    public readonly string $id;

    /**
     * Data user yang melakukan transaksi.
     */
    public readonly UserSaleValue $user;

    /**
     * Data pelanggan.
     */
    public readonly ?CustomerSaleValue $customer;

    /**
     * Nomor faktur.
     */
    public readonly string $invoiceNumber;

    /**
     * Tanggal transaksi.
     */
    public readonly DateTimeInterface $transactionDate;

    /**
     * Total quantity yang dibeli.
     */
    public readonly int $totalQuantity;

    /**
     * Total harga yang dibeli.
     */
    public readonly int $totalAmount;

    /**
     * Jumlah pembayaran.
     */
    public readonly int $paidAmount;

    /**
     * Jumlah kembalian.
     */
    public readonly int $changeAmount;

    /**
     * Metode transaksi yang digunakan.
     */
    public readonly PaymentMethodEnum $paymentMethod;

    /**
     * Catatan tambahan pembelian.
     */
    public readonly ?string $notes;

    /**
     * Daftar detailproduk yang dibeli.
     *
     * @var SaleDetailEntity[]
     */
    public readonly array $details;

    /**
     * Waktu transaksi dibuat.
     */
    public readonly ?DateTimeInterface $createdAt;

    /**
     * Waktu transaksi diperbarui.
     */
    public readonly ?DateTimeInterface $updatedAt;

    /**
     * Waktu transaksi dihapus.
     */
    public readonly ?DateTimeInterface $deletedAt;

    /**
     * Create a new class instance.
     */
    public function __construct(
        string $id,
        UserSaleValue $user,
        ?CustomerSaleValue $customer,
        string $invoiceNumber,
        DateTimeInterface $transactionDate,
        int $totalQuantity,
        int $totalAmount,
        int $paidAmount,
        int $changeAmount,
        PaymentMethodEnum $paymentMethod,
        ?string $notes,
        array $details,
        ?DateTimeInterface $createdAt,
        ?DateTimeInterface $updatedAt,
        ?DateTimeInterface $deletedAt,
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->customer = $customer;
        $this->invoiceNumber = $invoiceNumber;
        $this->transactionDate = $transactionDate;
        $this->totalQuantity = $totalQuantity;
        $this->totalAmount = $totalAmount;
        $this->paidAmount = $paidAmount;
        $this->changeAmount = $changeAmount;
        $this->paymentMethod = $paymentMethod;
        $this->notes = $notes;
        $this->details = $this->validateDetails($details);
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->deletedAt = $deletedAt;
    }

    /**
     * KOnversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user->toArray(),
            'customer' => $this->customer->toArray(),
            'invoice_number' => $this->invoiceNumber,
            'transaction_date' => $this->transactionDate->format('Y-m-d'),
            'total_uantity' => $this->totalQuantity,
            'total_amount' => $this->totalAmount,
            'paid_amount' => $this->paidAmount,
            'change_amount' => $this->changeAmount,
            'payment_method' => $this->paymentMethod->value,
            'notes' => $this->notes,
            'details' => array_map(fn ($detail) => $detail->toArray(), $this->details),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Validasi argument $detail.
     *
     * @return SaleDetailEntity[]
     */
    private function validateDetails(array $details): array
    {
        $result = [];

        foreach ($details as $detail) {
            if ($detail instanceof SaleDetailEntity) {
                $result[] = $detail;

                continue;
            }

            throw new InvalidArgumentException(
                sprintf('Agumen detail harus merupakan instansiasi dari %s', SaleDetailEntity::class),
            );
        }

        return $result;
    }
}
