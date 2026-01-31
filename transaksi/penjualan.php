<?php
// CRUD Transaksi Penjualan dengan Detail

// ========== PROSES TAMBAH TRANSAKSI ==========
if (isset($_POST['tambah'])) {
    $no_invoice = mysqli_real_escape_string($conn, $_POST['no_invoice']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal_penjualan']);
    $id_pelanggan = mysqli_real_escape_string($conn, $_POST['id_pelanggan']);
    $id_karyawan = mysqli_real_escape_string($conn, $_POST['id_karyawan']);
    $total = mysqli_real_escape_string($conn, $_POST['total_penjualan']);
    $status = 'Selesai';
    
    // Insert header transaksi
    mysqli_query($conn, "INSERT INTO transaksi_penjualan (no_invoice, tanggal_penjualan, id_pelanggan, id_karyawan, total_penjualan, status) 
                         VALUES ('$no_invoice', '$tanggal', '$id_pelanggan', '$id_karyawan', '$total', '$status')");
    
    $id_penjualan = mysqli_insert_id($conn);
    
    // Insert detail barang
    $id_barang_arr = $_POST['id_barang'];
    $qty_arr = $_POST['qty'];
    
    foreach ($id_barang_arr as $key => $id_barang) {
        if (!empty($id_barang) && !empty($qty_arr[$key])) {
            // Get harga from master_harga
            $harga_query = mysqli_query($conn, "SELECT harga_jual FROM master_harga WHERE id_barang='$id_barang' AND status='Aktif' LIMIT 1");
            $harga_data = mysqli_fetch_assoc($harga_query);
            $harga_jual = $harga_data['harga_jual'];
            $qty = $qty_arr[$key];
            $subtotal = $harga_jual * $qty;
            
            mysqli_query($conn, "INSERT INTO detail_penjualan (id_penjualan, id_barang, qty, harga_jual, subtotal) 
                                VALUES ('$id_penjualan', '$id_barang', '$qty', '$harga_jual', '$subtotal')");
            
            // Update stok barang
            mysqli_query($conn, "UPDATE master_barang SET stok = stok - $qty WHERE id_barang='$id_barang'");
        }
    }
    
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Transaksi penjualan berhasil ditambahkan',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location='?page=penjualan';
            });
          </script>";
    exit;
}

// ========== PROSES HAPUS TRANSAKSI ==========
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Kembalikan stok barang
    $detail_query = mysqli_query($conn, "SELECT id_barang, qty FROM detail_penjualan WHERE id_penjualan='$id'");
    while ($detail = mysqli_fetch_assoc($detail_query)) {
        mysqli_query($conn, "UPDATE master_barang SET stok = stok + {$detail['qty']} WHERE id_barang='{$detail['id_barang']}'");
    }
    
    // Hapus detail
    mysqli_query($conn, "DELETE FROM detail_penjualan WHERE id_penjualan='$id'");
    // Hapus header
    mysqli_query($conn, "DELETE FROM transaksi_penjualan WHERE id_penjualan='$id'");
    
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Terhapus!',
                text: 'Transaksi penjualan berhasil dihapus',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location='?page=penjualan';
            });
          </script>";
    exit;
}

