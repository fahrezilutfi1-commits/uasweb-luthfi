<?php
// =====================================
// KONEKSI DATABASE
// =====================================
include __DIR__ . '/../config/koneksi.php';

// =====================================
// QUERY LAPORAN PENJUALAN DETAIL
// =====================================
$query = "
    SELECT 
        tp.no_invoice,
        tp.tanggal_penjualan,
        p.nama_pelanggan,
        p.no_telp AS telp_pelanggan,
        b.kode_barang,
        b.nama_barang,
        kt.nama_kategori,
        dp.qty,
        st.nama_satuan,
        dp.harga_jual,
        dp.subtotal,
        k.nama_karyawan AS kasir,
        tp.status
    FROM transaksi_penjualan tp
    INNER JOIN detail_penjualan dp 
        ON tp.id_penjualan = dp.id_penjualan
    INNER JOIN master_pelanggan p 
        ON tp.id_pelanggan = p.id_pelanggan
    INNER JOIN master_barang b 
        ON dp.id_barang = b.id_barang
    INNER JOIN master_kategori kt 
        ON b.id_kategori = kt.id_kategori
    INNER JOIN master_satuan st 
        ON b.id_satuan = st.id_satuan
    INNER JOIN master_karyawan k 
        ON tp.id_karyawan = k.id_karyawan
    WHERE tp.status = 'Selesai'
    ORDER BY tp.tanggal_penjualan DESC
";

$data = mysqli_query($conn, $query);

// ERROR HANDLING
if (!$data) {
    die("Query Error: " . mysqli_error($conn));
}

$total_penjualan = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan Detail</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        @media print {
            .no-print { display: none; }
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #667eea;
            color: white;
        }
        table tfoot th {
            background-color: #764ba2;
        }
        .page-header {
            margin-bottom: 20px;
        }
        .alert {
            padding: 15px;
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 4px;
            margin: 15px 0;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        button {
            background-color: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #5568d3;
        }
    </style>
</head>
<body>

<div class="page-header">
    <h1>📊 Laporan Penjualan Detail</h1>
    <p>Laporan detail transaksi penjualan dengan JOIN 7 tabel</p>

    <div class="alert alert-info">
        <strong>📌 JOIN Tables:</strong>
        transaksi_penjualan, detail_penjualan, master_pelanggan,
        master_barang, master_kategori, master_satuan, master_karyawan
    </div>

    <div class="no-print" style="margin-top:15px;">
        <button onclick="window.print()">🖨️ Print Laporan</button>
        <button onclick="window.location.href='../index.php?page=dashboard'">🏠 Kembali ke Dashboard</button>
    </div>
</div>

<div class="card">
    <h3>Detail Penjualan</h3>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Invoice</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>No Telp</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Qty</th>
                    <th>Satuan</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                    <th>Kasir</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($data) > 0) {
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($data)):
                        $total_penjualan += $row['subtotal'];
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['no_invoice']); ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_penjualan'])); ?></td>
                    <td><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                    <td><?= htmlspecialchars($row['telp_pelanggan']); ?></td>
                    <td><?= htmlspecialchars($row['kode_barang']); ?></td>
                    <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                    <td><?= htmlspecialchars($row['nama_kategori']); ?></td>
                    <td><?= number_format($row['qty'], 0); ?></td>
                    <td><?= htmlspecialchars($row['nama_satuan']); ?></td>
                    <td>Rp <?= number_format($row['harga_jual'], 0, ',', '.'); ?></td>
                    <td>Rp <?= number_format($row['subtotal'], 0, ',', '.'); ?></td>
                    <td><?= htmlspecialchars($row['kasir']); ?></td>
                </tr>
                <?php 
                    endwhile;
                } else {
                    echo '<tr><td colspan="13" style="text-align:center;">Tidak ada data penjualan</td></tr>';
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="11" style="text-align:right;">TOTAL PENJUALAN:</th>
                    <th colspan="2">
                        Rp <?= number_format($total_penjualan, 0, ',', '.'); ?>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="card">
    <h3>📝 Penjelasan Query</h3>
    <p>Laporan ini menggunakan <strong>6 JOIN</strong> untuk menggabungkan data dari 7 tabel:</p>
    <ul>
        <li>✅ transaksi_penjualan - Data header transaksi</li>
        <li>✅ detail_penjualan - Data detail barang yang dijual (qty, harga, subtotal)</li>
        <li>✅ master_pelanggan - Informasi pelanggan</li>
        <li>✅ master_barang - Informasi barang</li>
        <li>✅ master_kategori - Kategori barang</li>
        <li>✅ master_satuan - Satuan barang</li>
        <li>✅ master_karyawan - Informasi kasir/petugas</li>
    </ul>
    
    <h4>Keterangan:</h4>
    <ul>
        <li>✔ Data diambil dari transaksi yang berstatus <b>Selesai</b></li>
        <li>✔ Harga jual dan subtotal sudah tersimpan di tabel detail_penjualan</li>
        <li>✔ Total penjualan dijumlahkan dari semua subtotal</li>
    </ul>
</div>

</body>
</html>