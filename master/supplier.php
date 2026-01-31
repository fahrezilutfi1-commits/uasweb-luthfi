<?php
// CRUD Master Supplier

if (isset($_POST['tambah'])) {
    $kode = mysqli_real_escape_string($conn, $_POST['kode_supplier']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    mysqli_query($conn, "INSERT INTO master_supplier (kode_supplier, nama_supplier, alamat, no_telp, email, status) 
                         VALUES ('$kode', '$nama', '$alamat', '$no_telp', '$email', '$status')");
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data supplier berhasil ditambahkan',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location='?page=supplier';
            });
          </script>";
    exit;
}

if (isset($_POST['edit'])) {
    $id = $_POST['id_supplier'];
    $kode = mysqli_real_escape_string($conn, $_POST['kode_supplier']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    mysqli_query($conn, "UPDATE master_supplier SET kode_supplier='$kode', nama_supplier='$nama', 
                         alamat='$alamat', no_telp='$no_telp', email='$email', status='$status' WHERE id_supplier='$id'");
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data supplier berhasil diupdate',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location='?page=supplier';
            });
          </script>";
    exit;
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM master_supplier WHERE id_supplier='$id'");
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Terhapus!',
                text: 'Data supplier berhasil dihapus',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location='?page=supplier';
            });
          </script>";
    exit;
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM master_supplier WHERE id_supplier='$id'");
    $edit_data = mysqli_fetch_assoc($result);
}

$data = mysqli_query($conn, "SELECT * FROM master_supplier ORDER BY kode_supplier ASC");
?>

<div class="page-header">
    <h1>🚚 Master Supplier</h1>
    <p>Kelola data supplier perusahaan</p>
</div>

<div class="card">
    <h3><?= $edit_data ? 'Edit' : 'Tambah' ?> Supplier</h3>
    <form method="POST" class="form-horizontal">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id_supplier" value="<?= $edit_data['id_supplier'] ?>">
        <?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label>Kode Supplier *</label>
                <input type="text" name="kode_supplier" class="form-control" value="<?= $edit_data['kode_supplier'] ?? '' ?>" required placeholder="SPL-001">
            </div>
            
            <div class="form-group">
                <label>Nama Supplier *</label>
                <input type="text" name="nama_supplier" class="form-control" value="<?= $edit_data['nama_supplier'] ?? '' ?>" required placeholder="PT. Nama Supplier">
            </div>
        </div>
        
        <div class="form-group">
            <label>Alamat *</label>
            <textarea name="alamat" class="form-control" rows="3" required placeholder="Alamat lengkap"><?= $edit_data['alamat'] ?? '' ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>No. Telepon *</label>
                <input type="text" name="no_telp" class="form-control" value="<?= $edit_data['no_telp'] ?? '' ?>" required placeholder="021-xxxxxxxx">
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= $edit_data['email'] ?? '' ?>" placeholder="supplier@example.com">
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
            <a href="?page=supplier" class="btn btn-secondary">❌ Batal</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <h3>Daftar Supplier</h3>
    
    <!-- SEARCH BOX -->
    <div class="search-box">
        <input type="text" id="searchInput" onkeyup="searchTable('searchInput', 'supplierTable')" 
               placeholder="🔍 Cari supplier (kode, nama, alamat, telepon, email)...">
    </div>
    
    <table class="data-table" id="supplierTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Supplier</th>
                <th>Alamat</th>
                <th>No. Telepon</th>
                <th>Email</th>
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
                <td><?= $row['kode_supplier'] ?></td>
                <td><?= $row['nama_supplier'] ?></td>
                <td><?= $row['alamat'] ?></td>
                <td><?= $row['no_telp'] ?></td>
                <td><?= $row['email'] ?></td>
                <td>
                    <span class="badge <?= $row['status'] == 'Aktif' ? 'badge-success' : 'badge-danger' ?>">
                        <?= $row['status'] ?>
                    </span>
                </td>
                <td>
                    <a href="?page=supplier&edit=<?= $row['id_supplier'] ?>" class="btn-action btn-edit">✏️</a>
                    <a href="#" onclick="confirmDelete('?page=supplier&hapus=<?= $row['id_supplier'] ?>'); return false;" 
                       class="btn-action btn-delete">🗑️</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>