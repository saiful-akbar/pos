# Implementasi Fitur Laporan Penjualan (Sales Report)

## 1. Tugas Utama

Implementasikan fitur **Laporan Penjualan (Sales Report)** yang terintegrasi dengan aplikasi POS saat ini.

Fitur ini digunakan untuk membantu pengguna memantau transaksi penjualan, nilai penjualan, produk yang terjual, pelanggan, metode pembayaran, dan performa penjualan berdasarkan periode tertentu.

Sebelum menulis atau mengubah kode, lakukan analisis terhadap proyek untuk memahami implementasi yang sudah ada.

Periksa setidaknya:

1. Struktur direktori dan namespace aplikasi.
2. Implementasi MVC dan pemisahan layer DDD.
3. Pola Repository, Repository Interface, Use Case, Entity, DTO, dan Value Object.
4. Fitur laporan lain yang sudah ada, khususnya laporan pembelian jika tersedia.
5. Controller, route, middleware, permission, dan authorization yang digunakan.
6. Pola validasi menggunakan Form Request atau mekanisme yang sudah digunakan proyek.
7. Pola respons JSON, error handling, logging, pagination, dan format tanggal.
8. Implementasi DataTables server-side, filter tabel, dan AJAX.
9. Implementasi ekspor Excel menggunakan Maatwebsite Excel 4.0.
10. Implementasi ekspor PDF menggunakan Barryvdh Laravel DomPDF.
11. Pola tampilan Blade, layout, komponen, modal, tombol, dan notifikasi yang sudah digunakan.
12. Use Case dan Repository penjualan yang telah tersedia apabila relevan.

Jangan langsung membuat implementasi baru sebelum memahami pola proyek yang ada. Gunakan kembali komponen dan mekanisme yang sudah tersedia jika sesuai.

## 2. Struktur Database

Fitur ini menggunakan tabel berikut:

### Tabel `sales`

Kolom utama:

- `id`
- `user_id`
- `customer_id` — nullable
- `invoice_number`
- `transaction_date`
- `total_product`
- `total_quantity`
- `total_amount`
- `discount`
- `grand_total`
- `paid_amount`
- `change_amount`
- `payment_method` — enum `cash`, `qris`, `debit`, `credit`
- `notes` — nullable
- `created_at`
- `updated_at`
- `deleted_at`

Relasi:

- `user_id` mengacu pada pengguna atau kasir.
- `customer_id` mengacu pada pelanggan.

### Tabel `sale_details`

Kolom utama:

- `id`
- `sale_id`
- `product_id`
- `unit_id`
- `multiples_quantity`
- `purchase_price`
- `selling_price`
- `quantity`
- `subtotal`
- `discount`
- `grand_total`
- `created_at`
- `updated_at`
- `deleted_at`

Relasi:

- `sale_id` mengacu pada transaksi penjualan.
- `product_id` mengacu pada produk.
- `unit_id` mengacu pada satuan produk.

### Tabel `customers`

Kolom relevan:

- `id`
- `name`
- `phone_number`
- `address`
- `discount`
- `is_active`
- `deleted_at`

### Tabel `products`

Kolom relevan:

- `id`
- `category_id`
- `code`
- `name`
- `minimum_stock`
- `current_stock`
- `discount`
- `is_active`
- `deleted_at`

### Tabel `categories`

Kolom relevan:

- `id`
- `code`
- `name`
- `is_active`
- `deleted_at`

### Tabel `units`

Kolom relevan:

- `id`
- `code`
- `name`
- `is_active`
- `deleted_at`

Gunakan definisi database aktual dan model yang ada sebagai sumber kebenaran. Periksa migration, relasi Eloquent, cast, accessor, dan implementasi transaksi yang sudah tersedia sebelum menentukan cara mengambil atau menghitung data.

## 3. Kebutuhan Halaman Laporan Penjualan

Buat halaman laporan penjualan terpisah dari halaman transaksi penjualan.

Halaman minimal memiliki:

- Judul sesuai pola aplikasi.
- Filter periode transaksi.
- Filter pelanggan.
- Filter kasir atau pengguna.
- Filter metode pembayaran.
- Pencarian nomor invoice.
- Filter kategori produk.
- Filter produk.
- Tombol pencarian atau penerapan filter sesuai pola aplikasi.
- Tombol reset filter.
- Ringkasan KPI.
- Tabel laporan penjualan dengan pagination server-side.
- Aksi untuk melihat detail transaksi.
- Ekspor Excel.
- Ekspor PDF jika sesuai dengan pola dan fitur ekspor yang sudah tersedia.

