<?php
// CRUD Transaksi Pembelian

// ========== PROSES TAMBAH PEMBELIAN ==========
if (isset($_POST['tambah'])) {
    $no_faktur = mysqli_real_escape_string($conn, $_POST['no_faktur']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal_pembelian']);
    $id_supplier = mysqli_real_escape_string($conn, $_POST['id_supplier']);
    $id_karyawan = isset($_SESSION['id_karyawan']) ? $_SESSION['id_karyawan'] : 1;
    $total_pembelian = mysqli_real_escape_string($conn, $_POST['total_pembelian']);
    $status = 'Selesai';
    
    // Insert header pembelian
    $query = "INSERT INTO transaksi_pembelian (no_faktur, tanggal_pembelian, id_supplier, id_karyawan, total_pembelian, status) 
              VALUES ('$no_faktur', '$tanggal', '$id_supplier', '$id_karyawan', '$total_pembelian', '$status')";
    
    mysqli_query($conn, $query);
    $id_pembelian = mysqli_insert_id($conn);
    
    // Insert detail barang
    $id_barang_arr = $_POST['id_barang'];
    $qty_arr = $_POST['qty'];
    $harga_arr = $_POST['harga_beli'];
    
    foreach ($id_barang_arr as $key => $id_barang) {
        if (!empty($id_barang) && !empty($qty_arr[$key])) {
            $qty = $qty_arr[$key];
            $harga_beli = $harga_arr[$key];
            $subtotal = $harga_beli * $qty;
            
            mysqli_query($conn, "INSERT INTO detail_pembelian (id_pembelian, id_barang, qty, harga_beli, subtotal) 
                                VALUES ('$id_pembelian', '$id_barang', '$qty', '$harga_beli', '$subtotal')");
            
            // Tambah stok barang
            mysqli_query($conn, "UPDATE master_barang SET stok = stok + $qty WHERE id_barang='$id_barang'");
        }
    }
    
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Pembelian berhasil ditambahkan',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = window.location.pathname + window.location.search.split('&')[0];
            });
          </script>";
    exit;
}

// ========== PROSES HAPUS PEMBELIAN ==========
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Kurangi stok barang
    $detail_query = mysqli_query($conn, "SELECT id_barang, qty FROM detail_pembelian WHERE id_pembelian='$id'");
    while ($detail = mysqli_fetch_assoc($detail_query)) {
        mysqli_query($conn, "UPDATE master_barang SET stok = stok - {$detail['qty']} WHERE id_barang='{$detail['id_barang']}'");
    }
    
    // Hapus detail
    mysqli_query($conn, "DELETE FROM detail_pembelian WHERE id_pembelian='$id'");
    // Hapus header
    mysqli_query($conn, "DELETE FROM transaksi_pembelian WHERE id_pembelian='$id'");
    
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Terhapus!',
                text: 'Pembelian berhasil dihapus',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = window.location.pathname + window.location.search.split('&')[0];
            });
          </script>";
    exit;
}

