<?php
// CRUD Retur Pembelian

// ========== PROSES TAMBAH RETUR ==========
if (isset($_POST['tambah'])) {
    $no_retur = mysqli_real_escape_string($conn, $_POST['no_retur']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal_retur']);
    $id_supplier = mysqli_real_escape_string($conn, $_POST['id_supplier']);
    $id_karyawan = 1;
    $total_barang = mysqli_real_escape_string($conn, $_POST['total_barang']);
    $total_nilai = mysqli_real_escape_string($conn, $_POST['total_nilai']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $status = 'Proses';
    
    // Insert header retur
    mysqli_query($conn, "INSERT INTO retur_pembelian (no_retur, tanggal_retur, id_supplier, id_karyawan, total_barang, total_nilai, keterangan, status) 
                         VALUES ('$no_retur', '$tanggal', '$id_supplier', '$id_karyawan', '$total_barang', '$total_nilai', '$keterangan', '$status')");
    
    $id_retur = mysqli_insert_id($conn);
    
    // Insert detail barang
    $id_barang_arr = $_POST['id_barang'];
    $qty_arr = $_POST['qty'];
    $alasan_arr = $_POST['alasan'];
    
    foreach ($id_barang_arr as $key => $id_barang) {
        if (!empty($id_barang) && !empty($qty_arr[$key])) {
            // Get harga beli from master_harga
            $harga_query = mysqli_query($conn, "SELECT harga_beli FROM master_harga WHERE id_barang='$id_barang' AND status='Aktif' LIMIT 1");
            $harga_data = mysqli_fetch_assoc($harga_query);
            $harga_beli = $harga_data['harga_beli'];
            $qty = $qty_arr[$key];
            $subtotal = $harga_beli * $qty;
            $alasan = mysqli_real_escape_string($conn, $alasan_arr[$key]);
            
            mysqli_query($conn, "INSERT INTO detail_retur_pembelian (id_retur_pembelian, id_barang, qty, harga_beli, subtotal, alasan) 
                                VALUES ('$id_retur', '$id_barang', '$qty', '$harga_beli', '$subtotal', '$alasan')");
            
            // Kurangi stok barang (karena diretur ke supplier)
            mysqli_query($conn, "UPDATE master_barang SET stok = stok - $qty WHERE id_barang='$id_barang'");
        }
    }
    
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Retur pembelian berhasil ditambahkan',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location='?page=retur_pembelian';
            });
          </script>";
    exit;
}

// ========== PROSES HAPUS RETUR ==========
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Kembalikan stok barang
    $detail_query = mysqli_query($conn, "SELECT id_barang, qty FROM detail_retur_pembelian WHERE id_retur_pembelian='$id'");
    while ($detail = mysqli_fetch_assoc($detail_query)) {
        mysqli_query($conn, "UPDATE master_barang SET stok = stok + {$detail['qty']} WHERE id_barang='{$detail['id_barang']}'");
    }
    
    // Hapus detail
    mysqli_query($conn, "DELETE FROM detail_retur_pembelian WHERE id_retur_pembelian='$id'");
    // Hapus header
    mysqli_query($conn, "DELETE FROM retur_pembelian WHERE id_retur_pembelian='$id'");
    
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Terhapus!',
                text: 'Retur pembelian berhasil dihapus',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location='?page=retur_pembelian';
            });
          </script>";
    exit;
}

// ========== PROSES UPDATE STATUS ==========
if (isset($_GET['selesai'])) {
    $id = $_GET['selesai'];
    mysqli_query($conn, "UPDATE retur_pembelian SET status='Selesai' WHERE id_retur_pembelian='$id'");
    
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Status retur diupdate menjadi Selesai',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location='?page=retur_pembelian';
            });
          </script>";
    exit;
}

