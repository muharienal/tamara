<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Master STO</h2>
    <a href="index.php?page=dashboard" class="btn btn-secondary">Back</a>
  </div>

  <!-- Flash messages -->
  <?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']);?></div>
  <?php endif;?>
  <?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']);?></div>
  <?php endif;?>

  <!-- 1) Registration form -->
  <form id="stoForm" class="row g-3 mb-5" method="POST">
    <div class="col-md-4">
      <label class="form-label">Nomor STO</label>
      <input type="text" name="nomor_sto" class="form-control" placeholder="Masukkan Nomor STO" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Tanggal Terbit</label>
      <input type="date" name="tanggal_terbit" class="form-control" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Nama Gudang</label>
      <select name="gudang_id" class="form-control" required>
        <option value="">-- Pilih Gudang --</option>
        <?php foreach($gudangs as $g): ?>
          <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nama_gudang']) ?></option>
        <?php endforeach;?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Jenis Transaksi</label>
      <select name="jenis_transaksi" class="form-control" required>
        <option value="">-- Pilih --</option>
        <option value="BONGKAR">BONGKAR</option>
        <option value="MUAT">MUAT</option>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Tonase Normal (Ton)</label>
      <input type="number" step="0.01" name="tonase_normal" id="tonase_normal" class="form-control" value="0">
    </div>
    <div class="col-md-4">
      <label class="form-label">Tonase Lembur (Ton)</label>
      <input type="number" step="0.01" name="tonase_lembur" id="tonase_lembur" class="form-control" value="0">
    </div>
    <div class="col-md-4">
      <label class="form-label">Total Tonase</label>
      <input type="text" id="total_tonase" class="form-control" readonly>
    </div>
    <div class="col-md-4">
      <label class="form-label">Transportir</label>
      <input type="text" name="transportir" class="form-control" placeholder="Nama Transportir" required>
    </div>
    <div class="col-md-8">
      <label class="form-label">Keterangan</label>
      <input type="text" name="keterangan" class="form-control" placeholder="Opsional">
    </div>
    <div class="col-12 text-end">
      <button type="submit" class="btn btn-primary">Daftar STO</button>
    </div>
  </form>

  <!-- 2) Table filters (client‐side) -->
  <div class="row g-2 mb-3">
    <div class="col-md-2">
      <input type="text" id="f_nomor" class="form-control filter-input" placeholder="Filter Nomor">
    </div>
    <div class="col-md-2">
      <input type="text" id="f_gudang" class="form-control filter-input" placeholder="Filter Gudang">
    </div>
    <div class="col-md-2">
      <input type="text" id="f_transaksi" class="form-control filter-input" placeholder="Filter Transaksi">
    </div>
    <div class="col-md-2">
      <input type="text" id="f_status" class="form-control filter-input" placeholder="Filter Status">
    </div>
    <div class="col-md-4 text-end">
      <button id="resetFilters" class="btn btn-secondary">Reset Filters</button>
    </div>
  </div>

  <!-- 3) Master table -->
  <div class="table-responsive">
    <table id="sto-table" class="table table-bordered">
      <thead>
        <tr>
          <th>#</th>
          <th>Nomor STO</th>
          <th>Tgl Terbit</th>
          <th>Gudang</th>
          <th>Transaksi</th>
          <th>Transportir</th>
          <th>Normal</th>
          <th>Lembur</th>
          <th>Jumlah</th>
          <th>Status</th>
          <th>Keterangan</th>
          <th>Created</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($stoList as $i => $s): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($s['nomor_sto']) ?></td>
          <td><?= htmlspecialchars($s['tanggal_terbit']) ?></td>
          <td><?= htmlspecialchars($s['nama_gudang']) ?></td>
          <td><?= htmlspecialchars($s['jenis_transaksi']) ?></td>
          <td><?= htmlspecialchars($s['transportir']) ?></td>
          <td><?= number_format($s['tonase_normal'],2) ?></td>
          <td><?= number_format($s['tonase_lembur'],2) ?></td>
          <td><?= number_format($s['jumlah'],2) ?></td>
          <td><?= htmlspecialchars($s['status']) ?></td>
          <td><?= htmlspecialchars($s['keterangan']) ?></td>
          <td><?= htmlspecialchars($s['created_at']) ?></td>
          <td>
            <button data-id="<?= $s['id'] ?>"
                    class="btn btn-sm btn-warning btn-edit">Edit</button>
            <a href="?page=master_sto&delete=<?= $s['id'] ?>"
               class="btn btn-sm btn-danger"
               onclick="return confirm('Hapus STO ini?')">Hapus</a>
          </td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