Seluruh elemen UI harus mengikuti desain dan komponen yang sudah digunakan proyek. Jangan membuat desain baru yang tidak konsisten dengan halaman lain.

## 4. KPI atau Ringkasan Penjualan

Tampilkan KPI berikut:

### A. Total Transaksi

Jumlah transaksi penjualan yang sesuai dengan filter aktif.

Gunakan jumlah transaksi yang unik, bukan jumlah baris detail penjualan.

### B. Total Penjualan

Jumlah nilai penjualan berdasarkan `sales.grand_total`.

Gunakan istilah yang konsisten dengan definisi nilai transaksi di aplikasi. Pastikan transaksi tidak dihitung ganda akibat join dengan `sale_details`.

### C. Total Produk Terjual

Jumlah kuantitas barang terjual berdasarkan data detail penjualan.

Periksa arti kolom `total_quantity`, `quantity`, dan `multiples_quantity` serta implementasi konversi satuan yang sudah digunakan aplikasi. Jangan menjumlahkan kuantitas dari satuan yang berbeda seolah-olah semuanya memiliki satuan dasar yang sama.

### D. Total Diskon

Tampilkan total diskon transaksi berdasarkan `sales.discount`, jika definisi kolom tersebut dalam implementasi transaksi memang merupakan nominal diskon yang dapat dijumlahkan.

Periksa pula penggunaan `sale_details.discount` dan pastikan diskon detail tidak ditambahkan lagi ke diskon transaksi apabila nilainya sudah tercakup dalam total transaksi.

## 5. Filter Laporan

Implementasikan filter berikut:

### Periode Transaksi

- Tanggal mulai.
- Tanggal akhir.
- Menggunakan kolom `sales.transaction_date`.
- Validasi bahwa tanggal mulai tidak lebih besar daripada tanggal akhir.
- Terapkan filter tanggal secara konsisten pada tabel, KPI, detail laporan, dan ekspor.

### Pelanggan

- Filter berdasarkan `sales.customer_id`.
- Sediakan pilihan semua pelanggan.
- Transaksi tanpa pelanggan harus tetap dapat ditampilkan ketika tidak ada filter pelanggan yang dipilih.

### Kasir atau Pengguna

- Filter berdasarkan `sales.user_id`.
- Gunakan sumber data pengguna yang sudah tersedia di proyek.

### Metode Pembayaran

Gunakan nilai enum yang benar-benar tersedia:

- Cash
- QRIS
- Debit
- Credit

Label yang ditampilkan dapat menggunakan format UI yang sudah berlaku di aplikasi.

### Nomor Invoice

- Pencarian berdasarkan `sales.invoice_number`.
- Gunakan pencarian yang aman dan sesuai pola query proyek.

### Kategori Produk

- Filter berdasarkan `products.category_id`.
- Gunakan relasi `sale_details.product_id` ke `products`, kemudian ke `categories`.

### Produk

- Filter berdasarkan `sale_details.product_id`.
- Sediakan pencarian atau dropdown sesuai pola UI yang sudah digunakan.

Untuk filter kategori dan produk, gunakan pendekatan query yang tidak menyebabkan satu transaksi muncul berulang kali. Pertimbangkan `whereHas`, `EXISTS`, atau strategi lain yang sesuai dengan arsitektur query proyek.

Jangan menerapkan filter kategori atau produk hanya pada tabel utama `sales`, karena kedua atribut tersebut berasal dari detail penjualan.

## 6. Tabel Laporan Penjualan

Tabel utama menampilkan satu baris untuk setiap transaksi penjualan.

Kolom yang disarankan:

1. Nomor.
2. Tanggal transaksi.
3. Nomor invoice.
4. Pelanggan.
5. Total jenis produk.
6. Total kuantitas.
7. Total harga sebelum diskon (`total_amount`).
8. Diskon.
9. Grand total.
10. Jumlah pembayaran (`paid_amount`).
11. Kembalian (`change_amount`).
12. Metode pembayaran.
13. Kasir.
14. Aksi.

Ketentuan:

