# Point Of Sales

# 1. Product Requirements Document (PRD)

POS & Inventory Warung Kelontong UMKM

Version: 1.0
Status: Draft
Target Platform: Web / Desktop / Mobile
Backend: Laravel 12
Database: MySQL 8
Frontend: Flutter
Target User: Warung kelontong / toko sembako skala UMKM

## 1.1. Product Overview

Aplikasi ini merupakan sistem Point of Sales (POS) dan Inventory Management yang ditujukan untuk warung kelontong dan toko sembako skala UMKM.

Sistem digunakan untuk:

- Melakukan transaksi penjualan dengan cepat.
- Mengelola data produk.
- Mengelola stok barang.
- Mencatat pembelian dari supplier.
- Mencatat retur barang.
- Melakukan stock opname.
- Mengetahui kondisi stok.
- Melihat omzet dan keuntungan.
- Mengelola kas sederhana.
- Menyediakan histori transaksi dan aktivitas inventory.

Sistem dirancang dengan prinsip simple, fast, dan mudah digunakan, karena pengguna utama adalah pemilik warung dan kasir yang membutuhkan proses transaksi cepat.

## 1.2. Problem Statement

Warung kelontong UMKM umumnya masih melakukan beberapa aktivitas secara manual, seperti:

- Mencatat penjualan menggunakan buku.
- Menghitung stok secara manual.
- Tidak mengetahui stok barang secara real-time.
- Kesulitan mengetahui barang yang mulai habis.
- Kesulitan mengetahui barang yang paling laku.
- Tidak mengetahui keuntungan secara akurat.
- Kesulitan melacak perubahan stok.
- Kesulitan mengetahui histori pembelian barang.
- Kesulitan melakukan rekonsiliasi uang kas.

Aplikasi ini dibuat untuk mengurangi pekerjaan manual tersebut dan menyediakan informasi operasional secara terpusat.

## 1.3. Product Goals

### 1.3.1. Primary Goals

1. Mempercepat proses transaksi penjualan.
2. Menyediakan informasi stok secara real-time.
3. Mencatat seluruh pergerakan stok.
4. Memudahkan pencatatan pembelian barang.
5. Membantu pemilik mengetahui omzet dan keuntungan.
6. Mengurangi kesalahan pencatatan stok.
7. Menyediakan histori transaksi yang mudah ditelusuri.

### 1.3.2. Secondary Goals

1. Menyediakan laporan sederhana untuk pemilik.
2. Membantu mengetahui produk yang paling laku.
3. Memberikan peringatan stok minimum.
4. Menyediakan pencatatan kas sederhana.
5. Menyediakan fondasi untuk pengembangan multi-outlet di masa depan.

## 1.4. Non-Goals

Fitur berikut tidak termasuk dalam MVP:

- Multi outlet.
- Multi warehouse.
- Loyalty point.
- Membership.
- Voucher.
- Gift card.
- Accounting penuh.
- Payroll.
- Marketplace integration.
- E-commerce.
- Batch/lot management.
- Serial number.
- Purchase Order.
- Complex approval workflow.
- Advanced promotion engine.
- Complex accounts payable.
- Complex accounts receivable.
- Advanced financial accounting.

Fitur tersebut dapat dipertimbangkan pada versi berikutnya.

## 1.5. Target Users

### 1.5.1. Owner / Admin

Pemilik warung yang memiliki akses penuh terhadap sistem.

Hak akses:

- Mengelola produk.
- Mengelola kategori.
- Mengelola satuan.
- Mengelola supplier.
- Mengelola customer.
- Melihat transaksi.
- Mengelola inventory.
- Melakukan stock opname.
- Melihat laporan.
- Mengelola user.
- Mengelola pengaturan.

### 1.5.2. Cashier

Pengguna yang bertugas melakukan transaksi penjualan.

Hak akses:

- Melihat produk.
- Melakukan transaksi.
- Melakukan pembayaran.
- Melihat transaksi yang dibuat sendiri.
- Mencetak struk.

Kasir tidak diperbolehkan melakukan perubahan data penting seperti:

- Harga produk.
- Stok manual.
- Penghapusan transaksi.
- Pengelolaan user.

## 1.7. Functional Requirements

### 1.7.1 Authentication

#### FR-AUTH-001 — Login

User dapat login menggunakan:

- Username
- Password

#### FR-AUTH-002 — Logout

User dapat logout dari aplikasi.

#### FR-AUTH-003 — Role

Sistem mendukung minimal:

- admin
- cashier

#### FR-AUTH-004 — Active User

User yang berstatus inactive tidak dapat login.

### 1.7.2. Product Management

#### FR-PROD-001 — Product List

Sistem harus menampilkan daftar produk dengan:

- Kode produk.
- Barcode.
- Nama.
- Kategori.
- Satuan.
- Harga beli.
- Harga jual.
- Stok.
- Minimum stock.
- Status.

#### FR-PROD-002 — Create Product

Admin dapat membuat produk baru.

Data minimal:

- code
- barcode
- name
- category_id
- unit_id
- purchase_price
- selling_price
- minimum_stock
- is_active