</div>

<!-- 4) Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="editForm" class="modal-content">
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="id" id="edit-id">
      <div class="modal-header">
        <h5 class="modal-title">Edit STO</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <!-- replicate the same fields as the form above -->
          <div class="col-md-6">
            <label class="form-label">Nomor STO</label>
            <input type="text" name="nomor_sto" id="edit-nomor" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Tanggal Terbit</label>
            <input type="date" name="tanggal_terbit" id="edit-tanggal" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Nama Gudang</label>
            <select name="gudang_id" id="edit-gudang" class="form-control" required>
              <option value="">-- Pilih Gudang --</option>
              <?php foreach($gudangs as $g): ?>
                <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nama_gudang']) ?></option>
              <?php endforeach;?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Jenis Transaksi</label>
            <select name="jenis_transaksi" id="edit-jenis" class="form-control" required>
              <option value="BONGKAR">BONGKAR</option>
              <option value="MUAT">MUAT</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Tonase Normal</label>
            <input type="number" step="0.01" name="tonase_normal" id="edit-normal" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Tonase Lembur</label>
            <input type="number" step="0.01" name="tonase_lembur" id="edit-lembur" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Transportir</label>
            <input type="text" name="transportir" id="edit-transportir" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Keterangan</label>
            <input type="text" name="keterangan" id="edit-keterangan" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Status</label>
            <select name="status" id="edit-status" class="form-control">
              <option value="NOT_USED">NOT_USED</option>
              <option value="USED">USED</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', ()=>{

  // compute total tonase on the registration form
  const n = document.getElementById('tonase_normal'),
        l = document.getElementById('tonase_lembur'),
        t = document.getElementById('total_tonase');
  function updTotal(){ t.value = ((parseFloat(n.value)||0)+(parseFloat(l.value)||0)).toFixed(2) }
  n.addEventListener('input',updTotal);
  l.addEventListener('input',updTotal);
  updTotal();

  // client-side table filtering
  document.querySelectorAll('.filter-input').forEach(inp=>{
    inp.addEventListener('input',()=>{
      const cols = {
        'f_nomor':1, 'f_gudang':3,
        'f_transaksi':4,'f_status':9
      };
      const table = document.querySelector('#sto-table tbody');
      Array.from(table.rows).forEach(r=>{
        let show = true;
        for(let id in cols){
          const val = document.getElementById(id).value.toLowerCase();
          if(val && !r.cells[cols[id]].innerText.toLowerCase().includes(val)){
            show = false; break;
          }
        }
        r.style.display = show?'':'none';
      });
    });
  });
  document.getElementById('resetFilters').addEventListener('click',()=>{
    document.querySelectorAll('.filter-input').forEach(i=>i.value='');
    document.querySelectorAll('.filter-input').forEach(i=>i.dispatchEvent(new Event('input')));
  });

  // ——————————————
  // Edit modal logic
  // ——————————————
  const editModal = new bootstrap.Modal(document.getElementById('editModal'));
  document.getElementById('sto-table').addEventListener('click', e=>{
    if(!e.target.classList.contains('btn-edit')) return;
    const id = e.target.dataset.id;
    fetch(`index.php?page=master_sto&action=get&id=${id}`)
      .then(r=>r.json())
      .then(s=>{
        document.getElementById('edit-id').value          = s.id;
        document.getElementById('edit-nomor').value      = s.nomor_sto;
        document.getElementById('edit-tanggal').value    = s.tanggal_terbit;
        document.getElementById('edit-gudang').value     = s.gudang_id;
        document.getElementById('edit-jenis').value      = s.jenis_transaksi;
        document.getElementById('edit-normal').value     = s.tonase_normal;
        document.getElementById('edit-lembur').value     = s.tonase_lembur;
        document.getElementById('edit-transportir').value= s.transportir;
        document.getElementById('edit-keterangan').value = s.keterangan;
        document.getElementById('edit-status').value     = s.status;
        editModal.show();
      });
  });
  document.getElementById('editForm').addEventListener('submit',e=>{
    e.preventDefault();
    const data = new FormData(e.target);
    fetch('index.php?page=master_sto',{
      method:'POST',
      body:data
    })
    .then(r=>r.json())
    .then(res=>{
      if(res.success){
        editModal.hide();
        window.location.reload();
      }
    });
  });

});
</script>
