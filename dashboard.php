<?php
// DASHBOARD dengan Statistik dan Chart

// Query untuk statistik
$total_barang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM master_barang"))['total'];
$total_kategori = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM master_kategori WHERE status='Aktif'"))['total'];
$total_supplier = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM master_supplier WHERE status='Aktif'"))['total'];
$total_pelanggan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM master_pelanggan WHERE status='Aktif'"))['total'];

// Total nilai stok
$total_stok_query = mysqli_query($conn, "SELECT SUM(b.stok * h.harga_beli) as total_nilai 
                                         FROM master_barang b 
                                         JOIN master_harga h ON b.id_barang = h.id_barang 
                                         WHERE h.status='Aktif'");
$total_nilai_stok = mysqli_fetch_assoc($total_stok_query)['total_nilai'] ?? 0;

// Total penjualan bulan ini
$bulan_ini = date('Y-m');
$penjualan_bulan_ini = mysqli_query($conn, "SELECT SUM(total_penjualan) as total 
                                            FROM transaksi_penjualan 
                                            WHERE DATE_FORMAT(tanggal_penjualan, '%Y-%m') = '$bulan_ini' 
                                            AND status='Selesai'");
$total_penjualan_bulan = mysqli_fetch_assoc($penjualan_bulan_ini)['total'] ?? 0;

// Total pembelian bulan ini
$pembelian_bulan_ini = mysqli_query($conn, "SELECT SUM(total_pembelian) as total 
                                            FROM transaksi_pembelian 
                                            WHERE DATE_FORMAT(tanggal_pembelian, '%Y-%m') = '$bulan_ini' 
                                            AND status='Selesai'");
$total_pembelian_bulan = mysqli_fetch_assoc($pembelian_bulan_ini)['total'] ?? 0;

// Barang dengan stok menipis (< 50)
$stok_menipis = mysqli_query($conn, "SELECT COUNT(*) as total FROM master_barang WHERE stok < 50");
$jumlah_stok_menipis = mysqli_fetch_assoc($stok_menipis)['total'];

// Data untuk chart - Top 5 Barang Terlaris
$top_barang = mysqli_query($conn, "SELECT b.nama_barang, SUM(dp.qty) as total_terjual
                                   FROM detail_penjualan dp
                                   JOIN master_barang b ON dp.id_barang = b.id_barang
                                   GROUP BY dp.id_barang
                                   ORDER BY total_terjual DESC
                                   LIMIT 5");

$barang_labels = [];
$barang_data = [];
while($row = mysqli_fetch_assoc($top_barang)) {
    $barang_labels[] = $row['nama_barang'];
    $barang_data[] = $row['total_terjual'];
}

// Data untuk chart - Penjualan 6 Bulan Terakhir
$penjualan_6bulan = mysqli_query($conn, "SELECT DATE_FORMAT(tanggal_penjualan, '%Y-%m') as bulan, 
                                         SUM(total_penjualan) as total
                                         FROM transaksi_penjualan
                                         WHERE tanggal_penjualan >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                                         AND status='Selesai'
                                         GROUP BY bulan
                                         ORDER BY bulan ASC");

$bulan_labels = [];
$penjualan_data = [];
while($row = mysqli_fetch_assoc($penjualan_6bulan)) {
    $bulan_labels[] = date('M Y', strtotime($row['bulan'] . '-01'));
    $penjualan_data[] = $row['total'];
}

// Recent transactions
$recent_transactions = mysqli_query($conn, "SELECT no_invoice, tanggal_penjualan, total_penjualan, status
                                            FROM transaksi_penjualan
                                            ORDER BY tanggal_penjualan DESC
                                            LIMIT 5");
?>

<div class="page-header">
    <h1>📊 Dashboard Inventory System</h1>
    <p>Ringkasan data dan statistik sistem inventory</p>
</div>

<!-- STATISTICS CARDS -->
<div class="stats-container">
    <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-value"><?= number_format($total_barang) ?></div>
        <div class="stat-label">Total Barang</div>
    </div>
    
    <div class="stat-card green">
        <div class="stat-icon">💰</div>
        <div class="stat-value">Rp <?= number_format($total_penjualan_bulan / 1000000, 1) ?>M</div>
        <div class="stat-label">Penjualan Bulan Ini</div>
    </div>
    
    <div class="stat-card orange">
        <div class="stat-icon">🛒</div>
        <div class="stat-value">Rp <?= number_format($total_pembelian_bulan / 1000000, 1) ?>M</div>
        <div class="stat-label">Pembelian Bulan Ini</div>
    </div>
    
    <div class="stat-card blue">
        <div class="stat-icon">💵</div>
        <div class="stat-value">Rp <?= number_format($total_nilai_stok / 1000000, 1) ?>M</div>
        <div class="stat-label">Total Nilai Stok</div>
    </div>
</div>

<!-- INFO CARDS -->
<div class="stats-container">
    <div class="card">
        <h3>🏷️ Master Data</h3>
        <ul style="line-height: 2;">
            <li>Kategori: <strong><?= $total_kategori ?></strong></li>
            <li>Supplier: <strong><?= $total_supplier ?></strong></li>
            <li>Pelanggan: <strong><?= $total_pelanggan ?></strong></li>
        </ul>
    </div>
    
    <div class="card">
        <h3>⚠️ Stok Menipis</h3>
        <div style="padding: 20px; text-align: center;">
            <div style="font-size: 48px; color: #e74c3c; font-weight: bold;"><?= $jumlah_stok_menipis ?></div>
            <p>Barang dengan stok < 50</p>
            <a href="?page=barang" class="btn btn-primary" style="margin-top: 10px;">Lihat Detail</a>
        </div>
    </div>
</div>

<!-- CHARTS -->
<div class="chart-grid">
    <div class="chart-container">
        <h3>📈 Penjualan 6 Bulan Terakhir</h3>
        <canvas id="salesChart"></canvas>
    </div>
    
    <div class="chart-container">
        <h3>🏆 Top 5 Barang Terlaris</h3>
        <canvas id="topProductsChart"></canvas>
    </div>
</div>

<!-- RECENT TRANSACTIONS -->
<div class="card">
    <h3>📋 Transaksi Terbaru</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>No. Invoice</th>
                <th>Tanggal</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($recent_transactions)): ?>
            <tr>
                <td><?= $row['no_invoice'] ?></td>
                <td><?= date('d/m/Y', strtotime($row['tanggal_penjualan'])) ?></td>
                <td>Rp <?= number_format($row['total_penjualan'], 0, ',', '.') ?></td>
                <td>
                    <span class="badge badge-success"><?= $row['status'] ?></span>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- CHART.JS SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Sales Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($bulan_labels) ?>,
        datasets: [{
            label: 'Penjualan (Rp)',
            data: <?= json_encode($penjualan_data) ?>,
            backgroundColor: 'rgba(102, 126, 234, 0.2)',
            borderColor: 'rgba(102, 126, 234, 1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});

// Top Products Chart
const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
const topProductsChart = new Chart(topProductsCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($barang_labels) ?>,
        datasets: [{
            label: 'Jumlah Terjual',
            data: <?= json_encode($barang_data) ?>,
            backgroundColor: [
                'rgba(102, 126, 234, 0.8)',
                'rgba(118, 75, 162, 0.8)',
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)'
            ],
            borderColor: [
                'rgba(102, 126, 234, 1)',
                'rgba(118, 75, 162, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