#### FR-PROD-003 — Edit Product

Admin dapat mengubah informasi produk.
Perubahan harga tidak boleh mengubah harga pada transaksi lama.

#### FR-PROD-004 — Product Search

Produk dapat dicari berdasarkan:

- Nama.
- Kode.
- Barcode.

#### FR-PROD-005 — Product Status

Produk dapat diaktifkan/nonaktifkan tanpa menghapus histori transaksi.

**Hard delete produk tidak diperbolehkan jika produk sudah digunakan dalam transaksi.**

### 1.7.3. Category Management

Admin dapat:

- Membuat kategori.
- Mengubah kategori.
- Mengaktifkan/nonaktifkan kategori.
- Melihat jumlah produk dalam kategori.

Contoh:

```
Sembako
Minuman
Mie Instan
Snack
Sabun
Deterjen
Rokok
Lainnya
```

### 1.7.4. Unit Management

Sistem menyediakan master satuan.

Contoh:

```
PCS
KG
LITER
BOTOL
DUS
SACHET
```

**Unit yang sudah digunakan oleh produk tidak boleh dihapus secara fisik.**

### 1.7.5. POS / Sales

#### FR-SALE-001 — Create Sale

Kasir dapat membuat transaksi baru.

Flow:

```
Cari / Scan Produk
      ↓
Tambah ke Cart
      ↓
Atur Quantity
      ↓
Hitung Total
      ↓
Pembayaran
      ↓
Transaksi Selesai
```

#### FR-SALE-002 — Add Product

Kasir dapat menambahkan produk melalui:

- Barcode scanner.
- Pencarian nama.
- Kode produk.

#### FR-SALE-003 — Quantity

Kasir dapat mengubah quantity produk.
Sistem harus memvalidasi bahwa quantity tidak melebihi stok yang tersedia, kecuali konfigurasi negative stock diaktifkan.
Untuk MVP, negative stock sebaiknya disabled.

#### FR-SALE-004 — Price Snapshot

Saat transaksi dibuat, sistem harus menyimpan:

```
selling_price
cost_price
```

ke `sale_details`.

Perubahan harga produk setelah transaksi tidak boleh memengaruhi transaksi lama.

FR-SALE-005 — Discount

Sistem mendukung:

- Diskon nominal.
- Diskon persentase.

Untuk MVP, diskon dapat diterapkan pada level transaksi.

#### FR-SALE-006 — Payment

Minimal mendukung:

- Cash
- QRIS
- Transfer

#### FR-SALE-007 — Cash Payment

Jika pembayaran cash:

```
Paid Amount >= Grand Total
```

Sistem menghitung:

```
Change = Paid Amount - Grand Total
```

#### FR-SALE-008 — Transaction Number

Sistem menghasilkan nomor invoice otomatis.

Format default:

```
INVYYMM-NNNNN
```

Contoh:

```
INV2608-00001
INV2608-00002
INV2608-00003
```

Nomor invoice harus unique.

Sistem tidak boleh menggunakan pola:

```sql
SELECT MAX(invoice_number) + 1
```

untuk menentukan nomor berikutnya karena dapat menyebabkan race condition ketika beberapa kasir melakukan transaksi secara bersamaan.

#### FR-SALE-009 — Complete Transaction

Ketika transaksi berhasil:

- Sales disimpan.
- Sale detail disimpan.
- Payment disimpan.
- Stock movement dibuat.
- Current stock dikurangi.
- Transaction status menjadi completed.

Seluruh proses harus dilakukan dalam database transaction.
Jika salah satu proses gagal, seluruh transaksi harus di-rollback.

#### FR-SALE-010 — Receipt

Sistem dapat menampilkan dan/atau mencetak struk.

Informasi minimal:

```
Nama Warung
Invoice Number
Tanggal
Kasir

Produk
Qty
Harga
Subtotal

Subtotal
Discount
Grand Total
Payment
Change
```

### 1.7.6. Sales History

Admin dapat melihat seluruh transaksi.

Filter:

- Invoice number.
- Tanggal.
- Kasir.
- Payment method.
- Status.

Detail transaksi menampilkan:

- Invoice.
- Kasir.
- Produk.
- Quantity.
- Harga.
- Discount.
- Total.
- Payment.
- Waktu transaksi.

### 1.7.7. Void Transaction

Admin dapat membatalkan transaksi.

Saat transaksi di-void:

```
sales.status = void
```

Stok harus dikembalikan melalui stock movement baru, bukan menghapus movement sebelumnya.

Contoh:

```
Sale
Stock -5

Void
Stock +5
```

### 1.7.8. Inventory

#### FR-INV-001 — Current Stock

Sistem menampilkan stok saat ini.

```
Product
Current Stock
Minimum Stock
Status
```

Status:

```
NORMAL
LOW_STOCK
OUT_OF_STOCK
```

#### FR-INV-002 — Stock In

Stok bertambah ketika:

- Pembelian.
- Retur penjualan.
- Adjustment positif.
- Stock opname positif.

#### FR-INV-003 — Stock Out

Stok berkurang ketika:

- Penjualan.
- Retur pembelian.
- Adjustment negatif.
- Stock opname negatif.

