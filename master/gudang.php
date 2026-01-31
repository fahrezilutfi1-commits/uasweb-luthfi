<?php
// CRUD Master Gudang

if (isset($_POST['tambah'])) {
    $kode = mysqli_real_escape_string($conn, $_POST['kode_gudang']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_gudang']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $kapasitas = mysqli_real_escape_string($conn, $_POST['kapasitas']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    mysqli_query($conn, "INSERT INTO master_gudang (kode_gudang, nama_gudang, lokasi, kapasitas, status) 
                         VALUES ('$kode', '$nama', '$lokasi', '$kapasitas', '$status')");
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data gudang berhasil ditambahkan',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location='?page=gudang';
            });
          </script>";
    exit;
}

if (isset($_POST['edit'])) {
    $id = $_POST['id_gudang'];
    $kode = mysqli_real_escape_string($conn, $_POST['kode_gudang']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_gudang']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $kapasitas = mysqli_real_escape_string($conn, $_POST['kapasitas']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    mysqli_query($conn, "UPDATE master_gudang SET kode_gudang='$kode', nama_gudang='$nama', 
                         lokasi='$lokasi', kapasitas='$kapasitas', status='$status' WHERE id_gudang='$id'");
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data gudang berhasil diupdate',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location='?page=gudang';
            });
          </script>";
    exit;
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM master_gudang WHERE id_gudang='$id'");
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Terhapus!',
                text: 'Data gudang berhasil dihapus',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location='?page=gudang';
            });
          </script>";
    exit;
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM master_gudang WHERE id_gudang='$id'");
    $edit_data = mysqli_fetch_assoc($result);
}

$data = mysqli_query($conn, "SELECT * FROM master_gudang ORDER BY kode_gudang ASC");
?>

<div class="page-header">
    <h1>🏭 Master Gudang</h1>
    <p>Kelola data gudang perusahaan</p>
</div>

<div class="card">
    <h3><?= $edit_data ? 'Edit' : 'Tambah' ?> Gudang</h3>
    <form method="POST" class="form-horizontal">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id_gudang" value="<?= $edit_data['id_gudang'] ?>">
        <?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label>Kode Gudang *</label>
                <input type="text" name="kode_gudang" class="form-control" value="<?= $edit_data['kode_gudang'] ?? '' ?>" required placeholder="GD-001">
            </div>
            
            <div class="form-group">
                <label>Nama Gudang *</label>
                <input type="text" name="nama_gudang" class="form-control" value="<?= $edit_data['nama_gudang'] ?? '' ?>" required placeholder="Gudang Utama">
            </div>
        </div>
        
        <div class="form-group">
            <label>Lokasi *</label>
            <input type="text" name="lokasi" class="form-control" value="<?= $edit_data['lokasi'] ?? '' ?>" required placeholder="Jakarta Pusat">
        </div>
        
        <div class="form-group">
            <label>Kapasitas</label>
            <input type="text" name="kapasitas" class="form-control" value="<?= $edit_data['kapasitas'] ?? '' ?>" placeholder="5000 m²">
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
            <a href="?page=gudang" class="btn btn-secondary">❌ Batal</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <h3>Daftar Gudang</h3>
    
    <!-- SEARCH BOX -->
    <div class="search-box">
        <input type="text" id="searchInput" onkeyup="searchTable('searchInput', 'gudangTable')" 
               placeholder="🔍 Cari gudang (kode, nama, lokasi, kapasitas)...">
    </div>
    
    <table class="data-table" id="gudangTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Gudang</th>
                <th>Lokasi</th>
                <th>Kapasitas</th>
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
                <td><?= $row['kode_gudang'] ?></td>
                <td><?= $row['nama_gudang'] ?></td>
                <td><?= $row['lokasi'] ?></td>
                <td><?= $row['kapasitas'] ?></td>
                <td>
                    <span class="badge <?= $row['status'] == 'Aktif' ? 'badge-success' : 'badge-danger' ?>">
                        <?= $row['status'] ?>
                    </span>
                </td>
                <td>
                    <a href="?page=gudang&edit=<?= $row['id_gudang'] ?>" class="btn-action btn-edit">✏️</a>
                    <a href="#" onclick="confirmDelete('?page=gudang&hapus=<?= $row['id_gudang'] ?>'); return false;" 
                       class="btn-action btn-delete">🗑️</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>