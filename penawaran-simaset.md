---
title: "Sistem Manajemen Aset IT (SIMASET)"
subtitle: "Dokumentasi Lengkap Sistem"
author: "PT [NAMA PERUSAHAAN]"
date: "[TANGGAL]"
geometry: margin=1in
colorlinks: true
toc: true
toc-depth: 2
---

<div align="center">

# 🖥️ **SISTEM MANAJEMEN ASET IT (SIMASET)**

## 📋 DOKUMENTASI LENGKAP SISTEM

---

**PT [NAMA PERUSAHAAN]**  
**[TANGGAL]**

</div>

<br>

---

## 📑 **DAFTAR ISI**

1. [Pendahuluan](#1-pendahuluan)
2. [Arsitektur Sistem](#2-arsitektur-sistem)
3. [Fitur Lengkap](#3-fitur-lengkap)
4. [Modul Sistem](#4-modul-sistem)
5. [Teknologi yang Digunakan](#5-teknologi-yang-digunakan)
6. [Hak Akses & Role](#6-hak-akses--role)
7. [Paket Penawaran](#7-paket-penawaran)
8. [Layanan & Support](#8-layanan--support)
9. [Kontak & Informasi](#9-kontak--informasi)

<br>

---

## 1. **PENDAHULUAN**

### 🎯 Tentang SIMASET

**SIMASET (Sistem Manajemen Aset IT)** adalah aplikasi berbasis web yang dirancang khusus untuk mengelola aset teknologi informasi (IT) secara efisien dan profesional.

### ✨ Manfaat Menggunakan SIMASET

| Manfaat | Deskripsi |
|:--------|:-----------|
| 🚀 **Efisiensi Operasional** | Mengurangi waktu pencarian aset hingga **70%** |
| 📊 **Akurasi Data** | Eliminasi kesalahan pencatatan manual |
| 🔒 **Keamanan Aset** | Deteksi dini aset hilang atau rusak |
| 📋 **Audit Ready** | Semua aktivitas tercatat dan mudah diaudit |
| 💰 **ROI Positif** | Penghematan biaya operasional hingga **40%** |

<br>

---

## 2. **ARSITEKTUR SISTEM**

### 🏗️ Stack Teknologi

┌─────────────────────────────────────────────────────────┐
│ FRONTEND │
│ Bootstrap 5 + JavaScript + jQuery │
│ Chart.js + FullCalendar │
└─────────────────────────────────────────────────────────┘
│
▼
┌─────────────────────────────────────────────────────────┐
│ BACKEND │
│ Laravel 12 + PHP 8.2 │
│ Spatie Permission + EAV Pattern │
└─────────────────────────────────────────────────────────┘
│
▼
┌─────────────────────────────────────────────────────────┐
│ DATABASE │
│ MySQL 8.0 │
│ (20+ Tables Terintegrasi) │
└─────────────────────────────────────────────────────────┘


### 🔐 Keamanan Sistem

| Fitur Keamanan | Implementasi |
|:---------------|:--------------|
| 🔑 **Autentikasi** | Laravel Breeze/Auth |
| 🛡️ **Autorisasi** | Spatie Permission (5 Role Level) |
| 🔒 **Password Hashing** | Bcrypt |
| 🚫 **CSRF Protection** | Laravel Built-in |
| 🛡️ **XSS Prevention** | Blade Auto-escaping |
| 💉 **SQL Injection** | Eloquent ORM + Parameter Binding |

<br>

---

## 3. **FITUR LENGKAP**

### 📦 **1. MANAJEMEN ASET (EAV)**
- ✅ CRUD Aset lengkap
- ✅ Spesifikasi aset dinamis (EAV Pattern)
- ✅ Upload dokumen (multi-file)
- ✅ QR Code untuk setiap aset
- ✅ Print label aset minimalis
- ✅ Bulk actions (delete, update massal)

### 🔧 **2. MAINTENANCE & PERAWATAN**
- ✅ Jadwal maintenance periodik
- ✅ Kalender maintenance (FullCalendar)
- ✅ Riwayat perbaikan aset
- ✅ Reminder maintenance (Notifikasi)

### 📋 **3. PEMINJAMAN ASET**
- ✅ Proses peminjaman & pengembalian
- ✅ Print bukti peminjaman (A5 Landscape)
- ✅ Perhitungan denda otomatis
- ✅ History peminjaman per aset

### 📊 **4. STOCK OPNAME (SENSUS ASET)**
- ✅ Buat sesi stock opname periodik
- ✅ Scan barcode aset
- ✅ Verifikasi kondisi aset
- ✅ Update otomatis lokasi aset
- ✅ Laporan stock opname + print

### 👥 **5. USER MANAGEMENT & ROLE**
- ✅ 5 Level Role (Super Admin, Admin, Technician, User, Viewer)
- ✅ Manajemen user (CRUD)
- ✅ Import/Export user via Excel

### 📹 **6. CCTV MANAGEMENT**
- ✅ Monitoring CCTV via ping
- ✅ Snapshot CCTV
- ✅ Import data CCTV via Excel

### 🆔 **7. MECARD / KARTU NAMA DIGITAL**
- ✅ Generate QR Code kontak
- ✅ Multi-field dinamis
- ✅ Download QR Code
- ✅ Cetak kartu nama digital

### 📈 **8. DASHBOARD & ANALYTICS**
- ✅ Grafik Chart.js
- ✅ Statistik aset per kategori
- ✅ Statistik peminjaman

### 📄 **9. LAPORAN CUSTOM**
- ✅ Export Excel & PDF
- ✅ Filter dinamis
- ✅ Print label aset

### 📱 **10. QR SCANNER**
- ✅ Scan QR aset via Webcam
- ✅ Integrasi dengan Ngrok (HTTPS)

### 🔔 **11. NOTIFIKASI**
- ✅ Notifikasi bell real-time
- ✅ Notifikasi maintenance jatuh tempo

### 💾 **12. BACKUP DATABASE**
- ✅ Backup manual database
- ✅ Restore dari backup
- ✅ Schedule backup otomatis

<br>

📄 BAGIAN 2 - (Modul Sistem sampai Hak Akses)

## 4. **MODUL SISTEM**

### 🗂️ Struktur Menu

<details>
<summary><b>📁 Klik untuk melihat struktur menu lengkap</b></summary>

📊 DASHBOARD
├── Grafik Statistik
├── Aset Terbaru
├── Maintenance Aktif
└── Peminjaman Aktif

💻 MANAJEMEN ASET
├── Daftar Aset
├── Tambah Aset
├── Import/Export Aset
├── Kategori Aset
└── Lokasi Aset

🔧 MAINTENANCE
├── Daftar Maintenance
├── Jadwal Maintenance
├── Kalender Maintenance
├── Riwayat Maintenance
└── Laporan Maintenance

📦 PEMINJAMAN
├── Daftar Peminjaman
├── Ajukan Peminjaman
└── Laporan Peminjaman

👥 USER MANAGEMENT
├── Daftar User
├── Tambah User
├── Import/Export User
└── Role & Permission

📹 CCTV MANAGEMENT
├── Daftar CCTV
├── Ping & Snapshot
└── Import CCTV

🆔 MECARD GENERATE
├── Buat MeCard
├── Daftar MeCard
└── Cetak Kartu

📈 LAPORAN
├── Laporan Aset
├── Laporan Maintenance
└── Laporan Peminjaman

📂 DOKUMEN
├── Upload Dokumen
├── Kategori Folder
└── Manajemen File

🔍 QR SCANNER
└── Scan Aset via Webcam

🗄️ STOCK OPNAME
├── Buat Sesi
├── Scan Aset
└── Laporan Stock Opname

💾 BACKUP DATABASE
├── Backup Manual
├── Restore Backup
└── Schedule Backup

⚙️ KONFIGURASI
├── Pengaturan Umum
├── Logo & Branding
└── System Settings


</details>

<br>

---

## 5. **TEKNOLOGI YANG DIGUNAKAN**

### 🖥️ Backend

| Teknologi | Versi | Fungsi |
|:----------|:-----:|:--------|
| Laravel | 12.x | Framework PHP |
| PHP | 8.2.x | Bahasa Pemrograman |
| MySQL | 8.0.x | Database |
| Spatie Permission | ^6.0 | Manajemen Role |
| Laravel Excel | ^3.1 | Import/Export Excel |
| Simple QR Code | ^4.0 | Generate QR Code |

### 🎨 Frontend

| Teknologi | Versi | Fungsi |
|:----------|:-----:|:--------|
| Bootstrap | 5.3.x | CSS Framework |
| jQuery | 3.7.x | JavaScript Library |
| Chart.js | 4.4.x | Dashboard Grafik |
| FullCalendar | 6.1.x | Kalender Maintenance |
| Font Awesome | 6.5.x | Icons |
| Bootstrap Icons | 1.11.x | Icons Additional |

<br>

---

## 6. **HAK AKSES & ROLE**

### 👥 Matriks Role

| Role | Aset | Maintenance | Peminjaman | User | CCTV | MeCard | Laporan | Backup | Stock Opname |
|:-----|:----:|:-----------:|:----------:|:----:|:----:|:------:|:-------:|:------:|:------------:|
| **Super Admin** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Admin** | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Technician** | 👁️ | ✅ | ❌ | ❌ | 👁️ | 👁️ | 👁️ | ❌ | ❌ |
| **User** | 👁️ | ❌ | ✅ | ❌ | ❌ | 👁️ | 👁️ | ❌ | ❌ |
| **Viewer** | 👁️ | 👁️ | 👁️ | ❌ | 👁️ | 👁️ | 👁️ | ❌ | ❌ |

> **Keterangan:** ✅ = Full Access, 👁️ = View Only, ❌ = No Access

<br>

📄 BAGIAN 3 - (Paket Penawaran sampai Akhir)

## 7. **PAKET PENAWARAN**

### 💎 **PAKET BASIC**

✓ Instalasi SIMASET
✓ Database Setup
✓ 5 User License
✓ Basic Support (1 bulan)
✓ Dokumentasi User


**💰 Harga: Rp 5.000.000**

---

### 💼 **PAKET BUSINESS**

✓ Paket Basic
✓ 20 User License
✓ Import/Export Data
✓ Custom Report
✓ Support 6 bulan
✓ Pelatihan 2 hari


**💰 Harga: Rp 12.000.000**

---

### 🏢 **PAKET ENTERPRISE**

✓ Paket Business
✓ Unlimited User
✓ Custom Feature Development
✓ Source Code (Full)
✓ Lifetime Support
✓ Pelatihan 5 hari
✓ Server Setup & Optimization


**💰 Harga: Rp 25.000.000**

---

### 🎯 **PAKET CUSTOM**

✓ Sesuai kebutuhan klien
✓ Modifikasi fitur
✓ Integrasi dengan sistem existing
✓ On-site training


**💰 Harga: Diskusi terlebih dahulu**

<br>

---

## 8. **LAYANAN & SUPPORT**

### 🛠️ Layanan Termasuk

| Layanan | Deskripsi |
|:--------|:-----------|
| 🚀 **Instalasi** | Setup server & aplikasi |
| ⚙️ **Konfigurasi** | Setting sesuai kebutuhan perusahaan |
| 📦 **Migrasi Data** | Import data aset existing |
| 🎓 **Pelatihan** | Training untuk admin & user |
| 📄 **Dokumentasi** | Manual book & video tutorial |
| 💬 **Support** | Bantuan teknis via WA/Email |
| 🔧 **Maintenance** | Update & bug fix |

### ⏱️ Waktu Respon Support

| Paket | Respon | Penyelesaian |
|:------|:------:|:-------------:|
| Basic | 3x24 jam | 5x24 jam |
| Business | 2x24 jam | 3x24 jam |
| Enterprise | 1x24 jam | 2x24 jam |

<br>

---

## 9. **KONTAK & INFORMASI**

### 📞 Hubungi Kami

<div align="center">

| | |
|:---:|:---:|
| 📧 **Email** | support@simaset.com |
| 📱 **WhatsApp** | 08xx-xxxx-xxxx |
| 🌐 **Website** | www.simaset.com |
| 📍 **Alamat** | [Alamat Perusahaan] |

</div>

### 📝 Form Pemesanan

Silakan isi form berikut untuk pemesanan:

Nama Perusahaan: _________________________

Alamat: __________________________________

PIC: ____________________________________

Jabatan: ________________________________

No. Telepon: ____________________________

Email: __________________________________

Paket dipilih: Basic / Business / Enterprise / Custom

Jumlah user: ____________________________

Fitur tambahan: _________________________

Budget: _______________________________


<br>

---

<div align="center">

## 🌟 **SIMASET - Solusi Manajemen Aset IT Terpercaya** 🌟

---

**© 2024 SIMASET - All Rights Reserved**

</div>