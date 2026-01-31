<?php
// File: master/kategori.php
// CRUD Master Kategori

// PROSES TAMBAH DATA
if (isset($_POST['tambah'])) {
    $kode = mysqli_real_escape_string($conn, $_POST['kode_kategori']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $query = "INSERT INTO master_kategori (kode_kategori, nama_kategori, deskripsi, status) 
              VALUES ('$kode', '$nama', '$deskripsi', '$status')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data kategori berhasil ditambahkan!'); window.location='?page=kategori';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// PROSES EDIT DATA
if (isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id_kategori']);
    $kode = mysqli_real_escape_string($conn, $_POST['kode_kategori']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $query = "UPDATE master_kategori SET 
              kode_kategori='$kode', 
              nama_kategori='$nama', 
              deskripsi='$deskripsi', 
              status='$status' 
              WHERE id_kategori='$id'";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data kategori berhasil diupdate!'); window.location='?page=kategori';</script>";
    }
}

// PROSES HAPUS DATA
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    $query = "DELETE FROM master_kategori WHERE id_kategori='$id'";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data kategori berhasil dihapus!'); window.location='?page=kategori';</script>";
    }
}

// AMBIL DATA UNTUK EDIT
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = mysqli_real_escape_string($conn, $_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM master_kategori WHERE id_kategori='$id'");
    $edit_data = mysqli_fetch_assoc($result);
}

// AMBIL SEMUA DATA KATEGORI
$data_kategori = mysqli_query($conn, "SELECT * FROM master_kategori ORDER BY kode_kategori ASC");
?>

<div class="page-header">
    <h1>Master Kategori</h1>
    <p>Kelola data kategori barang</p>
</div>

<div class="card">
    <h3><?= $edit_data ? 'Edit' : 'Tambah' ?> Kategori</h3>
    <form method="POST" class="form-horizontal">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id_kategori" value="<?= $edit_data['id_kategori'] ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Kode Kategori *</label>
            <input type="text" name="kode_kategori" class="form-control" 
                   value="<?= $edit_data['kode_kategori'] ?? '' ?>" required 
                   placeholder="Contoh: KAT-001">
        </div>
        
        <div class="form-group">
            <label>Nama Kategori *</label>
            <input type="text" name="nama_kategori" class="form-control" 
                   value="<?= $edit_data['nama_kategori'] ?? '' ?>" required 
                   placeholder="Contoh: Sembako">
        </div>
        
        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3" 
                      placeholder="Deskripsi kategori"><?= $edit_data['deskripsi'] ?? '' ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Status *</label>
            <select name="status" class="form-control" required>
                <option value="Aktif" <?= ($edit_data['status'] ?? '') == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                <option value="Tidak Aktif" <?= ($edit_data['status'] ?? '') == 'Tidak Aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
            </select>
        </div>
        
        <div class="form-actions">
            <button type="submit" name="<?= $edit_data ? 'edit' : 'tambah' ?>" class="btn btn-primary">
                <?= $edit_data ? '✏️ Update' : '➕ Tambah' ?>
            </button>
            <?php if ($edit_data): ?>
                <a href="?page=kategori" class="btn btn-secondary">❌ Batal</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <h3>Daftar Kategori</h3>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Kategori</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = mysqli_fetch_assoc($data_kategori)): 
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['kode_kategori'] ?></td>
                    <td><?= $row['nama_kategori'] ?></td>
                    <td><?= $row['deskripsi'] ?></td>
                    <td>
                        <span class="badge <?= $row['status'] == 'Aktif' ? 'badge-success' : 'badge-danger' ?>">
                            <?= $row['status'] ?>
                        </span>
                    </td>
                    <td>
                        <a href="?page=kategori&edit=<?= $row['id_kategori'] ?>" class="btn-action btn-edit">✏️ Edit</a>
                        <a href="?page=kategori&hapus=<?= $row['id_kategori'] ?>" 
                           class="btn-action btn-delete" 
                           onclick="return confirm('Yakin ingin menghapus data ini?')">🗑️ Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
