<?php
$data = mysqli_query($conn, "SELECT rp.*, p.nama_pelanggan FROM retur_penjualan rp JOIN master_pelanggan p ON rp.id_pelanggan = p.id_pelanggan ORDER BY rp.tanggal_retur DESC");
?>
<div class="page-header"><h1>Retur Penjualan</h1><p>Pengembalian barang dari pelanggan</p></div>
<div class="card"><h3>Daftar Retur Penjualan</h3>
<table class="data-table"><thead><tr><th>No</th><th>No. Retur</th><th>Tanggal</th><th>Pelanggan</th><th>Total Barang</th><th>Total Nilai</th><th>Status</th></tr></thead>
<tbody><?php $no = 1; while ($row = mysqli_fetch_assoc($data)): ?><tr><td><?= $no++ ?></td><td><?= $row['no_retur'] ?></td><td><?= date('d/m/Y', strtotime($row['tanggal_retur'])) ?></td><td><?= $row['nama_pelanggan'] ?></td><td><?= $row['total_barang'] ?> item</td><td>Rp <?= number_format($row['total_nilai'], 0, ',', '.') ?></td><td><span class="badge badge-success"><?= $row['status'] ?></span></td></tr><?php endwhile; ?></tbody></table></div>
