<?php
// CRUD Master Satuan

if (isset($_POST['tambah'])) {
    $kode = mysqli_real_escape_string($conn, $_POST['kode_satuan']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_satuan']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    mysqli_query($conn, "INSERT INTO master_satuan (kode_satuan, nama_satuan, deskripsi, status) VALUES ('$kode', '$nama', '$deskripsi', '$status')");
    echo "<script>alert('Data berhasil ditambahkan!'); window.location='?page=satuan';</script>";
}

if (isset($_POST['edit'])) {
    $id = $_POST['id_satuan'];
    $kode = mysqli_real_escape_string($conn, $_POST['kode_satuan']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_satuan']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    mysqli_query($conn, "UPDATE master_satuan SET kode_satuan='$kode', nama_satuan='$nama', deskripsi='$deskripsi', status='$status' WHERE id_satuan='$id'");
    echo "<script>alert('Data berhasil diupdate!'); window.location='?page=satuan';</script>";
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM master_satuan WHERE id_satuan='$id'");
    echo "<script>alert('Data berhasil dihapus!'); window.location='?page=satuan';</script>";
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM master_satuan WHERE id_satuan='$id'");
    $edit_data = mysqli_fetch_assoc($result);
}

$data_satuan = mysqli_query($conn, "SELECT * FROM master_satuan ORDER BY kode_satuan ASC");
?>

<div class="page-header">
    <h1>Master Satuan</h1>
    <p>Kelola satuan barang (Pcs, Kg, Liter, dll)</p>
</div>

<div class="card">
    <h3><?= $edit_data ? 'Edit' : 'Tambah' ?> Satuan</h3>
    <form method="POST" class="form-horizontal">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id_satuan" value="<?= $edit_data['id_satuan'] ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Kode Satuan *</label>
            <input type="text" name="kode_satuan" class="form-control" value="<?= $edit_data['kode_satuan'] ?? '' ?>" required placeholder="SAT-001">
        </div>
        
        <div class="form-group">
            <label>Nama Satuan *</label>
            <input type="text" name="nama_satuan" class="form-control" value="<?= $edit_data['nama_satuan'] ?? '' ?>" required placeholder="Pcs">
        </div>
        
        <div class="form-group">
            <label>Deskripsi</label>
            <input type="text" name="deskripsi" class="form-control" value="<?= $edit_data['deskripsi'] ?? '' ?>" placeholder="Pieces / Buah">
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
            <a href="?page=satuan" class="btn btn-secondary">❌ Batal</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <h3>Daftar Satuan</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Satuan</th>
                <th>Nama Satuan</th>
                <th>Deskripsi</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($row = mysqli_fetch_assoc($data_satuan)): 
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['kode_satuan'] ?></td>
                <td><?= $row['nama_satuan'] ?></td>
                <td><?= $row['deskripsi'] ?></td>
                <td><span class="badge <?= $row['status'] == 'Aktif' ? 'badge-success' : 'badge-danger' ?>"><?= $row['status'] ?></span></td>
                <td>
                    <a href="?page=satuan&edit=<?= $row['id_satuan'] ?>" class="btn-action btn-edit">✏️</a>
                    <a href="?page=satuan&hapus=<?= $row['id_satuan'] ?>" class="btn-action btn-delete" onclick="return confirm('Yakin hapus?')">🗑️</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
