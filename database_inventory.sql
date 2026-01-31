-- Database: inventory_system
-- Dibuat untuk UAS - Sistem Inventory dengan PHP & MySQL

CREATE DATABASE IF NOT EXISTS inventory_system;
USE inventory_system;

-- =============================================
-- MASTER DATA (8 TABEL)
-- =============================================

-- 1. Master Kategori
CREATE TABLE master_kategori (
    id_kategori INT PRIMARY KEY AUTO_INCREMENT,
    kode_kategori VARCHAR(20) UNIQUE NOT NULL,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    status ENUM('Aktif', 'Tidak Aktif') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Master Satuan
CREATE TABLE master_satuan (
    id_satuan INT PRIMARY KEY AUTO_INCREMENT,
    kode_satuan VARCHAR(20) UNIQUE NOT NULL,
    nama_satuan VARCHAR(50) NOT NULL,
    deskripsi VARCHAR(100),
    status ENUM('Aktif', 'Tidak Aktif') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3. Master Gudang
CREATE TABLE master_gudang (
    id_gudang INT PRIMARY KEY AUTO_INCREMENT,
    kode_gudang VARCHAR(20) UNIQUE NOT NULL,
    nama_gudang VARCHAR(100) NOT NULL,
    lokasi VARCHAR(200) NOT NULL,
    kapasitas VARCHAR(50),
    status ENUM('Aktif', 'Tidak Aktif') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 4. Master Barang
CREATE TABLE master_barang (
    id_barang INT PRIMARY KEY AUTO_INCREMENT,
    kode_barang VARCHAR(20) UNIQUE NOT NULL,
    nama_barang VARCHAR(150) NOT NULL,
    id_kategori INT NOT NULL,
    id_satuan INT NOT NULL,
    stok INT DEFAULT 0,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kategori) REFERENCES master_kategori(id_kategori),
    FOREIGN KEY (id_satuan) REFERENCES master_satuan(id_satuan)
);

-- 5. Master Harga
CREATE TABLE master_harga (
    id_harga INT PRIMARY KEY AUTO_INCREMENT,
    id_barang INT NOT NULL,
    harga_beli DECIMAL(15,2) NOT NULL,
    harga_jual DECIMAL(15,2) NOT NULL,
    margin DECIMAL(5,2) AS ((harga_jual - harga_beli) / harga_beli * 100) STORED,
    status ENUM('Aktif', 'Tidak Aktif') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_barang) REFERENCES master_barang(id_barang)
);

-- 6. Master Supplier
CREATE TABLE master_supplier (
    id_supplier INT PRIMARY KEY AUTO_INCREMENT,
    kode_supplier VARCHAR(20) UNIQUE NOT NULL,
    nama_supplier VARCHAR(150) NOT NULL,
    alamat TEXT NOT NULL,
    no_telp VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    status ENUM('Aktif', 'Tidak Aktif') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 7. Master Pelanggan
CREATE TABLE master_pelanggan (
    id_pelanggan INT PRIMARY KEY AUTO_INCREMENT,
    kode_pelanggan VARCHAR(20) UNIQUE NOT NULL,
    nama_pelanggan VARCHAR(150) NOT NULL,
    alamat TEXT NOT NULL,
    no_telp VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    status ENUM('Aktif', 'Tidak Aktif') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 8. Master Karyawan
CREATE TABLE master_karyawan (
    id_karyawan INT PRIMARY KEY AUTO_INCREMENT,
    kode_karyawan VARCHAR(20) UNIQUE NOT NULL,
    nama_karyawan VARCHAR(150) NOT NULL,
    jabatan ENUM('Admin', 'Kasir', 'Gudang', 'Supervisor') NOT NULL,
    no_telp VARCHAR(20) NOT NULL,
    status ENUM('Aktif', 'Tidak Aktif') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================================
-- TRANSAKSI DATA (5 TABEL + DETAIL)
-- =============================================

-- 1. Transaksi Pembelian (Header)
CREATE TABLE transaksi_pembelian (
    id_pembelian INT PRIMARY KEY AUTO_INCREMENT,
    no_faktur VARCHAR(50) UNIQUE NOT NULL,
    tanggal_pembelian DATE NOT NULL,
    id_supplier INT NOT NULL,
    id_karyawan INT NOT NULL,
    total_pembelian DECIMAL(15,2) DEFAULT 0,
    status ENUM('Selesai', 'Menunggu', 'Batal') DEFAULT 'Selesai',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_supplier) REFERENCES master_supplier(id_supplier),
    FOREIGN KEY (id_karyawan) REFERENCES master_karyawan(id_karyawan)
);

-- Detail Pembelian
CREATE TABLE detail_pembelian (
    id_detail_pembelian INT PRIMARY KEY AUTO_INCREMENT,
    id_pembelian INT NOT NULL,
    id_barang INT NOT NULL,
    qty INT NOT NULL,
    harga_beli DECIMAL(15,2) NOT NULL,
    subtotal DECIMAL(15,2) AS (qty * harga_beli) STORED,
    FOREIGN KEY (id_pembelian) REFERENCES transaksi_pembelian(id_pembelian) ON DELETE CASCADE,
    FOREIGN KEY (id_barang) REFERENCES master_barang(id_barang)
);

-- 2. Transaksi Penjualan (Header)
CREATE TABLE transaksi_penjualan (
    id_penjualan INT PRIMARY KEY AUTO_INCREMENT,
    no_invoice VARCHAR(50) UNIQUE NOT NULL,
    tanggal_penjualan DATE NOT NULL,
    id_pelanggan INT NOT NULL,
    id_karyawan INT NOT NULL,
    total_penjualan DECIMAL(15,2) DEFAULT 0,
    status ENUM('Selesai', 'Menunggu Pembayaran', 'Dalam Proses', 'Batal') DEFAULT 'Selesai',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pelanggan) REFERENCES master_pelanggan(id_pelanggan),
    FOREIGN KEY (id_karyawan) REFERENCES master_karyawan(id_karyawan)
);

-- Detail Penjualan
CREATE TABLE detail_penjualan (
    id_detail_penjualan INT PRIMARY KEY AUTO_INCREMENT,
    id_penjualan INT NOT NULL,
    id_barang INT NOT NULL,
    qty INT NOT NULL,
    harga_jual DECIMAL(15,2) NOT NULL,
    subtotal DECIMAL(15,2) AS (qty * harga_jual) STORED,
    FOREIGN KEY (id_penjualan) REFERENCES transaksi_penjualan(id_penjualan) ON DELETE CASCADE,
    FOREIGN KEY (id_barang) REFERENCES master_barang(id_barang)
);

-- 3. Retur Pembelian (Header)
CREATE TABLE retur_pembelian (
    id_retur_pembelian INT PRIMARY KEY AUTO_INCREMENT,
    no_retur VARCHAR(50) UNIQUE NOT NULL,
    tanggal_retur DATE NOT NULL,
    id_supplier INT NOT NULL,
    id_karyawan INT NOT NULL,
    total_barang INT DEFAULT 0,
    total_nilai DECIMAL(15,2) DEFAULT 0,
    status ENUM('Menunggu', 'Diterima', 'Ditolak') DEFAULT 'Menunggu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_supplier) REFERENCES master_supplier(id_supplier),
    FOREIGN KEY (id_karyawan) REFERENCES master_karyawan(id_karyawan)
);

-- Detail Retur Pembelian
CREATE TABLE detail_retur_pembelian (
    id_detail_retur_pembelian INT PRIMARY KEY AUTO_INCREMENT,
    id_retur_pembelian INT NOT NULL,
    id_barang INT NOT NULL,
    qty INT NOT NULL,
    harga DECIMAL(15,2) NOT NULL,
    alasan TEXT,
    subtotal DECIMAL(15,2) AS (qty * harga) STORED,
    FOREIGN KEY (id_retur_pembelian) REFERENCES retur_pembelian(id_retur_pembelian) ON DELETE CASCADE,
    FOREIGN KEY (id_barang) REFERENCES master_barang(id_barang)
);

-- 4. Retur Penjualan (Header)
CREATE TABLE retur_penjualan (
    id_retur_penjualan INT PRIMARY KEY AUTO_INCREMENT,
    no_retur VARCHAR(50) UNIQUE NOT NULL,
    tanggal_retur DATE NOT NULL,
    id_pelanggan INT NOT NULL,
    id_karyawan INT NOT NULL,
    total_barang INT DEFAULT 0,
    total_nilai DECIMAL(15,2) DEFAULT 0,
    status ENUM('Menunggu', 'Selesai', 'Ditolak') DEFAULT 'Menunggu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pelanggan) REFERENCES master_pelanggan(id_pelanggan),
    FOREIGN KEY (id_karyawan) REFERENCES master_karyawan(id_karyawan)
);

-- Detail Retur Penjualan
CREATE TABLE detail_retur_penjualan (
    id_detail_retur_penjualan INT PRIMARY KEY AUTO_INCREMENT,
    id_retur_penjualan INT NOT NULL,
    id_barang INT NOT NULL,
    qty INT NOT NULL,
    harga DECIMAL(15,2) NOT NULL,
    alasan TEXT,
    subtotal DECIMAL(15,2) AS (qty * harga) STORED,
    FOREIGN KEY (id_retur_penjualan) REFERENCES retur_penjualan(id_retur_penjualan) ON DELETE CASCADE,
    FOREIGN KEY (id_barang) REFERENCES master_barang(id_barang)
);

-- 5. Stok Opname (Header)
CREATE TABLE stok_opname (
    id_opname INT PRIMARY KEY AUTO_INCREMENT,
    no_opname VARCHAR(50) UNIQUE NOT NULL,
    tanggal_opname DATE NOT NULL,
    id_gudang INT NOT NULL,
    id_karyawan INT NOT NULL,
    total_selisih INT DEFAULT 0,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_gudang) REFERENCES master_gudang(id_gudang),
    FOREIGN KEY (id_karyawan) REFERENCES master_karyawan(id_karyawan)
);

-- Detail Stok Opname
CREATE TABLE detail_stok_opname (
    id_detail_opname INT PRIMARY KEY AUTO_INCREMENT,
    id_opname INT NOT NULL,
    id_barang INT NOT NULL,
    stok_sistem INT NOT NULL,
    stok_fisik INT NOT NULL,
    selisih INT AS (stok_fisik - stok_sistem) STORED,
    FOREIGN KEY (id_opname) REFERENCES stok_opname(id_opname) ON DELETE CASCADE,
    FOREIGN KEY (id_barang) REFERENCES master_barang(id_barang)
);

-- =============================================
-- DATA DUMMY UNTUK TESTING
-- =============================================

-- Dummy Kategori
INSERT INTO master_kategori (kode_kategori, nama_kategori, deskripsi, status) VALUES
('KAT-001', 'Sembako', 'Bahan makanan pokok', 'Aktif'),
('KAT-002', 'Elektronik', 'Perangkat elektronik', 'Aktif'),
('KAT-003', 'Pakaian', 'Produk pakaian dan fashion', 'Aktif');

-- Dummy Satuan
INSERT INTO master_satuan (kode_satuan, nama_satuan, deskripsi, status) VALUES
('SAT-001', 'Pcs', 'Pieces / Buah', 'Aktif'),
('SAT-002', 'Kg', 'Kilogram', 'Aktif'),
('SAT-003', 'Liter', 'Liter', 'Aktif');

-- Dummy Gudang
INSERT INTO master_gudang (kode_gudang, nama_gudang, lokasi, kapasitas, status) VALUES
('GD-001', 'Gudang Utama', 'Jakarta Pusat', '5000 m²', 'Aktif'),
('GD-002', 'Gudang Cabang A', 'Jakarta Selatan', '3000 m²', 'Aktif');

-- Dummy Barang
INSERT INTO master_barang (kode_barang, nama_barang, id_kategori, id_satuan, stok, deskripsi) VALUES
('BRG-001', 'Beras Premium 5kg', 1, 1, 150, 'Beras berkualitas premium'),
('BRG-002', 'Gula Pasir 1kg', 1, 2, 200, 'Gula pasir putih'),
('BRG-003', 'Minyak Goreng 2L', 1, 3, 120, 'Minyak goreng kemasan 2 liter');

-- Dummy Harga
INSERT INTO master_harga (id_barang, harga_beli, harga_jual, status) VALUES
(1, 68000, 75000, 'Aktif'),
(2, 13500, 15000, 'Aktif'),
(3, 28000, 32000, 'Aktif');

-- Dummy Supplier
INSERT INTO master_supplier (kode_supplier, nama_supplier, alamat, no_telp, email, status) VALUES
('SPL-001', 'PT Maju Bersama', 'Jakarta Utara', '021-12345678', 'info@majubersama.com', 'Aktif'),
('SPL-002', 'CV Sentosa Jaya', 'Bandung', '022-87654321', 'sentosa@email.com', 'Aktif');

-- Dummy Pelanggan
INSERT INTO master_pelanggan (kode_pelanggan, nama_pelanggan, alamat, no_telp, email, status) VALUES
('PLG-001', 'Andi Pratama', 'Jakarta Barat', '0812-1111-2222', 'andi@email.com', 'Aktif'),
('PLG-002', 'Budi Santoso', 'Bandung', '0813-2222-3333', 'budi@email.com', 'Aktif');

-- Dummy Karyawan
INSERT INTO master_karyawan (kode_karyawan, nama_karyawan, jabatan, no_telp, status) VALUES
('KR-001', 'Agus Saputra', 'Gudang', '0812-3456-7890', 'Aktif'),
('KR-002', 'Dewi Lestari', 'Kasir', '0813-7777-2211', 'Aktif'),
('KR-003', 'Siti Nurhaliza', 'Supervisor', '0814-9999-8888', 'Aktif');

-- Dummy Transaksi Pembelian
INSERT INTO transaksi_pembelian (no_faktur, tanggal_pembelian, id_supplier, id_karyawan, total_pembelian, status) VALUES
('PB-001', '2025-01-10', 1, 1, 5200000, 'Selesai');

INSERT INTO detail_pembelian (id_pembelian, id_barang, qty, harga_beli) VALUES
(1, 1, 50, 68000),
(1, 2, 100, 13500),
(1, 3, 30, 28000);

-- Dummy Transaksi Penjualan
INSERT INTO transaksi_penjualan (no_invoice, tanggal_penjualan, id_pelanggan, id_karyawan, total_penjualan, status) VALUES
('INV-001', '2025-01-12', 1, 2, 1050000, 'Selesai');

INSERT INTO detail_penjualan (id_penjualan, id_barang, qty, harga_jual) VALUES
(1, 1, 10, 75000),
(1, 2, 20, 15000);

-- =============================================
-- INDEX untuk optimasi query
-- =============================================
CREATE INDEX idx_barang_kategori ON master_barang(id_kategori);
CREATE INDEX idx_barang_satuan ON master_barang(id_satuan);
CREATE INDEX idx_pembelian_supplier ON transaksi_pembelian(id_supplier);
CREATE INDEX idx_penjualan_pelanggan ON transaksi_penjualan(id_pelanggan);
CREATE INDEX idx_pembelian_tanggal ON transaksi_pembelian(tanggal_pembelian);
CREATE INDEX idx_penjualan_tanggal ON transaksi_penjualan(tanggal_penjualan);