- Pastikan arti `total_product` sudah diperiksa. Jangan langsung menganggapnya sebagai jumlah baris detail atau total unit fisik.
- Gunakan `sales.grand_total` sebagai nilai total transaksi sesuai definisi bisnis yang ada.
- Transaksi tanpa pelanggan harus menampilkan label yang sesuai, misalnya "Umum", hanya jika itu konsisten dengan aturan bisnis proyek.
- Gunakan format mata uang Rupiah dan format tanggal yang berlaku pada aplikasi.
- Terapkan sorting, searching, pagination, dan filter server-side menggunakan DataTables sesuai implementasi proyek.
- Jangan memuat seluruh transaksi ke browser hanya untuk melakukan pagination.
- Gunakan eager loading atau optimasi query yang sesuai untuk mencegah N+1 query.
- Sediakan aksi melihat detail transaksi menggunakan halaman atau modal yang sesuai dengan pola aplikasi.
- Jika pengguna membuka detail transaksi, tampilkan item produk, kode produk, satuan, harga jual, kuantitas, subtotal, diskon, dan grand total sesuai data yang tersedia.

## 7. Laporan Analitis Tambahan

Jika struktur aplikasi dan waktu implementasi memungkinkan, tambahkan bagian analitis yang menggunakan filter periode yang sama.

### A. Tren Penjualan

Tampilkan nilai penjualan berdasarkan hari, minggu, atau bulan sesuai rentang tanggal.

Gunakan `transaction_date` dan `grand_total`.

Pastikan pengelompokan tanggal mengikuti periode yang dipilih. Hindari menampilkan tanggal tanpa transaksi sebagai nilai yang tidak valid.

### B. Penjualan Berdasarkan Metode Pembayaran

Tampilkan jumlah transaksi dan total nilai penjualan per `payment_method`.

Pastikan agregasi tidak menggandakan nilai akibat join dengan detail penjualan.

### C. Produk Terlaris

Tampilkan produk berdasarkan kuantitas terjual atau nilai penjualan.

Gunakan `sale_details` dan relasi produk. Jelaskan metrik yang digunakan untuk menentukan peringkat.

Perhatikan konversi satuan. Jangan membandingkan kuantitas dari satuan berbeda tanpa normalisasi yang benar.

### D. Penjualan Berdasarkan Kategori

Tampilkan nilai penjualan per kategori berdasarkan detail penjualan.

Pastikan satuan penjualan dan perhitungan diskon konsisten dengan aturan transaksi yang ada.

### E. Penjualan Berdasarkan Pelanggan atau Kasir

Tampilkan ringkasan jumlah transaksi dan nilai penjualan berdasarkan pelanggan atau kasir sesuai kebutuhan laporan.

Bagian analitis tambahan tidak boleh menghambat penyelesaian halaman utama. Prioritaskan filter, KPI, tabel, detail transaksi, dan ekspor terlebih dahulu.

## 8. Perhitungan Laba Kotor

Laba kotor dapat ditambahkan jika implementasi bisnis dan perhitungan HPP sudah dapat diverifikasi dari kode yang ada.

Tabel `sale_details` memiliki `purchase_price`, sehingga periksa apakah kolom tersebut merupakan snapshot harga pokok per unit pada saat transaksi, dan apakah nilainya menggunakan satuan yang sama dengan `quantity`.

Jika aturan tersebut terverifikasi, pertimbangkan:

- HPP penjualan = jumlah `purchase_price × quantity`, setelah penyesuaian satuan jika diperlukan.
- Laba kotor = total nilai penjualan bersih detail dikurangi HPP.
- Margin laba kotor = laba kotor dibagi penjualan bersih, dikalikan 100%, jika penjualan bersih lebih besar dari nol.

Sebelum menerapkan rumus, verifikasi:

1. Apakah `purchase_price` disimpan per unit penjualan atau per satuan dasar.
2. Apakah `quantity` dan `multiples_quantity` memiliki hubungan konversi tertentu.
3. Apakah `sale_details.grand_total` sudah memperhitungkan diskon detail.
4. Apakah diskon transaksi di `sales.discount` perlu dialokasikan ke detail untuk mendapatkan penjualan bersih yang tepat.
5. Apakah transaksi menyimpan harga pokok historis atau hanya referensi ke harga produk saat ini.

Jangan menggunakan harga beli produk saat ini untuk menghitung laba historis jika snapshot harga pokok tersedia pada detail transaksi.