#### FR-INV-004 — Stock Movement

Semua perubahan stok wajib memiliki histori.

Jenis:

```
purchase
sale
sale_return
purchase_return
adjustment
stock_opname
```

Setiap movement minimal menyimpan:

```
product_id
type
reference_type
reference_id
qty
stock_before
stock_after
user_id
created_at
```

### 1.7.9. Stock Card

User dapat melihat histori stok produk.

Contoh:

```
Date        Reference       In    Out    Balance
------------------------------------------------
01-08-26    Opening        100     0       100
02-08-26    Purchase        50     0       150
03-08-26    Sale             0    10       140
04-08-26    Adjustment       0     2       138
```

Sistem harus dapat menjelaskan perubahan stok berdasarkan movement.

### 1.7.10. Stock Opname

#### FR-OPNAME-001 — Create Opname

Admin dapat membuat stock opname.

#### FR-OPNAME-002 — System Stock

Sistem menyimpan stok sistem saat opname dimulai.

#### FR-OPNAME-003 — Physical Stock

User memasukkan jumlah stok fisik.

#### FR-OPNAME-004 — Difference

Sistem menghitung:

```
difference = physical_stock - system_stock
```

#### FR-OPNAME-005 — Complete Opname

Ketika opname diselesaikan:

```
current_stock = physical_stock
```

dan dibuat stock_movement dengan tipe:

```
stock_opname
```

### 1.7.11. Purchasing

#### FR-PURCHASE-001 — Create Purchase

Admin dapat mencatat pembelian barang.

Data:

```
Supplier
Tanggal
Invoice Supplier
Produk
Qty
Harga Beli
Subtotal
Total
```

#### FR-PURCHASE-002 — Stock Increase

Ketika pembelian disimpan:

```
current_stock += purchased_qty
```

dan dibuat:

```
stock_movement.type = purchase
```

#### FR-PURCHASE-003 — Purchase History

User dapat melihat histori pembelian.

Filter:

- Supplier.
- Tanggal.
- Nomor invoice.

### 1.7.12. Supplier

Admin dapat:

- Create supplier.
- Edit supplier.
- View supplier.
- Activate/deactivate supplier.

Data:

```
name
phone
address
notes
is_active
```

### 1.7.13. Sales Return

Sistem mendukung retur penjualan sederhana.

Flow:

```
Invoice
   ↓
Pilih Product
   ↓
Input Qty Return
   ↓
Confirm
   ↓
Stock +
```

Sistem membuat stock movement:

```
type = sale_return
```

### 1.7.14. Purchase Return

Sistem mendukung retur barang kepada supplier.

Flow:

```
Purchase
   ↓
Pilih Product
   ↓
Input Qty Return
   ↓
Confirm
   ↓
Stock -
```

Movement:

```
type = purchase_return
```

### 1.7.15. Cash Management

Sistem memiliki pencatatan kas sederhana.

Cash In

Contoh:

```
Modal awal
Tambahan kas
Cash Out
```

Contoh:

```
Pengeluaran operasional
Pembelian kebutuhan warung
Biaya lain
```

Setiap transaksi menyimpan:

```
type
amount
description
user_id
transaction_date
```

## 1.8. Database

### 1.8.1. Core Database Tables

```
users

categories
units
products

customers
suppliers

sales
sale_details
payments

purchases
purchase_details

stock_movements

stock_opnames
stock_opname_details

settings
```

Total:

```
15 Table
```

## 1.9. Development Priority

Urutan development yang disarankan:

```
Phase 1
├── Laravel Project
├── Authentication
├── User
└── Basic Settings

        ↓

Phase 2
├── Category
├── Unit
└── Product

        ↓

Phase 3
├── Sales
├── Sale Details
├── Payment
└── Stock Movement

        ↓

Phase 4
├── Purchase
├── Purchase Details
└── Supplier

        ↓

Phase 5
├── Stock Opname
├── Stock Adjustment
└── Stock Card

        ↓

Phase 6
├── Dashboard
├── Sales Report
├── Inventory Report
└── Profit Report

        ↓

Phase 7
├── Customer
├── Sales Return
├── Purchase Return
└── Cash Management

        ↓

Phase 8
├── Receipt Printing
├── Barcode
└── UX Optimization
```

## 1.10. Prinsip Utama Sistem

Ada beberapa prinsip yang sebaiknya dianggap sebagai technical/business rules, bukan sekadar implementasi:

- Setiap transaksi penjualan harus menghasilkan stock movement.
- Setiap pembelian harus menghasilkan stock movement.
- Stock movement tidak dihapus untuk memperbaiki kesalahan.
- Koreksi stok dilakukan melalui movement baru.
- Harga transaksi merupakan snapshot dari harga saat transaksi.
- Transaksi completed tidak boleh di-hard-delete.
- Semua perubahan stok dan transaksi finansial harus atomic.
- Invoice number harus unique dan concurrency-safe.
- Current stock digunakan untuk pembacaan cepat, stock movement digunakan sebagai audit trail.
- MVP mengutamakan kecepatan operasional kasir daripada kompleksitas fitur.
