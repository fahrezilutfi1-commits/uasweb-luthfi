<?php
// File: config/koneksi.php
// Fungsi: Koneksi ke database MySQL

// Informasi koneksi database
$host = "localhost";              // Server database (localhost = komputer sendiri)
$user = "root";                   // Username MySQL default XAMPP
$pass = "";                       // Password MySQL default XAMPP (kosong)
$database = "inventory_system";   // Nama database yang sudah dibuat

// Membuat koneksi ke MySQL
$conn = mysqli_connect($host, $user, $pass, $database);

// Cek apakah koneksi berhasil atau gagal
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set charset UTF-8 untuk mendukung karakter Indonesia
mysqli_set_charset($conn, "utf8");

// Jika sampai sini berarti koneksi berhasil! 🎉
?>