Jika rumus yang benar belum dapat dipastikan, jangan menampilkan KPI laba sebagai angka yang seolah-olah akurat. Catat ketidakpastian tersebut dan laporkan bagian yang memerlukan konfirmasi.

Apabila laba kotor diimplementasikan, beri label "Estimasi Laba Kotor" jika data belum mencakup biaya operasional, pajak, retur, atau penyesuaian lain.

## 9. Ekspor Excel

Implementasikan ekspor Excel menggunakan Maatwebsite Excel 4.0 yang telah terpasang.

Ketentuan:

- Gunakan filter yang sama dengan halaman laporan.
- Kolom ekspor harus sesuai dengan data yang ditampilkan pada laporan.
- Gunakan query yang efisien.
- Hindari memuat dataset yang besar seluruhnya ke memori jika library menyediakan mekanisme yang lebih sesuai.
- Terapkan format tanggal dan mata uang yang layak.
- Gunakan nama file yang informatif, misalnya `laporan-penjualan-YYYYMMDD-YYYYMMDD.xlsx`.
- Validasi seluruh parameter filter.
- Terapkan authorization yang sama dengan halaman laporan.
- Pastikan implementasi sesuai dengan API Maatwebsite Excel 4.0 yang benar-benar tersedia di proyek. Jangan menyalin implementasi versi lama tanpa memeriksa kompatibilitas.

Ekspor harus menghasilkan data sesuai filter yang aktif, bukan sekadar mengekspor halaman DataTables yang sedang terlihat.

## 10. Ekspor PDF

Jika proyek sudah menggunakan ekspor PDF atau fitur ini dapat diimplementasikan secara konsisten, tambahkan ekspor PDF menggunakan Barryvdh Laravel DomPDF.

Ketentuan:

- Gunakan Blade view khusus untuk PDF jika sesuai pola proyek.
- Gunakan layout tabel yang kompatibel dengan DomPDF.
- Tampilkan judul laporan, periode, waktu pembuatan, filter yang relevan, ringkasan, dan tabel transaksi.
- Terapkan filter yang sama dengan halaman laporan.
- Gunakan format Rupiah dan tanggal yang sesuai.
- Atur ukuran kertas dan orientasi berdasarkan kebutuhan jumlah kolom.
- Gunakan nama file yang informatif.
- Ikuti pola respons PDF yang sudah digunakan proyek, baik inline maupun download.
- Jangan menggunakan Browsershot, Puppeteer, Node.js, npm, atau pendekatan lain yang membutuhkan runtime Node.js.
- Jangan menambahkan dependency baru jika library yang sudah terpasang mencukupi.

## 11. Arsitektur dan Struktur Kode

Ikuti arsitektur DDD dan pola Repository/Use Case yang sudah digunakan.

Prinsip implementasi:

1. Controller bertanggung jawab untuk menerima request, validasi melalui mekanisme yang berlaku, memanggil Use Case, dan menghasilkan respons atau view.
2. Business logic dan perhitungan laporan ditempatkan di layer yang tepat, bukan ditumpuk di Controller.
3. Gunakan Repository Interface pada domain/application boundary sesuai pola proyek.
4. Gunakan implementasi Repository yang sudah ada apabila sesuai. Tambahkan method baru hanya jika memang diperlukan.
5. Gunakan Entity, DTO, dan Value Object yang relevan.
6. Jangan menambahkan Entity atau DTO baru jika tidak diperlukan atau bertentangan dengan konvensi proyek.
7. Gunakan dependency injection atau `app(UseCase::class)` sesuai pola yang benar-benar digunakan proyek.
8. Jangan melewati Use Case dan Repository hanya demi mempercepat implementasi, kecuali proyek memang memiliki pola query/report khusus yang telah digunakan.
9. Gunakan Form Request jika itu konvensi proyek.
10. Gunakan nama class, method, route, namespace, dan file yang konsisten dengan fitur laporan lain.
11. Pertahankan kontrak respons JSON dan struktur metadata yang digunakan aplikasi.
12. Jangan mengganti atau merusak implementasi transaksi penjualan yang sudah berjalan.

Jika perlu membuat query khusus untuk laporan agregat, gunakan pendekatan yang sesuai dengan pemisahan tanggung jawab di proyek. Hindari memaksakan seluruh data laporan menjadi Entity transaksi apabila agregat laporan memiliki struktur yang berbeda.

## 12. Route, Permission, dan Keamanan

