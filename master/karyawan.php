<?php
// CRUD Master Karyawan

if (isset($_POST['tambah'])) {
    $kode = mysqli_real_escape_string($conn, $_POST['kode_karyawan']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_karyawan']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    mysqli_query($conn, "INSERT INTO master_karyawan (kode_karyawan, nama_karyawan, jabatan, no_telp, status) 
                         VALUES ('$kode', '$nama', '$jabatan', '$no_telp', '$status')");
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data karyawan berhasil ditambahkan',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location='?page=karyawan';
            });
          </script>";
    exit;
}

if (isset($_POST['edit'])) {
    $id = $_POST['id_karyawan'];
    $kode = mysqli_real_escape_string($conn, $_POST['kode_karyawan']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_karyawan']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    mysqli_query($conn, "UPDATE master_karyawan SET kode_karyawan='$kode', nama_karyawan='$nama', 
                         jabatan='$jabatan', no_telp='$no_telp', status='$status' WHERE id_karyawan='$id'");
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data karyawan berhasil diupdate',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location='?page=karyawan';
            });
          </script>";
    exit;
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM master_karyawan WHERE id_karyawan='$id'");
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Terhapus!',
                text: 'Data karyawan berhasil dihapus',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location='?page=karyawan';
            });
          </script>";
    exit;
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM master_karyawan WHERE id_karyawan='$id'");
    $edit_data = mysqli_fetch_assoc($result);
}

$data = mysqli_query($conn, "SELECT * FROM master_karyawan ORDER BY kode_karyawan ASC");
?>

<div class="page-header">
    <h1>👥 Master Karyawan</h1>
    <p>Kelola data karyawan perusahaan</p>
</div>

<div class="card">
    <h3><?= $edit_data ? 'Edit' : 'Tambah' ?> Karyawan</h3>
    <form method="POST" class="form-horizontal">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id_karyawan" value="<?= $edit_data['id_karyawan'] ?>">
        <?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label>Kode Karyawan *</label>
                <input type="text" name="kode_karyawan" class="form-control" value="<?= $edit_data['kode_karyawan'] ?? '' ?>" required placeholder="KR-001">
            </div>
            
            <div class="form-group">
                <label>Nama Karyawan *</label>
                <input type="text" name="nama_karyawan" class="form-control" value="<?= $edit_data['nama_karyawan'] ?? '' ?>" required placeholder="Nama lengkap">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Jabatan *</label>
                <select name="jabatan" class="form-control" required>
                    <option value="">Pilih Jabatan</option>
                    <option value="Admin" <?= ($edit_data['jabatan'] ?? '') == 'Admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="Kasir" <?= ($edit_data['jabatan'] ?? '') == 'Kasir' ? 'selected' : '' ?>>Kasir</option>
                    <option value="Gudang" <?= ($edit_data['jabatan'] ?? '') == 'Gudang' ? 'selected' : '' ?>>Gudang</option>
                    <option value="Supervisor" <?= ($edit_data['jabatan'] ?? '') == 'Supervisor' ? 'selected' : '' ?>>Supervisor</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>No. Telepon *</label>
                <input type="text" name="no_telp" class="form-control" value="<?= $edit_data['no_telp'] ?? '' ?>" required placeholder="08xx-xxxx-xxxx">
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
            <a href="?page=karyawan" class="btn btn-secondary">❌ Batal</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <h3>Daftar Karyawan</h3>
    
    <!-- SEARCH BOX -->
    <div class="search-box">
        <input type="text" id="searchInput" onkeyup="searchTable('searchInput', 'karyawanTable')" 
               placeholder="🔍 Cari karyawan (kode, nama, jabatan, telepon)...">
    </div>
    
    <table class="data-table" id="karyawanTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Karyawan</th>
                <th>Jabatan</th>
                <th>No. Telepon</th>
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
                <td><?= $row['kode_karyawan'] ?></td>
                <td><?= $row['nama_karyawan'] ?></td>
                <td><?= $row['jabatan'] ?></td>
                <td><?= $row['no_telp'] ?></td>
                <td>
                    <span class="badge <?= $row['status'] == 'Aktif' ? 'badge-success' : 'badge-danger' ?>">
                        <?= $row['status'] ?>
                    </span>
                </td>
                <td>
                    <a href="?page=karyawan&edit=<?= $row['id_karyawan'] ?>" class="btn-action btn-edit">✏️</a>
                    <a href="#" onclick="confirmDelete('?page=karyawan&hapus=<?= $row['id_karyawan'] ?>'); return false;" 
                       class="btn-action btn-delete">🗑️</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>