// ========== JIKA REQUEST AJAX UNTUK DETAIL ==========
if (isset($_GET['ajax_detail'])) {
    $id = $_GET['ajax_detail'];
    $header = mysqli_query($conn, "SELECT tp.*, s.nama_supplier, s.alamat, s.no_telp, k.nama_karyawan
                                   FROM transaksi_pembelian tp
                                   JOIN master_supplier s ON tp.id_supplier = s.id_supplier
                                   JOIN master_karyawan k ON tp.id_karyawan = k.id_karyawan
                                   WHERE tp.id_pembelian='$id'");
    $h = mysqli_fetch_assoc($header);
    
    $detail = mysqli_query($conn, "SELECT dp.*, b.nama_barang, s.nama_satuan
                                   FROM detail_pembelian dp
                                   JOIN master_barang b ON dp.id_barang = b.id_barang
                                   JOIN master_satuan s ON b.id_satuan = s.id_satuan
                                   WHERE dp.id_pembelian='$id'");
    ?>
    <div class="info-grid">
        <div class="info-item">
            <strong>No. Faktur:</strong>
            <span><?= $h['no_faktur'] ?></span>
        </div>
        <div class="info-item">
            <strong>Tanggal Pembelian:</strong>
            <span><?= date('d F Y', strtotime($h['tanggal_pembelian'])) ?></span>
        </div>
        <div class="info-item">
            <strong>Supplier:</strong>
            <span><?= $h['nama_supplier'] ?></span>
        </div>
        <div class="info-item">
            <strong>Alamat Supplier:</strong>
            <span><?= $h['alamat'] ?></span>
        </div>
        <div class="info-item">
            <strong>No. Telp:</strong>
            <span><?= $h['no_telp'] ?></span>
        </div>
        <div class="info-item">
            <strong>Petugas:</strong>
            <span><?= $h['nama_karyawan'] ?></span>
        </div>
        <div class="info-item">
            <strong>Status:</strong>
            <span class="badge badge-success"><?= $h['status'] ?></span>
        </div>
    </div>
    
    <h4 style="margin-top: 20px;">Detail Barang:</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga Beli</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($d = mysqli_fetch_assoc($detail)): 
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $d['nama_barang'] ?></td>
                <td><?= $d['qty'] ?></td>
                <td><?= $d['nama_satuan'] ?></td>
                <td>Rp <?= number_format($d['harga_beli'], 0, ',', '.') ?></td>
                <td>Rp <?= number_format($d['subtotal'], 0, ',', '.') ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align: right;">TOTAL PEMBELIAN:</th>
                <th>Rp <?= number_format($h['total_pembelian'], 0, ',', '.') ?></th>
            </tr>
        </tfoot>
    </table>
    <?php
    exit;
}

// ========== DATA UNTUK FORM ==========
$supplier_list = mysqli_query($conn, "SELECT * FROM master_supplier WHERE status='Aktif' ORDER BY nama_supplier");
$barang_list = mysqli_query($conn, "SELECT b.*, h.harga_beli, s.nama_satuan
                                    FROM master_barang b 
                                    LEFT JOIN master_harga h ON b.id_barang = h.id_barang AND h.status='Aktif'
                                    LEFT JOIN master_satuan s ON b.id_satuan = s.id_satuan
                                    ORDER BY b.nama_barang");

// Generate No Faktur Otomatis
$last_faktur = mysqli_query($conn, "SELECT no_faktur FROM transaksi_pembelian ORDER BY id_pembelian DESC LIMIT 1");
if (mysqli_num_rows($last_faktur) > 0) {
    $last = mysqli_fetch_assoc($last_faktur)['no_faktur'];
    $num = (int)substr($last, 3) + 1;
} else {
    $num = 1;
}
$no_faktur_baru = 'PB-' . str_pad($num, 5, '0', STR_PAD_LEFT);

// ========== DATA PEMBELIAN ==========
$data = mysqli_query($conn, "SELECT tp.*, s.nama_supplier, k.nama_karyawan 
                              FROM transaksi_pembelian tp
                              JOIN master_supplier s ON tp.id_supplier = s.id_supplier
                              JOIN master_karyawan k ON tp.id_karyawan = k.id_karyawan
                              ORDER BY tp.tanggal_pembelian DESC");
?>

<div class="page-header">
    <h1>🛒 Transaksi Pembelian</h1>
    <p>Kelola transaksi pembelian dari supplier</p>
</div>

<!-- FORM TAMBAH PEMBELIAN -->
<div class="card">
    <h3>➕ Tambah Pembelian Baru</h3>
    <form method="POST" id="formPembelian">
        <div class="form-row">
            <div class="form-group">
                <label>No. Faktur *</label>
                <input type="text" name="no_faktur" value="<?= $no_faktur_baru ?>" readonly required>
            </div>
            
            <div class="form-group">
                <label>Tanggal Pembelian *</label>
                <input type="date" name="tanggal_pembelian" value="<?= date('Y-m-d') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Supplier *</label>
                <select name="id_supplier" required>
                    <option value="">-- Pilih Supplier --</option>
                    <?php while ($s = mysqli_fetch_assoc($supplier_list)): ?>
                        <option value="<?= $s['id_supplier'] ?>"><?= $s['nama_supplier'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        
        <hr>
        <h4>Detail Barang yang Dibeli</h4>
        <div id="detailBarang">
            <div class="detail-row">
                <div class="form-row">
                    <div class="form-group" style="flex: 2;">
                        <label>Barang *</label>
                        <select name="id_barang[]" class="barang-select" onchange="updateHarga(this)" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php 
                            mysqli_data_seek($barang_list, 0);
                            while ($b = mysqli_fetch_assoc($barang_list)): 
                            ?>
                                <option value="<?= $b['id_barang'] ?>" 
                                        data-harga="<?= $b['harga_beli'] ?>"
                                        data-satuan="<?= $b['nama_satuan'] ?>">
                                    <?= $b['nama_barang'] ?> (<?= $b['nama_satuan'] ?>) - Rp <?= number_format($b['harga_beli'], 0, ',', '.') ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Qty *</label>
                        <input type="number" name="qty[]" min="1" class="qty-input" onchange="hitungTotal()" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Harga Beli *</label>
                        <input type="number" name="harga_beli[]" class="harga-input" onchange="hitungTotal()" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Subtotal</label>
                        <input type="text" class="subtotal-display" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm" onclick="hapusRow(this)">🗑️</button>
                    </div>
                </div>
            </div>
        </div>
        
        <button type="button" class="btn btn-secondary" onclick="tambahRow()">➕ Tambah Barang</button>
        
        <hr>
        
        <div class="form-row">
            <div class="form-group">
                <label><strong>TOTAL PEMBELIAN:</strong></label>
                <input type="text" id="totalDisplay" value="Rp 0" readonly style="font-size: 1.5em; font-weight: bold; color: #4CAF50;">
                <input type="hidden" name="total_pembelian" id="totalHidden" value="0">
            </div>
        </div>
        
        <button type="submit" name="tambah" class="btn btn-primary">💾 Simpan Pembelian</button>
    </form>
</div>

<!-- DAFTAR PEMBELIAN -->
<div class="card">
    <h3>📋 Daftar Transaksi Pembelian</h3>
    
    <div class="search-box">
        <input type="text" id="searchPembelian" onkeyup="searchTable('searchPembelian', 'pembelianTable')" 
               placeholder="🔍 Cari pembelian...">
    </div>
    
    <div class="table-responsive">
        <table class="data-table" id="pembelianTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. Faktur</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Petugas</th>
                    <th>Total</th>
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
                    <td><?= $row['no_faktur'] ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_pembelian'])) ?></td>
                    <td><?= $row['nama_supplier'] ?></td>
                    <td><?= $row['nama_karyawan'] ?></td>
                    <td>Rp <?= number_format($row['total_pembelian'], 0, ',', '.') ?></td>
                    <td><span class="badge badge-success"><?= $row['status'] ?></span></td>
                    <td class="action-buttons">
                        <a href="javascript:void(0)" onclick="lihatDetail(<?= $row['id_pembelian'] ?>)" class="btn btn-info btn-sm">👁️ Detail</a>
                        <a href="javascript:void(0)" onclick="hapusData(<?= $row['id_pembelian'] ?>)" class="btn btn-danger btn-sm">🗑️ Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL DETAIL -->
<div id="modalDetail" class="modal-overlay" style="display:none;" onclick="tutupModal()"></div>
<div id="modalContent" class="modal-detail" style="display:none;">
    <div class="modal-header">
        <h2 id="modalTitle">🧾 Detail Pembelian</h2>
        <button onclick="tutupModal()" class="btn-close">✖</button>
    </div>
    
    <div class="modal-body" id="modalBody">
        <!-- Content will be loaded here -->
    </div>
    
    <div class="modal-footer">
        <button onclick="window.print()" class="btn btn-secondary">🖨️ Print</button>
        <button onclick="tutupModal()" class="btn btn-primary">Tutup</button>
    </div>
</div>

<script>
// Function untuk tambah row barang
function tambahRow() {
    const container = document.getElementById('detailBarang');
    const firstRow = container.querySelector('.detail-row');
    const newRow = firstRow.cloneNode(true);
    
    // Reset values
    newRow.querySelectorAll('select, input').forEach(input => {
        if (input.type !== 'button') {
            input.value = '';
        }
    });
    
    container.appendChild(newRow);
}

// Function untuk hapus row
function hapusRow(btn) {
    const container = document.getElementById('detailBarang');
    if (container.querySelectorAll('.detail-row').length > 1) {
        btn.closest('.detail-row').remove();
        hitungTotal();
    } else {
        alert('Minimal harus ada 1 barang!');
    }
}

// Function untuk update harga saat pilih barang
function updateHarga(select) {
    const row = select.closest('.detail-row');
    const harga = select.options[select.selectedIndex].getAttribute('data-harga');
    row.querySelector('.harga-input').value = harga || 0;
    hitungTotal();
}

// Function untuk hitung total
function hitungTotal() {
    let grandTotal = 0;
    const rows = document.querySelectorAll('.detail-row');
    
    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const harga = parseFloat(row.querySelector('.harga-input').value) || 0;
        const subtotal = qty * harga;
        
        row.querySelector('.subtotal-display').value = 'Rp ' + subtotal.toLocaleString('id-ID');
        grandTotal += subtotal;
    });
    
    document.getElementById('totalDisplay').value = 'Rp ' + grandTotal.toLocaleString('id-ID');
    document.getElementById('totalHidden').value = grandTotal;
}

// Function untuk search table
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toUpperCase();
    const table = document.getElementById(tableId);
    const tr = table.getElementsByTagName('tr');
    
    for (let i = 1; i < tr.length; i++) {
        let found = false;
        const td = tr[i].getElementsByTagName('td');
        
        for (let j = 0; j < td.length; j++) {
            if (td[j]) {
                if (td[j].innerHTML.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        tr[i].style.display = found ? '' : 'none';
    }
}

// Function untuk lihat detail dengan AJAX
function lihatDetail(id) {
    const url = window.location.pathname + window.location.search.split('&')[0] + '&ajax_detail=' + id;
    
    fetch(url)
        .then(response => response.text())
        .then(data => {
            document.getElementById('modalBody').innerHTML = data;
            document.getElementById('modalDetail').style.display = 'block';
            document.getElementById('modalContent').style.display = 'block';
        })
        .catch(error => {
            alert('Gagal memuat detail: ' + error);
        });
}

// Function untuk tutup modal
function tutupModal() {
    document.getElementById('modalDetail').style.display = 'none';
    document.getElementById('modalContent').style.display = 'none';
}

// Function untuk hapus data
function hapusData(id) {
    Swal.fire({
        title: 'Yakin hapus?',
        text: "Pembelian akan dihapus dan stok akan dikurangi!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = window.location.pathname + window.location.search.split('&')[0] + '&hapus=' + id;
        }
    });
}
</script>

<style>
.page-header {
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 3px solid #2196F3;
}

.page-header h1 {
    margin: 0 0 10px 0;
    color: #333;
}

.page-header p {
    margin: 0;
    color: #666;
}

.card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.card h3 {
    margin-top: 0;
    color: #333;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.detail-row {
    margin-bottom: 10px;
    padding: 10px;
    background: #e3f2fd;
    border-radius: 5px;
    border-left: 4px solid #2196F3;
}

.form-row {
    display: flex;
    gap: 15px;
    align-items: end;
    margin-bottom: 15px;
}

.form-group {
    flex: 1;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #333;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #2196F3;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #6c757d;
    color: white;
    margin-right: 10px;
}

.btn-secondary:hover {
    background: #5a6268;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
}

.btn-info {
    background: #17a2b8;
    color: white;
}

.btn-info:hover {
    background: #138496;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 12px;
}

.search-box {
    margin-bottom: 20px;
}

.search-box input {
    width: 100%;
    padding: 12px 20px;
    border: 2px solid #ddd;
    border-radius: 25px;
    font-size: 14px;
    transition: all 0.3s;
}

.search-box input:focus {
    outline: none;
    border-color: #2196F3;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
}

.table-responsive {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.data-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.data-table th,
.data-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.data-table tbody tr:hover {
    background: #f5f5f5;
}

.action-buttons {
    display: flex;
    gap: 5px;
}

.badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
}

.badge-success {
    background: #28a745;
    color: white;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 999;
}

.modal-detail {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 0;
    border-radius: 12px;
    max-width: 1000px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 30px;
    border-bottom: 2px solid #2196F3;
    background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
    color: white;
    border-radius: 12px 12px 0 0;
}

.modal-header h2 {
    margin: 0;
}

.btn-close {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    transition: all 0.3s;
}

.btn-close:hover {
    background: rgba(255,255,255,0.3);
    transform: rotate(90deg);
}

.modal-body {
    padding: 30px;
}

.modal-footer {
    padding: 20px 30px;
    border-top: 1px solid #eee;
    text-align: right;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.info-item strong {
    color: #2196F3;
    font-size: 0.9em;
}

.info-item span {
    font-size: 1.1em;
}

hr {
    border: none;
    border-top: 2px solid #f0f0f0;
    margin: 20px 0;
}

@media print {
    .modal-overlay,
    .modal-footer,
    .page-header,
    .card:first-of-type,
    .btn {
        display: none !important;
    }
    
    .modal-detail {
        position: static;
        transform: none;
        max-width: 100%;
        box-shadow: none;
    }
}
</style>