- Tambahkan route laporan penjualan mengikuti konvensi route proyek.
- Periksa route transaksi penjualan yang sudah ada untuk menentukan penamaan dan pengelompokan route yang konsisten.
- Terapkan middleware autentikasi, module permission, policy, atau authorization yang sesuai dengan sistem yang sudah digunakan.
- Jangan mengasumsikan nama permission baru tanpa memeriksa permission yang tersedia.
- Jika diperlukan permission baru, ikuti mekanisme registrasi permission dan seeder yang digunakan proyek.
- Validasi semua input filter.
- Hindari raw SQL yang menggabungkan input pengguna secara langsung.
- Pastikan detail transaksi dan ekspor hanya dapat diakses oleh pengguna yang berwenang.
- Jangan mengekspos data pelanggan atau pengguna melebihi kebutuhan laporan.

## 13. Penanganan Error dan Logging

Ikuti mekanisme error handling dan logging yang sudah digunakan aplikasi.

Khusus Repository, apabila implementasi proyek menggunakan pola `try/catch`, panggil mekanisme `errorLog(__METHOD__, $e->getMessage())` pada penanganan error dan lemparkan kembali exception sesuai pola yang berlaku.

Jangan menelan exception secara diam-diam atau mengembalikan nilai kosong yang dapat membuat laporan tampak valid padahal query gagal.

Tampilkan pesan kesalahan yang sesuai untuk pengguna dan simpan detail teknis melalui mekanisme logging proyek.

## 14. Soft Delete dan Integritas Agregasi

Perhatikan kolom `deleted_at` pada tabel transaksi dan detail transaksi.

- Jangan memasukkan transaksi yang telah dihapus secara lunak ke laporan standar, kecuali aplikasi secara eksplisit memiliki kebutuhan laporan arsip.
- Periksa global scope dan implementasi SoftDeletes pada model.
- Pastikan detail transaksi yang dihapus tidak ikut terhitung jika sesuai dengan aturan bisnis aplikasi.
- Pastikan join dengan pelanggan, produk, kategori, atau satuan tidak menghilangkan transaksi yang valid tanpa alasan.
- Pastikan transaksi dengan `customer_id` null tetap dapat muncul.
- Jangan menggandakan `grand_total` transaksi ketika menggabungkan `sales` dengan `sale_details`.
- Untuk KPI dan agregasi, gunakan query yang sesuai agar total tidak berubah akibat multiplicity relasi.

## 15. Batasan dan Asumsi Bisnis

Jangan membuat asumsi berikut tanpa bukti dari implementasi:

- `total_product` selalu berarti jumlah produk unik.
- `total_quantity` selalu setara dengan jumlah unit fisik.
- `paid_amount` selalu berarti uang bersih yang diterima.
- `sales.discount` dan `sale_details.discount` selalu memiliki jenis dan satuan yang sama.
- Semua nilai `purchase_price` menggunakan satuan yang sama dengan `quantity`.
- `customer_id` null selalu berarti pelanggan umum.
- Penjualan bersih sudah memperhitungkan retur.
- Laba kotor merupakan laba bersih setelah seluruh biaya.

Tidak ada tabel retur yang tercantum dalam struktur yang diberikan. Karena itu, jangan menyatakan laporan sudah dikurangi retur kecuali ditemukan mekanisme retur lain dalam proyek.

Tidak ada kolom status pembayaran atau tabel riwayat pembayaran dalam struktur yang diberikan. Jangan membuat laporan piutang atau status lunas berdasarkan tebakan. Jika aplikasi memiliki mekanisme tambahan yang relevan, gunakan hanya setelah diverifikasi.

Jika ada definisi bisnis yang belum jelas, periksa kode transaksi, migration, model, dan Use Case yang ada terlebih dahulu. Jika tetap tidak dapat dipastikan, catat temuan tersebut dan ajukan pertanyaan yang spesifik sebelum menerapkan perhitungan yang berisiko salah.

## 16. Testing dan Verifikasi

Buat atau perbarui test sesuai framework dan pola pengujian proyek.

Minimal verifikasi:

