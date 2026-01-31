<?php
$data = mysqli_query($conn, "SELECT so.*, g.nama_gudang, k.nama_karyawan FROM stok_opname so JOIN master_gudang g ON so.id_gudang = g.id_gudang JOIN master_karyawan k ON so.id_karyawan = k.id_karyawan ORDER BY so.tanggal_opname DESC");
?>
<div class="page-header"><h1>Stok Opname</h1><p>Pengecekan fisik persediaan barang</p></div>
<div class="card"><h3>Riwayat Stok Opname</h3>
<table class="data-table"><thead><tr><th>No</th><th>No. Opname</th><th>Tanggal</th><th>Gudang</th><th>PIC</th><th>Total Selisih</th></tr></thead>
<tbody><?php $no = 1; while ($row = mysqli_fetch_assoc($data)): ?><tr><td><?= $no++ ?></td><td><?= $row['no_opname'] ?></td><td><?= date('d/m/Y', strtotime($row['tanggal_opname'])) ?></td><td><?= $row['nama_gudang'] ?></td><td><?= $row['nama_karyawan'] ?></td><td><?= $row['total_selisih'] ?> item</td></tr><?php endwhile; ?></tbody></table></div>
