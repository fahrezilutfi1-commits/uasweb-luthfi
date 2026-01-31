<?php
// CRUD Master Barang (dengan relasi kategori dan satuan)

if (isset($_POST['tambah'])) {
    $kode = mysqli_real_escape_string($conn, $_POST['kode_barang']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $kategori = mysqli_real_escape_string($conn, $_POST['id_kategori']);
    $satuan = mysqli_real_escape_string($conn, $_POST['id_satuan']);
    $stok = mysqli_real_escape_string($conn, $_POST['stok']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    mysqli_query($conn, "INSERT INTO master_barang (kode_barang, nama_barang, id_kategori, id_satuan, stok, deskripsi) 
                         VALUES ('$kode', '$nama', '$kategori', '$satuan', '$stok', '$deskripsi')");
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data barang berhasil ditambahkan',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location='?page=barang';
            });
          </script>";
    exit;
}

if (isset($_POST['edit'])) {
    $id = $_POST['id_barang'];
    $kode = mysqli_real_escape_string($conn, $_POST['kode_barang']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $kategori = mysqli_real_escape_string($conn, $_POST['id_kategori']);
    $satuan = mysqli_real_escape_string($conn, $_POST['id_satuan']);
    $stok = mysqli_real_escape_string($conn, $_POST['stok']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    mysqli_query($conn, "UPDATE master_barang SET kode_barang='$kode', nama_barang='$nama', id_kategori='$kategori', 
                         id_satuan='$satuan', stok='$stok', deskripsi='$deskripsi' WHERE id_barang='$id'");
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data barang berhasil diupdate',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location='?page=barang';
            });
          </script>";
    exit;
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM master_barang WHERE id_barang='$id'");
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Terhapus!',
                text: 'Data barang berhasil dihapus',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location='?page=barang';
            });
          </script>";
    exit;
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM master_barang WHERE id_barang='$id'");
    $edit_data = mysqli_fetch_assoc($result);
}

// Query dengan JOIN
$data = mysqli_query($conn, "SELECT b.*, k.nama_kategori, s.nama_satuan 
                              FROM master_barang b
                              JOIN master_kategori k ON b.id_kategori = k.id_kategori
                              JOIN master_satuan s ON b.id_satuan = s.id_satuan
                              ORDER BY b.kode_barang ASC");

// Ambil data untuk dropdown
$kategori_list = mysqli_query($conn, "SELECT * FROM master_kategori WHERE status='Aktif'");
$satuan_list = mysqli_query($conn, "SELECT * FROM master_satuan WHERE status='Aktif'");
?>

<div class="page-header">
    <h1>Master Barang</h1>
    <p>Kelola data barang perusahaan</p>
</div>

<div class="card">
    <h3><?= $edit_data ? 'Edit' : 'Tambah' ?> Barang</h3>
    <form method="POST" class="form-horizontal">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id_barang" value="<?= $edit_data['id_barang'] ?>">
        <?php endif; ?>
        
        <div class="form-row">
            <div class="form-group">
                <label>Kode Barang *</label>
                <input type="text" name="kode_barang" class="form-control" value="<?= $edit_data['kode_barang'] ?? '' ?>" required placeholder="BRG-001">
            </div>
            
            <div class="form-group">
                <label>Nama Barang *</label>
                <input type="text" name="nama_barang" class="form-control" value="<?= $edit_data['nama_barang'] ?? '' ?>" required placeholder="Nama barang">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Kategori *</label>
                <select name="id_kategori" class="form-control" required>
                    <option value="">Pilih Kategori</option>
                    <?php 
                    mysqli_data_seek($kategori_list, 0);
                    while($k = mysqli_fetch_assoc($kategori_list)): 
                    ?>
                        <option value="<?= $k['id_kategori'] ?>" <?= ($edit_data['id_kategori'] ?? '') == $k['id_kategori'] ? 'selected' : '' ?>>
                            <?= $k['nama_kategori'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Satuan *</label>
                <select name="id_satuan" class="form-control" required>
                    <option value="">Pilih Satuan</option>
                    <?php 
                    mysqli_data_seek($satuan_list, 0);
                    while($s = mysqli_fetch_assoc($satuan_list)): 
                    ?>
                        <option value="<?= $s['id_satuan'] ?>" <?= ($edit_data['id_satuan'] ?? '') == $s['id_satuan'] ? 'selected' : '' ?>>
                            <?= $s['nama_satuan'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label>Stok *</label>
            <input type="number" name="stok" class="form-control" value="<?= $edit_data['stok'] ?? '0' ?>" required>
        </div>
        
        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi barang"><?= $edit_data['deskripsi'] ?? '' ?></textarea>
        </div>
        
        <button type="submit" name="<?= $edit_data ? 'edit' : 'tambah' ?>" class="btn btn-primary">
            <?= $edit_data ? '✏️ Update' : '➕ Tambah' ?>
        </button>
        <?php if ($edit_data): ?>
            <a href="?page=barang" class="btn btn-secondary">❌ Batal</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <h3>Daftar Barang</h3>
    
    <!-- SEARCH BOX -->
    <div class="search-box">
        <input type="text" id="searchInput" onkeyup="searchTable('searchInput', 'barangTable')" 
               placeholder="🔍 Cari barang (kode, nama, kategori, satuan)...">
    </div>
    
    <table class="data-table" id="barangTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th>Stok</th>
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
                <td><?= $row['nama_kategori'] ?></td>
                <td><?= $row['nama_satuan'] ?></td>
                <td><?= $row['stok'] ?></td>
                <td>
                    <a href="?page=barang&edit=<?= $row['id_barang'] ?>" class="btn-action btn-edit">✏️</a>
                    <a href="#" onclick="confirmDelete('?page=barang&hapus=<?= $row['id_barang'] ?>'); return false;" 
                       class="btn-action btn-delete">🗑️</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
