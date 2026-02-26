# 📅 Sistem Informasi Penjadwalan Mata Pelajaran - MAN Sipagimbar

Sistem Informasi Penjadwalan Mata Pelajaran untuk MAN Sipagimbar. Proyek ini mengimplementasikan Algoritma Genetika untuk mengotomatisasi penyusunan jadwal sekolah secara efisien, mencegah bentrok jadwal guru dan kelas, serta memaksimalkan alokasi waktu belajar mengajar agar proses akademik menjadi jauh lebih terstruktur dan juga sangat efektif.

## 🛠️ Teknologi yang Digunakan

<div align="left">
  <img src="https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
  <img src="https://img.shields.io/badge/CodeIgniter-%23EF4223.svg?style=for-the-badge&logo=codeIgniter&logoColor=white" alt="CodeIgniter" />
  <img src="https://img.shields.io/badge/mysql-%2300000F.svg?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
</div>

## ✨ Fitur Utama
- **Automasi Penjadwalan:** Menghasilkan jadwal pelajaran secara otomatis menggunakan Algoritma Genetika.
- **Pencegahan Bentrok (Clash-free):** Memastikan tidak ada guru yang mengajar di dua kelas berbeda pada waktu yang sama.
- **Manajemen Data Akademik:** Fitur untuk mengelola data Guru, Mata Pelajaran, Kelas, dan Ruangan.
- **Optimasi Jam Mengajar:** Distribusi jam mengajar yang merata dan sesuai dengan beban kerja guru.

## 🚀 Cara Instalasi (Local Development)

Ikuti langkah-langkah berikut untuk menjalankan project ini di komputer lokal Anda:

1. **Clone Repository ini**
   ```bash
   git clone [https://github.com/JumjumiAsbullah-08/penjadwalan-algoritma-genetika.git](https://github.com/JumjumiAsbullah-08/penjadwalan-algoritma-genetika.git)
2. **Siapkan Database**
   - Buka phpMyAdmin (atau database manager lainnya).
   - Buat database baru (misalnya: db_penjadwalan).
   - Import file .sql yang telah disediakan di dalam folder project ke database tersebut.
4. **Konfigurasi Project**
   - Sesuaikan pengaturan database di dalam file konfigurasi CodeIgniter (application/config/database.php atau .env jika menggunakan CI4).
   - Pastikan base_url pada application/config/config.php (atau app/Config/App.php) sudah sesuai dengan folder Laragon/XAMPP Anda.
6. **Jalankan Aplikasi**
   Buka browser dan akses melalui localhost (contoh: http://localhost/penjadwalan-algoritma-genetika atau http://man-sipagimbar.test).
