<?php
// File: index.php
// Fungsi: Halaman utama inventory system
include 'config/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System - Fahrezi Luthfi</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <button class="menu-toggle" onclick="toggleMenu()">☰</button>
    
    <div class="container">
        <!-- SIDEBAR -->
        <div class="sidebar" id="sidebar">
            <div class="header">
                <h2>FAHREZI LUTHFI</h2>
                <p>2455201110006</p>
            </div>

            <div class="main-menu">
                <!-- MENU PORTFOLIO -->
                <div class="menu-section-title">PORTFOLIO</div>
                <div class="list-item">
                    <a href="?page=home">🏠 Home</a>
                </div>
                <div class="list-item">
                    <a href="?page=dashboard">📊 Dashboard</a>
                </div>

                <!-- MENU MASTER DATA -->
                <div class="menu-section-title">MASTER DATA</div>
                <div class="list-item">
                    <a href="?page=barang">📦 Master Barang</a>
                </div>
                <div class="list-item">
                    <a href="?page=gudang">🏭 Master Gudang</a>
                </div>
                <div class="list-item">
                    <a href="?page=harga">💰 Master Harga</a>
                </div>
                <div class="list-item">
                    <a href="?page=karyawan">👥 Master Karyawan</a>
                </div>
                <div class="list-item">
                    <a href="?page=kategori">🏷️ Master Kategori</a>
                </div>
                <div class="list-item">
                    <a href="?page=pelanggan">👤 Master Pelanggan</a>
                </div>
                <div class="list-item">
                    <a href="?page=satuan">📏 Master Satuan</a>
                </div>
                <div class="list-item">
                    <a href="?page=supplier">🚚 Master Supplier</a>
                </div>

                <!-- MENU TRANSAKSI -->
                <div class="menu-section-title">TRANSAKSI</div>
                <div class="list-item">
                    <a href="?page=pembelian">🛒 Transaksi Pembelian</a>
                </div>
                <div class="list-item">
                    <a href="?page=penjualan">💳 Transaksi Penjualan</a>
                </div>
                <div class="list-item">
                    <a href="?page=retur_pembelian">↩️ Retur Pembelian</a>
                </div>
                <div class="list-item">
                    <a href="?page=retur_penjualan">🔄 Retur Penjualan</a>
                </div>
                <div class="list-item">
                    <a href="?page=stok_opname">📊 Stok Opname</a>
                </div>

                <!-- MENU LAPORAN -->
                <div class="menu-section-title">LAPORAN</div>
                <div class="list-item">
                    <a href="?page=laporan_penjualan">📈 Laporan Penjualan</a>
                </div>
                <div class="list-item">
                    <a href="?page=laporan_stok">📊 Laporan Stok Barang</a>
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <?php
            // Routing sederhana
            $page = isset($_GET['page']) ? $_GET['page'] : 'home';
            
            switch($page) {
                // Portfolio
                case 'home':
                    include 'home.php';
                    break;
                
                case 'dashboard':
                    include 'dashboard.php';
                    break;
                
                // Master Data
                case 'barang':
                    include 'master/barang.php';
                    break;
                case 'gudang':
                    include 'master/gudang.php';
                    break;
                case 'harga':
                    include 'master/harga.php';
                    break;
                case 'karyawan':
                    include 'master/karyawan.php';
                    break;
                case 'kategori':
                    include 'master/kategori.php';
                    break;
                case 'pelanggan':
                    include 'master/pelanggan.php';
                    break;
                case 'satuan':
                    include 'master/satuan.php';
                    break;
                case 'supplier':
                    include 'master/supplier.php';
                    break;
                
                // Transaksi
                case 'pembelian':
                    include 'transaksi/pembelian.php';
                    break;
                case 'penjualan':
                    include 'transaksi/penjualan.php';
                    break;
                case 'retur_pembelian':
                    include 'transaksi/retur_pembelian.php';
                    break;
                case 'retur_penjualan':
                    include 'transaksi/retur_penjualan.php';
                    break;
                case 'stok_opname':
                    include 'transaksi/stok_opname.php';
                    break;
                
                // Laporan
                case 'laporan_penjualan':
                    include 'laporan/laporan_penjualan.php';
                    break;
                case 'laporan_stok':
                    include 'laporan/laporan_stok.php';
                    break;
                
                default:
                    echo '<h2>Halaman tidak ditemukan</h2>';
                    break;
            }
            ?>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>