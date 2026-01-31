<?php
// Laporan Stok Barang - dengan JOIN 4 tabel

$query = "SELECT 
            b.kode_barang,
            b.nama_barang,
            kt.nama_kategori,
            st.nama_satuan,
            b.stok,
            h.harga_beli,
            h.harga_jual,
            h.margin,
            (b.stok * h.harga_beli) as nilai_beli,
            (b.stok * h.harga_jual) as nilai_jual,
            CASE 
                WHEN b.stok <= 50 THEN 'Stok Rendah'
                WHEN b.stok <= 100 THEN 'Stok Normal'
                ELSE 'Stok Tinggi'
            END as status_stok
          FROM master_barang b
          JOIN master_kategori kt ON b.id_kategori = kt.id_kategori
          JOIN master_satuan st ON b.id_satuan = st.id_satuan
          LEFT JOIN master_harga h ON b.id_barang = h.id_barang
          WHERE h.status = 'Aktif' OR h.status IS NULL
          ORDER BY nilai_jual DESC";

$data = mysqli_query($conn, $query);
$total_nilai_beli = 0;
$total_nilai_jual = 0;
?>

<div class="page-header">
    <h1>📦 Laporan Stok Barang</h1>
    <p>Laporan nilai stok barang dengan JOIN 4 tabel</p>
    <div class="alert alert-info">
        <strong>📌 JOIN Tables:</strong> master_barang, master_kategori, master_satuan, master_harga
    </div>
    
    <!-- PRINT & EXPORT BUTTONS -->
    <div class="no-print" style="margin-top: 15px;">
        <button onclick="printPage()" class="btn btn-print">🖨️ Print Laporan</button>
        <button onclick="exportToPDF()" class="btn btn-export">📄 Export PDF</button>
    </div>
</div>

<div class="card">
    <h3>Nilai Stok Barang</h3>
    
    <!-- SEARCH BOX -->
    <div class="search-box no-print">
        <input type="text" id="searchStok" onkeyup="searchTable('searchStok', 'stokTable')" 
               placeholder="🔍 Cari data (kode, nama, kategori, satuan, status)...">
    </div>
    
    <div class="table-responsive">
        <table class="data-table" id="stokTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Satuan</th>
                    <th>Stok</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Margin (%)</th>
                    <th>Nilai Beli</th>
                    <th>Nilai Jual</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = mysqli_fetch_assoc($data)): 
                    $total_nilai_beli += $row['nilai_beli'];
                    $total_nilai_jual += $row['nilai_jual'];
                    
                    $status_class = '';
                    if ($row['status_stok'] == 'Stok Rendah') $status_class = 'badge-danger';
                    elseif ($row['status_stok'] == 'Stok Normal') $status_class = 'badge-warning';
                    else $status_class = 'badge-success';
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['kode_barang'] ?></td>
                    <td><?= $row['nama_barang'] ?></td>
                    <td><?= $row['nama_kategori'] ?></td>
                    <td><?= $row['nama_satuan'] ?></td>
                    <td><?= $row['stok'] ?></td>
                    <td>Rp <?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                    <td><?= number_format($row['margin'], 2) ?>%</td>
                    <td>Rp <?= number_format($row['nilai_beli'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($row['nilai_jual'], 0, ',', '.') ?></td>
                    <td><span class="badge <?= $status_class ?>"><?= $row['status_stok'] ?></span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="9" style="text-align: right;">TOTAL NILAI INVENTORY:</th>
                    <th>Rp <?= number_format($total_nilai_beli, 0, ',', '.') ?></th>
                    <th>Rp <?= number_format($total_nilai_jual, 0, ',', '.') ?></th>
                    <th></th>
                </tr>
                <tr>
                    <th colspan="9" style="text-align: right;">POTENSI PROFIT:</th>
                    <th colspan="2">Rp <?= number_format($total_nilai_jual - $total_nilai_beli, 0, ',', '.') ?></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="card">
    <h3>📝 Penjelasan Query</h3>
    <p>Laporan ini menggunakan <strong>3 JOIN</strong> untuk menggabungkan data dari 4 tabel:</p>
    <ul>
        <li>✅ master_barang - Data stok barang</li>
        <li>✅ master_kategori - Kategori barang</li>
        <li>✅ master_satuan - Satuan barang</li>
        <li>✅ master_harga - Harga beli & jual</li>
    </ul>
    <p><strong>Calculated Fields:</strong></p>
    <ul>
        <li>✅ Nilai Beli = Stok × Harga Beli</li>
        <li>✅ Nilai Jual = Stok × Harga Jual</li>
        <li>✅ Status Stok = CASE WHEN berdasarkan jumlah stok</li>
    </ul>
</div>
