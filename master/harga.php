<?php
// CRUD Master Harga

if (isset($_POST['tambah'])) {
    $barang = mysqli_real_escape_string($conn, $_POST['id_barang']);
    $beli = mysqli_real_escape_string($conn, $_POST['harga_beli']);
    $jual = mysqli_real_escape_string($conn, $_POST['harga_jual']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    mysqli_query($conn, "INSERT INTO master_harga (id_barang, harga_beli, harga_jual, status) VALUES ('$barang', '$beli', '$jual', '$status')");
    echo "<script>alert('Data berhasil ditambahkan!'); window.location='?page=harga';</script>";
}

if (isset($_POST['edit'])) {
    $id = $_POST['id_harga'];
    $barang = mysqli_real_escape_string($conn, $_POST['id_barang']);
    $beli = mysqli_real_escape_string($conn, $_POST['harga_beli']);
    $jual = mysqli_real_escape_string($conn, $_POST['harga_jual']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    mysqli_query($conn, "UPDATE master_harga SET id_barang='$barang', harga_beli='$beli', harga_jual='$jual', status='$status' WHERE id_harga='$id'");
    echo "<script>alert('Data berhasil diupdate!'); window.location='?page=harga';</script>";
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM master_harga WHERE id_harga='$id'");
    echo "<script>alert('Data berhasil dihapus!'); window.location='?page=harga';</script>";
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM master_harga WHERE id_harga='$id'");
    $edit_data = mysqli_fetch_assoc($result);
}

$data = mysqli_query($conn, "SELECT h.*, b.kode_barang, b.nama_barang FROM master_harga h JOIN master_barang b ON h.id_barang = b.id_barang ORDER BY b.kode_barang ASC");
$barang_list = mysqli_query($conn, "SELECT * FROM master_barang");
?>

<div class="page-header">
    <h1>Master Harga</h1>
    <p>Kelola harga beli dan jual barang</p>
</div>

<div class="card">
    <h3><?= $edit_data ? 'Edit' : 'Tambah' ?> Harga</h3>
    <form method="POST" class="form-horizontal">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id_harga" value="<?= $edit_data['id_harga'] ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Barang *</label>
            <select name="id_barang" class="form-control" required>
                <option value="">Pilih Barang</option>
                <?php while($b = mysqli_fetch_assoc($barang_list)): ?>
                    <option value="<?= $b['id_barang'] ?>" <?= ($edit_data['id_barang'] ?? '') == $b['id_barang'] ? 'selected' : '' ?>>
                        <?= $b['kode_barang'] ?> - <?= $b['nama_barang'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Harga Beli *</label>
                <input type="number" name="harga_beli" class="form-control" value="<?= $edit_data['harga_beli'] ?? '' ?>" required>
            </div>
            
            <div class="form-group">
                <label>Harga Jual *</label>
                <input type="number" name="harga_jual" class="form-control" value="<?= $edit_data['harga_jual'] ?? '' ?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label>Status *</label>
            <select name="status" class="form-control" required>
                <option value="Aktif" <?= ($edit_data['status'] ?? '') == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                <option value="Tidak Aktif" <?= ($edit_data['status'] ?? '') == 'Tidak Aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
            </select>
        </div>
        
        <button type="submit" name="<?= $edit_data ? 'edit' : 'tambah' ?>" class="btn btn-primary">
            <?= $edit_data ? '✏️ Update' : '➕ Tambah' ?>
        </button>
        <?php if ($edit_data): ?>
            <a href="?page=harga" class="btn btn-secondary">❌ Batal</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <h3>Daftar Harga</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th>Margin (%)</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = mysqli_fetch_assoc($data)): 
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['kode_barang'] ?></td>
                <td><?= $row['nama_barang'] ?></td>
                <td>Rp <?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
                <td>Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                <td><?= number_format($row['margin'], 2) ?>%</td>
                <td><span class="badge <?= $row['status'] == 'Aktif' ? 'badge-success' : 'badge-danger' ?>"><?= $row['status'] ?></span></td>
                <td>
                    <a href="?page=harga&edit=<?= $row['id_harga'] ?>" class="btn-action btn-edit">✏️</a>
                    <a href="?page=harga&hapus=<?= $row['id_harga'] ?>" class="btn-action btn-delete" onclick="return confirm('Yakin hapus?')">🗑️</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
