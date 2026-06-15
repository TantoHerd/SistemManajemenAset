# 📚 DOKUMENTASI USER SIMASET
## Sistem Manajemen Aset IT

**Versi 1.0** | **Terakhir Diupdate: 09 Juni 2024**

---

<div align="center">

![SIMASET Logo](https://via.placeholder.com/150x150?text=SIMASET)

**Sistem Manajemen Aset IT Profesional**

</div>

---

## 📋 DAFTAR ISI

- [1. Pengenalan](#1-pengenalan)
- [2. Login & Dashboard](#2-login--dashboard)
- [3. Manajemen Aset](#3-manajemen-aset)
- [4. Maintenance Aset](#4-maintenance-aset)
- [5. Peminjaman Aset](#5-peminjaman-aset)
- [6. Stock Opname](#6-stock-opname)
- [7. MeCard Generate](#7-mecard-generate)
- [8. User Management](#8-user-management)
- [9. Laporan](#9-laporan)
- [10. FAQ & Troubleshooting](#10-faq--troubleshooting)

---

## 1. PENGENALAN

### 1.1 Tentang SIMASET

**SIMASET (Sistem Manajemen Aset IT)** adalah aplikasi berbasis web yang membantu perusahaan mengelola:

| Modul | Fungsi |
|-------|--------|
| 📦 **Aset** | Mencatat, melacak, dan mengelola seluruh aset IT |
| 🔧 **Maintenance** | Menjadwalkan dan mencatat perbaikan aset |
| 📋 **Peminjaman** | Mengelola peminjaman dan pengembalian aset |
| 📊 **Stock Opname** | Melakukan sensus dan verifikasi aset |
| 🆔 **MeCard** | Membuat kartu nama digital dengan QR Code |
| 📈 **Laporan** | Menghasilkan laporan berbagai format |

### 1.2 Hak Akses User

| Role | Hak Akses | Keterangan |
|------|-----------|-------------|
| 👑 **Super Admin** | ✅ Semua akses | Manajemen penuh termasuk user & backup |
| 🛡️ **Admin** | ✅ Aset, Maintenance, Peminjaman, Laporan | Tanpa manajemen user |
| 🔧 **Technician** | ✅ Maintenance | Hanya mengelola perbaikan aset |
| 👤 **User** | ✅ Peminjaman | Mengajukan peminjaman aset |
| 👁️ **Viewer** | 👁️ Lihat saja | Read only semua data |

### 1.2 Browser yang Didukung

| Browser | Versi Minimal |
|---------|---------------|
| Google Chrome | 90+ |
| Mozilla Firefox | 88+ |
| Microsoft Edge | 90+ |
| Safari | 14+ |

---

## 2. LOGIN & DASHBOARD

### 2.1 Cara Login

```bash
Langkah-langkah:
1. Buka URL aplikasi SIMASET
2. Masukkan Username dan Password
3. Klik tombol "Login"

Tampilan Halaman Login:

┌─────────────────────────────────────┐
│            SIMASET                  │
│                                     │
│   ┌─────────────────────────────┐   │
│   │ 📧 username@company.com     │   │
│   └─────────────────────────────┘   │
│   ┌─────────────────────────────┐   │
│   │ 🔒 •••••••••••••••••••••    │   │
│   └─────────────────────────────┘   │
│                                     │
│   ┌─────────┐    ┌─────────┐       │
│   │ Login   │    │ Lupa PW │       │
│   └─────────┘    └─────────┘       │
└─────────────────────────────────────┘

2.2 Dashboard
Setelah login, Anda akan melihat halaman dashboard dengan informasi:

Widget	Fungsi
📊 Total Aset	Jumlah seluruh aset dalam database
🔧 Aset Maintenance	Aset yang sedang dalam perbaikan
📦 Aset Dipinjam	Aset yang sedang dipinjam
📅 Maintenance Bulan Ini	Jadwal maintenance bulan berjalan
⏰ Peminjaman Aktif	Peminjaman yang belum dikembalikan

2.3 Navigasi Menu

┌─────────────────────────────────────────────────────────────┐
│  🏠 Dashboard                                                │
│  📦 Manajemen Aset                                          │
│     ├── 📋 Daftar Aset                                      │
│     ├── ➕ Tambah Aset                                      │
│     ├── 📥 Import Aset                                     │
│     └── 🏷️ Kategori & Lokasi                               │
│  🔧 Maintenance                                             │
│  📋 Peminjaman                                              │
│  📊 Stock Opname                                            │
│  🆔 MeCard Generate                                        │
│  👥 User Management                                        │
│  📈 Laporan                                                 │
│  ⚙️ Konfigurasi                                            │
└─────────────────────────────────────────────────────────────┘

2.4 Mengubah Password

Langkah-langkah:
1. Klik icon profil di pojok kanan atas
2. Pilih "Profile"
3. Klik "Change Password"
4. Masukkan password lama
5. Masukkan password baru (minimal 8 karakter)
6. Konfirmasi password baru
7. Klik "Save"

3. MANAJEMEN ASET

3.1 Melihat Daftar Aset
Menu: Manajemen Aset → Daftar Aset

Fitur yang tersedia:

Fitur	Fungsi
🔍 Search	Cari berdasarkan nama/kode/seri
🎯 Filter	Filter berdasarkan kategori, lokasi, status
📊 Sortir	Urutkan berdasarkan kolom tertentu
📄 Pagination	10/25/50/100 data per halaman
📎 Export	Export ke Excel/PDF
3.2 Menambah Aset Baru
Menu: Manajemen Aset → Tambah Aset

Form Tambah Aset:

┌─────────────────────────────────────────────────────────────┐
│ TAMBAH ASET BARU                                  [×]       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Kode Aset* : [AST-2024001            ]                     │
│  Nama Aset* : [Laptop Dell Latitude 5420]                   │
│  Serial No  : [CN-0Y8H3K-12345-XXX   ]                     │
│  Merk       : [Dell                   ]                     │
│  Model      : [Latitude 5420          ]                     │
│                                                             │
│  Kategori*  : [Laptop ▼]                                    │
│  Lokasi*    : [Ruang IT - Meja 3 ▼]                         │
│  Status     : [Tersedia ▼]                                  │
│                                                             │
│  Tanggal Beli*   : [01/01/2024]                             │
│  Harga Beli*     : [Rp 15.000.000]                          │
│  Masa Pakai (bln): [36]                                     │
│                                                             │
│  Spesifikasi:                                               │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Processor   : [Intel Core i7-1165G7]                │   │
│  │ RAM         : [16GB DDR4]                           │   │
│  │ Storage     : [512GB SSD]                           │  
│  │ OS          : [Windows 11 Pro]                      │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  Dokumen:                                                   │
│  [+ Upload] faktur_pembelian.pdf (2.3 MB) [×]              │
│  [+ Upload] garansi.pdf (1.1 MB) [×]                       │
│                                                             │
│  Catatan:                                                   │
│  [_______________________________]                          │
│                                                             │
│  ┌─────────┐    ┌─────────┐                               │
│  │ Simpan  │    │ Batal   │                               │
│  └─────────┘    └─────────┘                               │
└─────────────────────────────────────────────────────────────┘

Catatan: Field dengan tanda * wajib diisi.

3.3 Mengedit Aset
Langkah-langkah:
1. Buka "Daftar Aset"
2. Cari aset yang ingin diedit
3. Klik icon ✏️ (Edit) pada baris aset
4. Ubah data yang diperlukan
5. Klik "Update"

3.4 Menghapus Aset
Langkah-langkah:
1. Buka "Daftar Aset"
2. Cari aset yang ingin dihapus
3. Klik icon 🗑️ (Delete)
4. Konfirmasi "Ya, Hapus"
5. Aset akan dipindahkan ke tong sampah

3.5 Print Label Aset
Langkah-langkah:
1. Buka "Daftar Aset"
2. Cari aset
3. Klik icon 🖨️ (Printer)
4. Pilih ukuran label:
   - Kecil (50x30mm)
   - Sedang (70x40mm)
   - Besar (100x50mm)
5. Print label

Contoh Label Aset:
┌─────────────────────────┐
│  [QR CODE]              │
│                         │
│  ASET: AST-2024001      │
│  Nama: Laptop Dell      │
│  Lokasi: Ruang IT       │
│  Status: Tersedia       │
└─────────────────────────┘

3.6 Bulk Actions (Aksi Massal)
Langkah-langkah:
1. Buka "Daftar Aset"
2. Centang beberapa aset yang dipilih
3. Pilih aksi dari dropdown:
   - Delete (Hapus massal)
   - Update Status
   - Export Terpilih
4. Konfirmasi
5. Klik "Execute"

3.7 Import Aset via Excel
Menu: Manajemen Aset → Import Aset

Template Excel terdiri dari 3 sheet:

Sheet	Isi
Sheet 1	Data Aset (isi data di sini)
Sheet 2	Contoh Data
Sheet 3	Petunjuk Pengisian

Langkah Import:
1. Download template Excel
2. Isi data sesuai petunjuk
3. Klik "Pilih File"
4. Upload file Excel
5. Klik "Import"
6. Cek hasil import (berhasil/gagal)

4. MAINTENANCE ASET

4.1 Melihat Daftar Maintenance
Menu: Maintenance → Daftar Maintenance

Informasi yang ditampilkan:

Kolom	Keterangan
Aset	Nama dan kode aset
Judul	Jenis maintenance
Tanggal	Tanggal maintenance dilaksanakan
Teknisi	Nama teknisi penanggung jawab
Biaya	Total biaya maintenance
Status	Selesai/Proses/Dijadwalkan
4.2 Menambah Maintenance Baru
Menu: Maintenance → Tambah Maintenance

Form Tambah Maintenance:
┌─────────────────────────────────────────────────────────────┐
│ TAMBAH MAINTENANCE                               [×]       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Pilih Aset* : [Laptop Dell Latitude 5420 ▼]               │
│                                                             │
│  Judul*      : [Service rutin dan bersihkan laptop]        │
│                                                             │
│  Tanggal*    : [15/01/2024]                                │
│                                                             │
│  Teknisi     : [Budi Santoso      ]                         │
│                                                             │
│  Biaya       : [Rp 500.000        ]                         │
│                                                             │
│  Status*     : [Selesai ▼]                                 │
│                                                             │
│  Deskripsi:                                                │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ - Bersihkan debu dari kipas dan heatsink            │   │
│  │ - Ganti thermal paste                               │   │
│  │ - Update driver                                     │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌─────────┐    ┌─────────┐                               │
│  │ Simpan  │    │ Batal   │                               │
│  └─────────┘    └─────────┘                               │
└─────────────────────────────────────────────────────────────┘

4.3 Kalender Maintenance
Menu: Maintenance → Kalender

Fitur Kalender:

📅 Tampilan: Bulan / Minggu / Hari

🟢 Warna Hijau: Maintenance selesai

🟡 Warna Kuning: Maintenance berjalan

🔴 Warna Merah: Maintenance terlambat

🔵 Warna Biru: Maintenance dijadwalkan

Klik pada event untuk melihat detail maintenance.

4.4 Riwayat Maintenance
Menu: Maintenance → Riwayat

Menampilkan history maintenance semua aset dengan filter:

Filter periode

Filter aset

Filter status

Export ke Excel/PDF

4.5 Laporan Maintenance
Menu: Maintenance → Laporan

Laporan mencakup:

Total biaya maintenance per periode

Aset paling sering maintenance

Maintenance by teknisi

Grafik tren maintenance

5. PEMINJAMAN ASET

5.1 Melihat Daftar Peminjaman
Menu: Peminjaman → Daftar Peminjaman

Status Peminjaman:

Status	Warna	Keterangan
Menunggu	🟡 Kuning	Menunggu approval
Disetujui	🟢 Hijau	Disetujui, aset dapat diambil
Dipinjam	🔵 Biru	Aset sedang dipinjam
Terlambat	🔴 Merah	Melebihi tanggal kembali
Selesai	⚪ Abu	Sudah dikembalikan
5.2 Mengajukan Peminjaman
Menu: Peminjaman → Ajukan Peminjaman

┌─────────────────────────────────────────────────────────────┐
│ AJUKAN PEMINJAMAN                                [×]       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Pilih Aset* : [Laptop Dell Latitude 5420 ▼]               │
│                                                             │
│  Tanggal Pinjam*  : [10/01/2024]                           │
│  Tanggal Kembali* : [17/01/2024]                           │
│                                                             │
│  Keperluan* :                                              │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Presentasi proyek client di luar kantor             │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  Catatan:                                                  │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Akan mengambil tanggal 10 Jan 2024 jam 09:00        │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌─────────┐    ┌─────────┐                               │
│  │ Ajukan  │    │ Batal   │                               │
│  └─────────┘    └─────────┘                               │
└─────────────────────────────────────────────────────────────┘

5.3 Mengembalikan Aset
Langkah-langkah:
1. Buka "Daftar Peminjaman"
2. Cari peminjaman aktif
3. Klik tombol "Kembalikan"
4. Cek kondisi aset:
   [✓] Baik
   [ ] Rusak Ringan
   [ ] Rusak Berat
5. Masukkan catatan (jika ada kerusakan)
6. Klik "Konfirmasi Pengembalian"

5.4 Print Bukti Peminjaman
Langkah-langkah:
1. Buka detail peminjaman
2. Klik icon printer 🖨️
3. Print bukti peminjaman (ukuran A5 Landscape)

Contoh Bukti Peminjaman:
┌─────────────────────────────────────────────┐
│           BUKTI PEMINJAMAN ASET             │
│                 SIMASET                     │
├─────────────────────────────────────────────┤
│ No. Transaksi   : INV/PJM/202401/0001       │
│ Tanggal         : 10 Januari 2024           │
│ Peminjam        : Budi Santoso              │
│ Aset            : Laptop Dell Latitude 5420 │
│ Kode Aset       : AST-2024001               │
│ Tgl Pinjam      : 10/01/2024                │
│ Tgl Kembali     : 17/01/2024                │
│ Keperluan       : Presentasi client         │
│                                             │
│ ┌─────────┐    ┌─────────┐                 │
│ │ Tanda   │    │ Tanda   │                 │
│ │ Peminjam│    │ Petugas │                 │
│ └─────────┘    └─────────┘                 │
└─────────────────────────────────────────────┘

5.5 Perhitungan Denda
Keterlambatan	Denda per Hari
1-3 hari	Rp 10.000
4-7 hari	Rp 15.000
>7 hari	Rp 20.000

6. STOCK OPNAME

6.1 Membuat Sesi Stock Opname
Menu: Stock Opname → Buat Sesi Baru
┌─────────────────────────────────────────────────────────────┐
│ BUAT SESI STOCK OPNAME                           [×]       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Nama Sesi* : [Stock Opname Desember 2024    ]              │
│                                                             │
│  Lokasi     : [Ruang IT ▼]                                 │
│              (Kosongkan untuk semua lokasi)                 │
│                                                             │
│  Catatan    : [______________________________]              │
│                                                             │
│  ⓘ Informasi:                                              │
│  - Sesi akan membuat daftar semua aset di lokasi tertentu   │
│  - Proses stock opname dilakukan dengan scan barcode        │
│  - Anda dapat memulai sesi setelah membuatnya               │
│                                                             │
│  ┌─────────┐    ┌─────────┐                               │
│  │ Buat    │    │ Batal   │                               │
│  └─────────┘    └─────────┘                               │
└─────────────────────────────────────────────────────────────┘

6.2 Memulai Stock Opname
Langkah-langkah:
1. Buka "Daftar Sesi"
2. Cari sesi yang telah dibuat (status: Draft)
3. Klik tombol "Mulai Stock Opname"
4. Status berubah menjadi "Berjalan"

6.3 Proses Scan Aset
Menu: Stock Opname → Scan Aset
┌─────────────────────────────────────────────────────────────┐
│ PROGRESS: ████████░░░░░░░░░░░░ 40% (40/100)                │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │  🔍 Scan / Masukkan Kode Aset                       │   │
│  │  ┌─────────────────────────────────────────────────┐│   │
│  │  │ AST20260422-510AA7                    [Enter]   ││   │
│  │  └─────────────────────────────────────────────────┘│   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  HASIL SCAN:                                                │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ ✅ Aset ditemukan                                   │   │
│  │                                                     │   │
│  │ Kode Aset   : AST-2024001                          │   │
│  │ Nama Aset   : Laptop Dell Latitude 5420            │   │
│  │ Lokasi      : Ruang IT - Meja 3                    │   │
│  │ Kondisi     : Baik                                 │   │
│  │                                                     │   │
│  │ Status Verifikasi:                                  │   │
│  │ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐       │   │
│  │ │Ditemukan│ │ Hilang │ │ Rusak  │ │Berpindah│       │   │
│  │ └────────┘ └────────┘ └────────┘ └────────┘       │   │
│  │                                                     │   │
│  │ Catatan: [_______________________________]          │   │
│  │                                                     │   │
│  │ ┌─────────┐                                        │   │
│  │ │ Simpan  │                                        │   │
│  │ └─────────┘                                        │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘

6.4 Laporan Stock Opname
Menu: Stock Opname → Laporan

Ringkasan Laporan:
┌─────────────────────────────────────────────────────────────┐
│ LAPORAN STOCK OPNAME DESEMBER 2024                          │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Total Aset     : 100 aset                                  │
│  ✅ Ditemukan   : 85 aset (85%)                             │
│  ❌ Hilang      : 5 aset (5%)                               │
│  ⚠️ Rusak       : 4 aset (4%)                               │
│  🔄 Berpindah   : 6 aset (6%)                               │
│                                                             │
│  Detail Aset Hilang:                                        │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Kode      │ Nama Aset              │ Lokasi         │   │
│  ├───────────┼────────────────────────┼────────────────┤   │
│  │ AST-089   │ Mouse Logitech M185    │ Meja Helpdesk  │   │
│  │ AST-092   │ Kabel HDMI 2 meter     │ Rak Kabel      │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌─────────┐    ┌─────────┐                               │
│  │ Cetak   │    │ Export  │                               │
│  └─────────┘    └─────────┘                               │
└─────────────────────────────────────────────────────────────┘

7. MECARD GENERATE

7.1 Membuat MeCard (Kartu Nama Digital)
Menu: MeCard Generate → Buat MeCard
┌─────────────────────────────────────────────────────────────┐
│ BUAT MECARD                                      [×]       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Nama Lengkap*  : [Budi Santoso              ]             │
│  Jabatan        : [IT Manager                ]             │
│  Perusahaan     : [PT Teknologi Nusantara    ]             │
│                                                             │
│  Nomor Telepon:                              [+ Tambah]    │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ [Kantor ▼] [021-12345678            ] [🗑️]         │   │
│  │ [HP ▼]     [08123456789              ] [🗑️]         │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  Email:                                        [+ Tambah]  │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ [Kantor ▼] [budi@company.com          ] [🗑️]         │   │
│  │ [Personal▼] [budisantoso@gmail.com    ] [🗑️]         │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  Alamat:                                       [+ Tambah]  │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ [Kantor ▼] [Jl. Sudirman No 123, Jakarta] [🗑️]      │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  Social Media:                                [+ Tambah]  │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ [Website ▼] [https://budi.dev           ] [🗑️]       │   │
│  │ [LinkedIn▼] [https://linkedin.com/in/budi][🗑️]      │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  Logo/Foto:   [Choose File] no-file.png                    │
│                                                             │
│  Catatan:                                                  │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Senior IT Professional dengan 10+ tahun pengalaman  │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌─────────┐    ┌─────────┐                               │
│  │ Simpan  │    │ Batal   │                               │
│  └─────────┘    └─────────┘                               │
└─────────────────────────────────────────────────────────────┘

7.2 Download QR Code
Langkah-langkah:
1. Buka "Daftar MeCard"
2. Cari MeCard yang sudah dibuat
3. Klik icon 📱 (Download QR)
4. File PNG akan terdownload

7.3 Cetak Kartu Nama Digital
Langkah-langkah:
1. Buka detail MeCard
2. Klik tombol "Cetak Kartu"
3. Preview kartu nama akan muncul
4. Klik "Print"
5. Gunakan kertas karton/foto untuk hasil terbaik

Hasil Scan QR Code:
┌─────────────────────────────────────┐
│  Scan QR Code → Simpan ke Kontak    │
├─────────────────────────────────────┤
│                                     │
│  Nama    : Budi Santoso             │
│  Telepon : 021-12345678             │
│  Telepon : 08123456789              │
│  Email   : budi@company.com         │
│  Email   : budisantoso@gmail.com    │
│  Alamat  : Jl. Sudirman No 123      │
│  Website : https://budi.dev         │
│                                     │
│  ┌─────────┐    ┌─────────┐        │
│  │ Simpan  │    │ Batal   │        │
│  └─────────┘    └─────────┘        │
└─────────────────────────────────────┘

8. USER MANAGEMENT

8.1 Melihat Daftar User
Menu: User Management → Daftar User

Kolom	Keterangan
Name	Nama lengkap user
Email	Alamat email
Role	Super Admin/Admin/Technician/User/Viewer
Status	Active/Inactive
Last Login	Terakhir kali login

8.2 Menambah User Baru
Menu: User Management → Tambah User
┌─────────────────────────────────────────────────────────────┐
│ TAMBAH USER                                      [×]       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Nama Lengkap* : [Siti Aisyah                 ]             │
│  Email*        : [siti@company.com            ]             │
│  Password*     : [••••••••                    ]             │
│  Konfirmasi PW*: [••••••••                    ]             │
│                                                             │
│  Role*         : [User ▼]                                  │
│                                                             │
│  Departemen    : [IT Department ▼]                         │
│  Jabatan       : [Staff IT                   ]             │
│  No. Telepon   : [081234567890               ]             │
│                                                             │
│  Status        : ● Active  ○ Inactive                      │
│                                                             │
│  ┌─────────┐    ┌─────────┐                               │
│  │ Simpan  │    │ Batal   │                               │
│  └─────────┘    └─────────┘                               │
└─────────────────────────────────────────────────────────────┘

8.3 Import User via Excel
Menu: User Management → Import

Template Excel terdiri dari 3 sheet:

Sheet	Isi
Sheet 1	Data User (isi di sini)
Sheet 2	Contoh Data
Sheet 3	Petunjuk Pengisian
Kolom yang wajib diisi:

name

email

role (super_admin/admin/technician/user/viewer)

8.4 Export User
Menu: User Management → Export

Export data user ke file Excel (.xlsx)

9. LAPORAN

9.1 Laporan Aset
Menu: Laporan → Laporan Aset

Filter Laporan:
┌─────────────────────────────────────────────────────────────┐
│ FILTER LAPORAN ASET                              [×]       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Periode: ○ Bulan ini  ○ Tahun ini  ● Custom              │
│  Dari : [01/01/2024]                                       │
│  Sampai: [31/12/2024]                                      │
│                                                             │
│  Kategori : [Semua Kategori ▼]                             │
│  Lokasi   : [Semua Lokasi ▼]                               │
│  Status   : [Semua Status ▼]                               │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Include:                                            │   │
│  │ [✓] Data Aset                                       │   │
│  │ [✓] Nilai Aset                                      │   │
│  │ [✓] Penyusutan                                      │   │
│  │ [ ] History Peminjaman                              │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  Format Export: ● Excel  ○ PDF                            │
│                                                             │
│  ┌─────────┐    ┌─────────┐                               │
│  │ Export  │    │ Reset   │                               │
│  └─────────┘    └─────────┘                               │
└─────────────────────────────────────────────────────────────┘

9.2 Laporan Maintenance
Menu: Laporan → Laporan Maintenance

Informasi dalam laporan:

Total maintenance per periode

Total biaya maintenance

Aset paling sering maintenance

Maintenance by teknisi

Grafik tren maintenance

9.3 Laporan Peminjaman
Menu: Laporan → Laporan Peminjaman

Informasi dalam laporan:

Total peminjaman per periode

Aset paling sering dipinjam

User paling sering pinjam

Rata-rata durasi pinjam

Denda terkumpul

10. FAQ & TROUBLESHOOTING

10.1 Frequently Asked Questions
No	Pertanyaan	                        Jawaban
1	Lupa password?                      Klik "Lupa Password" di halaman login, ikuti instruksi yang dikirim ke email
2	Tidak bisa login?                   Cek username & password, pastikan Caps Lock tidak aktif, atau hubungi admin
3	QR Code tidak terbaca?	            Pastikan pencahayaan cukup, kamera fokus, atau coba scan dari jarak 10-20cm
4	Data tidak muncul setelah simpan?	Refresh halaman (F5) atau clear cache browser
5	Error 500?	                        Laporkan ke tim IT support, sertakan screenshot error
6	Bisa akses dari HP?	                Ya, SIMASET responsive dan bisa diakses dari smartphone
7	Bagaimana cara backup data?	        Hanya Super Admin yang bisa backup via menu Backup Database
8	Export Excel gagal?	                Cek koneksi internet, atau coba dengan jumlah data lebih sedikit

10.2 Tips Penggunaan
💡 TIPS & TRIK:

1. **Scan QR Code**
   - Gunakan aplikasi Camera bawaan HP (bukan QR scanner pihak ketiga)
   - Pastikan pencahayaan cukup
   - Scan dari jarak 15-20cm

2. **Manajemen Aset**
   - Cetak label aset untuk memudahkan identifikasi
   - Lakukan stock opname minimal 6 bulan sekali
   - Update status aset secara berkala

3. **Maintenance**
   - Gunakan kalender untuk melihat jadwal maintenance
   - Catat biaya maintenance untuk analisis budget

4. **Peminjaman**
   - Print bukti peminjaman untuk arsip
   - Ingatkan peminjam H-1 sebelum jatuh tempo

5. **Browser**
   - Gunakan Chrome atau Firefox untuk hasil terbaik
   - Clear cache secara berkala

10.3 Kontak Support
Kontak	Keterangan
📧 Email	support@simaset.com
📱 WhatsApp	08xx-xxxx-xxxx
⏰ Jam Kerja	Senin-Jumat, 08:00 - 17:00 WIB
🕐 Respon	Maksimal 2x24 jam

10.4 Status Warna
Warna	Status
🟢 Hijau	Aktif / Baik / Ditemukan / Selesai
🟡 Kuning	Maintenance / Peringatan / Menunggu
🔴 Merah	Rusak / Hilang / Terlambat / Error
🔵 Biru	Dipinjam / Berpindah / Informasi
⚪ Abu-abu	Tidak Aktif / Archived

📝 LAMPIRAN

A. Shortcut Keyboard
Shortcut	Fungsi
Ctrl + F	Search / Cari data
Ctrl + P	Print halaman
F5	Refresh halaman
Ctrl + Shift + R	Hard refresh / Clear cache
Ctrl + C	Copy
Ctrl + V	Paste

B. Glossary / Istilah
Istilah	Arti
EAV	Entity-Attribute-Value (sistem spesifikasi dinamis)
MeCard	Format QR code untuk menyimpan kontak
Stock Opname	Proses pencocokan fisik aset dengan database
VCF	Format file kontak (vCard)
Bulk Actions	Aksi massal untuk beberapa data sekaligus

<div align="center">

📚 SIMASET - Dokumentasi User
Terima Kasih telah menggunakan SIMASET!

© 2024 SIMASET - All Rights Reserved

Dokumentasi ini dapat berubah sewaktu-waktu tanpa pemberitahuan

</div> ```