// ========== DATA UNTUK FORM ==========
$pelanggan_list = mysqli_query($conn, "SELECT * FROM master_pelanggan WHERE status='Aktif' ORDER BY nama_pelanggan");
$karyawan_list = mysqli_query($conn, "SELECT * FROM master_karyawan WHERE status='Aktif' ORDER BY nama_karyawan");
$barang_list = mysqli_query($conn, "SELECT b.*, h.harga_jual 
                                    FROM master_barang b 
                                    LEFT JOIN master_harga h ON b.id_barang = h.id_barang AND h.status='Aktif'
                                    ORDER BY b.nama_barang");

// Generate No Invoice Otomatis
$last_invoice = mysqli_query($conn, "SELECT no_invoice FROM transaksi_penjualan ORDER BY id_penjualan DESC LIMIT 1");
if (mysqli_num_rows($last_invoice) > 0) {
    $last = mysqli_fetch_assoc($last_invoice)['no_invoice'];
    $num = (int)substr($last, 4) + 1;
} else {
    $num = 1;
}
$no_invoice_baru = 'INV-' . str_pad($num, 3, '0', STR_PAD_LEFT);

// ========== DATA TRANSAKSI ==========
$data = mysqli_query($conn, "SELECT tp.*, p.nama_pelanggan, k.nama_karyawan 
                              FROM transaksi_penjualan tp
                              JOIN master_pelanggan p ON tp.id_pelanggan = p.id_pelanggan
                              JOIN master_karyawan k ON tp.id_karyawan = k.id_karyawan
                              ORDER BY tp.tanggal_penjualan DESC");
?>

<div class="page-header">
    <h1>💳 Transaksi Penjualan</h1>
    <p>Kelola transaksi penjualan kepada pelanggan</p>
</div>

<!-- FORM TAMBAH TRANSAKSI -->
<div class="card">
    <h3>➕ Tambah Transaksi Baru</h3>
    <form method="POST" id="formPenjualan">
        <div class="form-row">
            <div class="form-group">
                <label>No. Invoice *</label>
                <input type="text" name="no_invoice" value="<?= $no_invoice_baru ?>" readonly required>
            </div>
            
            <div class="form-group">
                <label>Tanggal *</label>
                <input type="date" name="tanggal_penjualan" value="<?= date('Y-m-d') ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Pelanggan *</label>
                <select name="id_pelanggan" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    <?php while ($p = mysqli_fetch_assoc($pelanggan_list)): ?>
                        <option value="<?= $p['id_pelanggan'] ?>"><?= $p['nama_pelanggan'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Kasir *</label>
                <select name="id_karyawan" required>
                    <option value="">-- Pilih Kasir --</option>
                    <?php while ($k = mysqli_fetch_assoc($karyawan_list)): ?>
                        <option value="<?= $k['id_karyawan'] ?>"><?= $k['nama_karyawan'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        
        <hr>
        <h4>Detail Barang</h4>
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
                                <option value="<?= $b['id_barang'] ?>" data-harga="<?= $b['harga_jual'] ?>">
                                    <?= $b['nama_barang'] ?> - Rp <?= number_format($b['harga_jual'], 0, ',', '.') ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Qty *</label>
                        <input type="number" name="qty[]" min="1" class="qty-input" onchange="hitungTotal()" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Harga</label>
                        <input type="text" class="harga-display" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger" onclick="hapusRow(this)">🗑️</button>
                    </div>
                </div>
            </div>
        </div>
        
        <button type="button" class="btn btn-secondary" onclick="tambahRow()">➕ Tambah Barang</button>
        
        <hr>
        <div class="form-group">
            <label><strong>Total Penjualan:</strong></label>
            <input type="text" id="totalDisplay" value="Rp 0" readonly style="font-size: 1.5em; font-weight: bold;">
            <input type="hidden" name="total_penjualan" id="totalHidden" value="0">
        </div>
        
        <button type="submit" name="tambah" class="btn btn-primary">💾 Simpan Transaksi</button>
    </form>
</div>

<!-- DAFTAR TRANSAKSI -->
<div class="card">
    <h3>📋 Daftar Transaksi Penjualan</h3>
    
    <div class="search-box">
        <input type="text" id="searchPenjualan" onkeyup="searchTable('searchPenjualan', 'penjualanTable')" 
               placeholder="🔍 Cari transaksi...">
    </div>
    
    <div class="table-responsive">
        <table class="data-table" id="penjualanTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. Invoice</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Kasir</th>
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
                    <td><?= $row['no_invoice'] ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_penjualan'])) ?></td>
                    <td><?= $row['nama_pelanggan'] ?></td>
                    <td><?= $row['nama_karyawan'] ?></td>
                    <td>Rp <?= number_format($row['total_penjualan'], 0, ',', '.') ?></td>
                    <td><span class="badge badge-success"><?= $row['status'] ?></span></td>
                    <td class="action-buttons">
                        <a href="?page=penjualan&detail=<?= $row['id_penjualan'] ?>" class="btn btn-info btn-sm">👁️ Detail</a>
                        <a href="?page=penjualan&hapus=<?= $row['id_penjualan'] ?>" class="btn btn-danger btn-sm" 
                           onclick="return confirmDelete(this.href, 'Transaksi akan dihapus dan stok dikembalikan!')">🗑️ Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// ========== MODAL DETAIL TRANSAKSI ==========
if (isset($_GET['detail'])) {
    $id = $_GET['detail'];
    $header = mysqli_query($conn, "SELECT tp.*, p.nama_pelanggan, p.no_telp, k.nama_karyawan 
                                   FROM transaksi_penjualan tp
                                   JOIN master_pelanggan p ON tp.id_pelanggan = p.id_pelanggan
                                   JOIN master_karyawan k ON tp.id_karyawan = k.id_karyawan
                                   WHERE tp.id_penjualan='$id'");
    $h = mysqli_fetch_assoc($header);
    
    $detail = mysqli_query($conn, "SELECT dp.*, b.nama_barang, s.nama_satuan
                                   FROM detail_penjualan dp
                                   JOIN master_barang b ON dp.id_barang = b.id_barang
                                   JOIN master_satuan s ON b.id_satuan = s.id_satuan
                                   WHERE dp.id_penjualan='$id'");
?>
<div class="modal-overlay" onclick="window.location='?page=penjualan'"></div>
<div class="modal-detail">
    <div class="modal-header">
        <h2>🧾 Detail Transaksi: <?= $h['no_invoice'] ?></h2>
        <button onclick="window.location='?page=penjualan'" class="btn-close">✖</button>
    </div>
    
    <div class="modal-body">
        <div class="info-grid">
            <div class="info-item">
                <strong>Tanggal:</strong>
                <span><?= date('d F Y', strtotime($h['tanggal_penjualan'])) ?></span>
            </div>
            <div class="info-item">
                <strong>Pelanggan:</strong>
                <span><?= $h['nama_pelanggan'] ?> (<?= $h['no_telp'] ?>)</span>
            </div>
            <div class="info-item">
                <strong>Kasir:</strong>
                <span><?= $h['nama_karyawan'] ?></span>
            </div>
            <div class="info-item">
                <strong>Status:</strong>
                <span class="badge badge-success"><?= $h['status'] ?></span>
            </div>
        </div>
        
        <h4>Detail Barang:</h4>
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Satuan</th>
                    <th>Harga</th>
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
                    <td>Rp <?= number_format($d['harga_jual'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($d['subtotal'], 0, ',', '.') ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" style="text-align: right;">TOTAL:</th>
                    <th>Rp <?= number_format($h['total_penjualan'], 0, ',', '.') ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <div class="modal-footer">
        <button onclick="window.print()" class="btn btn-secondary">🖨️ Print</button>
        <button onclick="window.location='?page=penjualan'" class="btn btn-primary">Tutup</button>
    </div>
</div>
<?php } ?>

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
    row.querySelector('.harga-display').value = 'Rp ' + parseInt(harga).toLocaleString('id-ID');
    hitungTotal();
}

// Function untuk hitung total
function hitungTotal() {
    let total = 0;
    const rows = document.querySelectorAll('.detail-row');
    
    rows.forEach(row => {
        const select = row.querySelector('.barang-select');
        const qty = row.querySelector('.qty-input').value;
        
        if (select.value && qty) {
            const harga = select.options[select.selectedIndex].getAttribute('data-harga');
            total += parseInt(harga) * parseInt(qty);
        }
    });
    
    document.getElementById('totalDisplay').value = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('totalHidden').value = total;
}
</script>

<style>
.detail-row {
    margin-bottom: 10px;
    padding: 10px;
    background: #f9f9f9;
    border-radius: 5px;
}

.form-row {
    display: flex;
    gap: 15px;
    align-items: end;
}

.form-group {
    flex: 1;
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
    max-width: 900px;
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
    border-bottom: 2px solid #667eea;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    color: #667eea;
    font-size: 0.9em;
}

.info-item span {
    font-size: 1.1em;
}

@media print {
    .modal-overlay,
    .modal-footer,
    .page-header,
    .card:first-of-type {
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