<?php

declare(strict_types=1);

namespace App\Application\Sale\DTOs;

use App\Base\DTO;
use App\Domain\Purchase\Enums\PaymentMethodEnum;
use DateTimeInterface;
use Override;

class CreateSaleDTO extends DTO
{
    /**
     * ID user yang
     */
    public readonly string $userId;

    /**
     * ID customer
     */
    public readonly ?string $customerId;

    /**
     * Nomor faktur
     */
    public readonly string $invoiceNumber;

    /**
     * Thun transaksi
     */
    public readonly int $transactionYear;

    /**
     * Bulan transaksi
     */
    public readonly int $transactionMonth;

    /**
     * Tanggal transaksi
     */
    public readonly DateTimeInterface $transactionDate;

    /**
     * Total qty
     */
    public readonly int|float $totalQuantity;

    /**
     * Total harga
     */
    public readonly int|float $totalAmount;

    /**
     * Pembayaran
     */
    public readonly int|float $paidAmount;

    /**
     * Kembalian
     */
    public readonly int|float $changeAmount;

    /**
     * Metode pembayaran
     */
    public readonly PaymentMethodEnum $paymentMethod;

    /**
     * Catatan
     */
    public readonly ?string $notes;

    /**
     * Create a new class instance.
     */
    public function __construct(
        string $userId,
        ?string $customerId,
        string $invoiceNumber,
        DateTimeInterface $transactionDate,
        int|float $totalQuantity,
        int|float $totalAmount,
        int|float $paidAmount,
        int|float $changeAmount,
        PaymentMethodEnum $paymentMethod,
        ?string $notes,
    ) {
        $this->userId = $userId;
        $this->customerId = $customerId;
        $this->invoiceNumber = $invoiceNumber;
        $this->transactionYear = (int) $transactionDate->format('Y');
        $this->transactionMonth = (int) $transactionDate->format('m');
        $this->transactionDate = $transactionDate;
        $this->totalQuantity = $totalQuantity;
        $this->totalAmount = $totalAmount;
        $this->paidAmount = $paidAmount;
        $this->changeAmount = $changeAmount;
        $this->paymentMethod = $paymentMethod;
        $this->notes = $notes;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'customer_id' => $this->customerId,
            'invoice_number' => $this->invoiceNumber,
            'transaction_year' => $this->transactionYear,
            'transaction_month' => $this->transactionMonth,
            'transaction_date' => $this->transactionDate->format('Y-m-d'),
            'total_quantity' => $this->totalQuantity,
            'total_amount' => $this->totalAmount,
            'paid_amount' => $this->paidAmount,
            'change_amount' => $this->changeAmount,
            'payment_method' => $this->paymentMethod->value,
            'notes' => $this->notes,

        ];
    }
}