1. Laporan menampilkan transaksi sesuai periode.
2. Filter pelanggan berfungsi.
3. Filter kasir berfungsi.
4. Filter metode pembayaran berfungsi.
5. Pencarian invoice berfungsi.
6. Filter kategori dan produk berfungsi tanpa menggandakan transaksi.
7. KPI konsisten dengan hasil query.
8. Transaksi tanpa pelanggan ditangani dengan benar.
9. Soft-deleted records tidak masuk dalam laporan standar.
10. Pagination dan sorting server-side berfungsi.
11. Detail transaksi dapat dibuka oleh pengguna yang berwenang.
12. Ekspor Excel menggunakan filter yang sama dengan halaman.
13. Ekspor PDF, jika diimplementasikan, menghasilkan PDF valid.
14. Perhitungan laba kotor, jika diimplementasikan, sesuai dengan aturan satuan dan diskon yang sudah diverifikasi.
15. Pengguna tanpa permission tidak dapat mengakses halaman atau ekspor.
16. Rentang tanggal yang tidak valid ditolak dengan benar.
17. Dataset kosong ditangani dengan benar.
18. Query agregat tidak menghasilkan nilai ganda akibat join.

Jalankan test yang relevan dan periksa hasilnya. Jangan mengklaim pengujian berhasil jika belum benar-benar dijalankan.

## 17. Urutan Pengerjaan

Lakukan pengerjaan secara bertahap:

**Tahap 1 — Audit**

- Pelajari arsitektur proyek.
- Temukan implementasi laporan pembelian atau laporan lain sebagai referensi.
- Periksa model, relasi, Use Case, Repository, route, permission, dan library ekspor.
- Laporkan temuan dan rencana implementasi singkat.

**Tahap 2 — Backend**

- Implementasikan query laporan.
- Implementasikan filter.
- Implementasikan KPI.
- Implementasikan pagination server-side.
- Implementasikan detail transaksi dan ekspor.

**Tahap 3 — Frontend**

- Buat halaman Blade.
- Tambahkan filter dan KPI.
- Implementasikan DataTables dan AJAX sesuai pola proyek.
- Tambahkan aksi detail dan ekspor.

**Tahap 4 — Analisis Tambahan**

- Tambahkan tren penjualan dan ringkasan berdasarkan metode pembayaran, produk, kategori, pelanggan, atau kasir jika sesuai dengan struktur proyek.
- Tambahkan estimasi laba kotor hanya jika rumusnya telah terverifikasi.

**Tahap 5 — Verifikasi**

- Jalankan test dan pemeriksaan kode.
- Perbaiki error yang ditemukan.
- Periksa route, permission, query, dan hasil ekspor.
- Pastikan fitur transaksi penjualan yang sudah ada tetap berfungsi.

## 18. Aturan Pelaksanaan

- Kerjakan langsung pada codebase yang sedang dibuka di Claude Code.
- Jangan hanya memberikan contoh kode atau tutorial.
- Jangan membuat ulang struktur proyek tanpa alasan kuat.
- Jangan mengganti library atau menambahkan dependency yang tidak diperlukan.
- Jangan mengubah skema database kecuali benar-benar diperlukan dan disertai alasan.
- Jangan menambahkan kolom atau tabel untuk mengatasi asumsi yang belum terverifikasi.
- Jangan mengubah fitur transaksi penjualan yang sudah ada jika tidak diperlukan.
- Gunakan kode yang siap dipelihara, konsisten, dan sesuai konvensi proyek.
- Jika terdapat beberapa alternatif implementasi, pilih yang paling konsisten dengan pola proyek.
- Jika ada konflik antara instruksi ini dan implementasi aktual, periksa konteks proyek terlebih dahulu dan jelaskan konflik tersebut.
- Jangan menganggap fitur selesai hanya karena halaman dapat dibuka; pastikan perhitungan, filter, otorisasi, dan ekspor benar.

## 19. Laporan Akhir Implementasi

Setelah selesai, berikan ringkasan yang mencakup:

1. Fitur yang berhasil diimplementasikan.
2. File yang dibuat dan diubah beserta tanggung jawabnya.
3. Route yang ditambahkan.
4. Use Case dan Repository yang dibuat atau diubah.
5. Filter dan KPI yang tersedia.
6. Mekanisme ekspor yang berhasil diimplementasikan.
7. Test yang dijalankan dan hasilnya.
8. Asumsi bisnis yang berhasil diverifikasi.
9. Hal yang belum diimplementasikan beserta alasannya.
10. Potensi perbaikan atau pengembangan lanjutan.

Mulai dengan mengaudit codebase dan menemukan pola implementasi yang paling relevan. Setelah itu, lanjutkan implementasi berdasarkan hasil audit tersebut.