// ========== DATA UNTUK FORM ==========
$supplier_list = mysqli_query($conn, "SELECT * FROM master_supplier WHERE status='Aktif' ORDER BY nama_supplier");
$barang_list = mysqli_query($conn, "SELECT b.*, h.harga_beli 
                                    FROM master_barang b 
                                    LEFT JOIN master_harga h ON b.id_barang = h.id_barang AND h.status='Aktif'
                                    ORDER BY b.nama_barang");

// Generate No Retur Otomatis
$last_retur = mysqli_query($conn, "SELECT no_retur FROM retur_pembelian ORDER BY id_retur_pembelian DESC LIMIT 1");
if (mysqli_num_rows($last_retur) > 0) {
    $last = mysqli_fetch_assoc($last_retur)['no_retur'];
    $num = (int)substr($last, 4) + 1;
} else {
    $num = 1;
}
$no_retur_baru = 'RTP-' . str_pad($num, 3, '0', STR_PAD_LEFT);

// ========== DATA RETUR ==========
$data = mysqli_query($conn, "SELECT rp.*, s.nama_supplier 
                              FROM retur_pembelian rp 
                              JOIN master_supplier s ON rp.id_supplier = s.id_supplier 
                              ORDER BY rp.tanggal_retur DESC");
?>

<div class="page-header">
    <h1>↩️ Retur Pembelian</h1>
    <p>Kelola pengembalian barang ke supplier</p>
</div>

<!-- FORM TAMBAH RETUR -->
<div class="card">
    <h3>➕ Tambah Retur Pembelian</h3>
    <form method="POST" id="formRetur">
        <div class="form-row">
            <div class="form-group">
                <label>No. Retur *</label>
                <input type="text" name="no_retur" value="<?= $no_retur_baru ?>" readonly required>
            </div>
            
            <div class="form-group">
                <label>Tanggal Retur *</label>
                <input type="date" name="tanggal_retur" value="<?= date('Y-m-d') ?>" required>
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
        <h4>Detail Barang yang Diretur</h4>
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
                                <option value="<?= $b['id_barang'] ?>" data-harga="<?= $b['harga_beli'] ?>">
                                    <?= $b['nama_barang'] ?> - Rp <?= number_format($b['harga_beli'], 0, ',', '.') ?>
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
                    
                    <div class="form-group" style="flex: 2;">
                        <label>Alasan Retur *</label>
                        <input type="text" name="alasan[]" placeholder="Misal: Rusak, Cacat, Kadaluarsa" required>
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
        
        <div class="form-row">
            <div class="form-group">
                <label>Total Barang:</label>
                <input type="number" name="total_barang" id="totalBarang" readonly>
            </div>
            
            <div class="form-group">
                <label><strong>Total Nilai Retur:</strong></label>
                <input type="text" id="totalDisplay" value="Rp 0" readonly style="font-size: 1.3em; font-weight: bold;">
                <input type="hidden" name="total_nilai" id="totalHidden" value="0">
            </div>
        </div>
        
        <div class="form-group">
            <label>Keterangan:</label>
            <textarea name="keterangan" rows="3" placeholder="Keterangan tambahan (opsional)"></textarea>
        </div>
        
        <button type="submit" name="tambah" class="btn btn-primary">💾 Simpan Retur</button>
    </form>
</div>

<!-- DAFTAR RETUR -->
<div class="card">
    <h3>📋 Daftar Retur Pembelian</h3>
    
    <div class="search-box">
        <input type="text" id="searchRetur" onkeyup="searchTable('searchRetur', 'returTable')" 
               placeholder="🔍 Cari retur...">
    </div>
    
    <div class="table-responsive">
        <table class="data-table" id="returTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. Retur</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Total Barang</th>
                    <th>Total Nilai</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = mysqli_fetch_assoc($data)): 
                    $badge_class = $row['status'] == 'Selesai' ? 'badge-success' : 'badge-warning';
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['no_retur'] ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_retur'])) ?></td>
                    <td><?= $row['nama_supplier'] ?></td>
                    <td><?= $row['total_barang'] ?> item</td>
                    <td>Rp <?= number_format($row['total_nilai'], 0, ',', '.') ?></td>
                    <td><span class="badge <?= $badge_class ?>"><?= $row['status'] ?></span></td>
                    <td class="action-buttons">
                        <a href="?page=retur_pembelian&detail=<?= $row['id_retur_pembelian'] ?>" class="btn btn-info btn-sm">👁️ Detail</a>
                        <?php if ($row['status'] == 'Proses'): ?>
                            <a href="?page=retur_pembelian&selesai=<?= $row['id_retur_pembelian'] ?>" class="btn btn-success btn-sm">✓ Selesai</a>
                        <?php endif; ?>
                        <a href="?page=retur_pembelian&hapus=<?= $row['id_retur_pembelian'] ?>" class="btn btn-danger btn-sm" 
                           onclick="return confirmDelete(this.href, 'Retur akan dihapus dan stok dikembalikan!')">🗑️ Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// ========== MODAL DETAIL RETUR ==========
if (isset($_GET['detail'])) {
    $id = $_GET['detail'];
    $header = mysqli_query($conn, "SELECT rp.*, s.nama_supplier, s.alamat, s.no_telp 
                                   FROM retur_pembelian rp
                                   JOIN master_supplier s ON rp.id_supplier = s.id_supplier
                                   WHERE rp.id_retur_pembelian='$id'");
    $h = mysqli_fetch_assoc($header);
    
    $detail = mysqli_query($conn, "SELECT drp.*, b.nama_barang, s.nama_satuan
                                   FROM detail_retur_pembelian drp
                                   JOIN master_barang b ON drp.id_barang = b.id_barang
                                   JOIN master_satuan s ON b.id_satuan = s.id_satuan
                                   WHERE drp.id_retur_pembelian='$id'");
?>
<div class="modal-overlay" onclick="window.location='?page=retur_pembelian'"></div>
<div class="modal-detail">
    <div class="modal-header">
        <h2>🧾 Detail Retur: <?= $h['no_retur'] ?></h2>
        <button onclick="window.location='?page=retur_pembelian'" class="btn-close">✖</button>
    </div>
    
    <div class="modal-body">
        <div class="info-grid">
            <div class="info-item">
                <strong>Tanggal Retur:</strong>
                <span><?= date('d F Y', strtotime($h['tanggal_retur'])) ?></span>
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
                <strong>Total Barang:</strong>
                <span><?= $h['total_barang'] ?> item</span>
            </div>
            <div class="info-item">
                <strong>Status:</strong>
                <span class="badge <?= $h['status'] == 'Selesai' ? 'badge-success' : 'badge-warning' ?>"><?= $h['status'] ?></span>
            </div>
        </div>
        
        <?php if ($h['keterangan']): ?>
        <div class="info-item">
            <strong>Keterangan:</strong>
            <span><?= $h['keterangan'] ?></span>
        </div>
        <?php endif; ?>
        
        <h4 style="margin-top: 20px;">Detail Barang:</h4>
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Satuan</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                    <th>Alasan</th>
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
                    <td><?= $d['alasan'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" style="text-align: right;">TOTAL NILAI RETUR:</th>
                    <th colspan="2">Rp <?= number_format($h['total_nilai'], 0, ',', '.') ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <div class="modal-footer">
        <button onclick="window.print()" class="btn btn-secondary">🖨️ Print</button>
        <button onclick="window.location='?page=retur_pembelian'" class="btn btn-primary">Tutup</button>
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
    let totalBarang = 0;
    const rows = document.querySelectorAll('.detail-row');
    
    rows.forEach(row => {
        const select = row.querySelector('.barang-select');
        const qty = row.querySelector('.qty-input').value;
        
        if (select.value && qty) {
            const harga = select.options[select.selectedIndex].getAttribute('data-harga');
            total += parseInt(harga) * parseInt(qty);
            totalBarang += parseInt(qty);
        }
    });
    
    document.getElementById('totalDisplay').value = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('totalHidden').value = total;
    document.getElementById('totalBarang').value = totalBarang;
}
</script>

<style>
.detail-row {
    margin-bottom: 10px;
    padding: 10px;
    background: #fff3cd;
    border-radius: 5px;
    border-left: 4px solid #ff6b6b;
}

.form-row {
    display: flex;
    gap: 15px;
    align-items: end;
}

.form-group {
    flex: 1;
}

.badge-warning {
    background: #ffc107;
    color: #000;
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