<?php
// app/views/report/index.php

$months   = $months   ?? [];
$types    = $types    ?? [];
$gudangs  = $gudangs  ?? [];
$stoList  = $stoList  ?? [];
$invoices = $invoices ?? [];

$stoData = [];
$stoOptions = [];
foreach ($stoList as $s) {
    $id   = (int)$s['id'];
    $norm = isset($s['tonase_normal']) ? (float)$s['tonase_normal'] : 0;
    $lemb = isset($s['tonase_lembur']) ? (float)$s['tonase_lembur'] : 0;
    $stoData[$id] = [
      'tanggal'       => $s['tanggal_terbit'] ?? '',
      'nama_gudang'   => $s['nama_gudang']    ?? '',
      'transportir'   => $s['transportir']    ?? '',
      'tonase_normal' => $norm,
      'tonase_lembur' => $lemb,
      'jumlah'        => $norm + $lemb,
      'keterangan'    => $s['keterangan']     ?? '',
    ];
    $stoOptions[] = [
      'id'   => $id,
      'text' => $s['nomor_sto'],
    ];
}
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
      rel="stylesheet"/>

<div class="container mt-4">
  <a href="index.php?page=dashboard" class="btn btn-secondary mb-3">← Back</a>
  <h2>Laporan STO</h2>

  <?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success">
      <?= htmlspecialchars($_SESSION['success']) ?>
      <?php unset($_SESSION['success']) ?>
    </div>
  <?php endif; ?>

  <form id="frm-report" method="POST" action="index.php?page=report_generate">
    <div class="row gy-3">
      <div class="col-md-3">
        <label>Bulan</label>
        <select name="bulan" class="form-control" required>
          <?php foreach ($months as $m): ?>
            <option value="<?= htmlspecialchars($m) ?>"><?= htmlspecialchars($m) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label>Jenis Pupuk</label>
        <input name="jenis_pupuk" class="form-control" placeholder="Masukkan jenis pupuk…" required>
      </div>
      <div class="col-md-3">
        <label>Gudang</label>
        <select id="sel-gudang" name="gudang_id" class="form-control" required>
          <option value="">-- Pilih --</option>
          <?php foreach ($gudangs as $g): ?>
            <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nama_gudang']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label>Jenis Transaksi</label>
        <select id="sel-trans" name="jenis_transaksi" class="form-control" required>
          <option value="">-- Pilih --</option>
          <?php foreach ($types as $t): ?>
            <option value="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars($t) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label>Uraian Pekerjaan</label>
        <input name="uraian_pekerjaan" class="form-control" placeholder="Misal: Bongkar Pupuk" required>
      </div>
      <div class="col-md-4">
        <label>Tarif Normal (Rp)</label>
        <input id="fld-normal" name="tarif_normal" class="form-control" readonly>
      </div>
      <div class="col-md-4">
        <label>Tarif Lembur (Rp)</label>
        <input id="fld-lembur" name="tarif_lembur" class="form-control" readonly>
      </div>
    </div>

    <hr>

    <div class="table-responsive mb-3">
      <table class="table table-bordered" id="tbl-sto">
        <thead class="table-light">
          <tr>
            <th style="width:40px;">No</th>
            <th>
              Sales Transport Order<br>
              <small>Nomor STO</small>
            </th>
            <th>Tanggal Terbit</th>
            <th>Nama Gudang</th>
            <th>Transportir</th>
            <th>Tonase Normal</th>
            <th>Tonase Lembur</th>
            <th>Jumlah</th>
            <th>Keterangan</th>
            <th style="width:40px;"></th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
      <button type="button" id="btn-add" class="btn btn-sm btn-primary">+ Tambah Baris</button>
    </div>

    <button type="submit" class="btn btn-success">Generate Invoice & QR</button>
  </form>

  <hr>
  <h4>Daftar Invoice</h4>
<table class="table table-striped">
  <thead>
    <tr>
      <th>#</th>
      <th>ID</th>
      <th>Bulan</th>
      <th>Pupuk</th>
      <th>Gudang</th>
      <th>Transaksi</th>
      <th>Tarif N</th>
      <th>Tarif L</th>
      <th>Baris</th>
      <th>Dibuat</th>
      <th>QR</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($invoices as $i => $inv): ?>
      <tr>
        <td><?= $i + 1 ?></td>
        <td><?= $inv['id'] ?></td>
        <td><?= htmlspecialchars($inv['bulan']) ?></td>
        <td><?= htmlspecialchars($inv['jenis_pupuk']) ?></td>
        <td><?= htmlspecialchars($inv['nama_gudang']) ?></td>
        <td><?= htmlspecialchars($inv['jenis_transaksi']) ?></td>
        <td><?= number_format($inv['tarif_normal'], 0, ',', '.') ?></td>
        <td><?= number_format($inv['tarif_lembur'], 0, ',', '.') ?></td>
        <td>
          <?php
            // count lines for this invoice
            $stmt = $conn->prepare("SELECT COUNT(*) FROM invoice_line WHERE invoice_id = ?");
            $stmt->execute([ $inv['id'] ]);
            echo $stmt->fetchColumn();
          ?>
        </td>
        <td><?= $inv['created_at'] ?></td>
        <td>
          <?php $qr = "uploads/qr_{$inv['id']}.png"; ?>
          <?php if (file_exists($qr)): ?>
            <img src="<?= $qr ?>" width="48" alt="QR #<?= $inv['id'] ?>">
          <?php else: ?>
            &ndash;
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (empty($invoices)): ?>
      <tr>
        <td colspan="11" class="text-center text-muted">
          Belum ada invoice yang dibuat.
        </td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  const stoData   = <?= json_encode($stoData,   JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;
  const stoOpts   = <?= json_encode($stoOptions, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>;

  function renumber() {
    $('#tbl-sto tbody tr').each((i,tr) => {
      $(tr).find('td.no').text(i+1);
      if (i === 0) $(tr).find('.rm').hide();
      else         $(tr).find('.rm').show();
    });
  }

  function addRow(){
    const $row = $(`
      <tr>
        <td class="no"></td>
        <td>
          <select name="sto_ids[]" class="form-control sto-sel" required>
            <option></option>
          </select>
        </td>
        <td class="tgl"></td>
        <td class="gdg"></td>
        <td class="trp"></td>
        <td class="norm"></td>
        <td class="lemb"></td>
        <td class="jml"></td>
        <td class="ket"></td>
        <td class="text-center">
          <button type="button" class="btn btn-sm btn-outline-danger rm">–</button>
        </td>
      </tr>
    `);

    $('#tbl-sto tbody').append($row);
    renumber();

    $row.find('.sto-sel')
      .select2({
        data: stoOpts,
        placeholder: 'Cari Nomor STO…',
        allowClear: true,
        width: '100%'
      })
      .on('select2:select', e => {
        const id = e.params.data.id;
        const d  = stoData[id] || {};
        $row.find('.tgl').text(d.tanggal);
        $row.find('.gdg').text(d.nama_gudang);
        $row.find('.trp').text(d.transportir);
        $row.find('.norm').text(d.tonase_normal);
        $row.find('.lemb').text(d.tonase_lembur);
        $row.find('.jml').text(d.jumlah);
        $row.find('.ket').text(d.keterangan);
        renumber();
      });

    $row.find('.rm').click(() => {
      $row.remove();
      renumber();
    });
  }

  $(function(){
    addRow();
    $('#btn-add').click(addRow);

    $('#sel-gudang, #sel-trans').on('change', () => {
      const g = $('#sel-gudang').val();
      const t = $('#sel-trans').val();
      if (!g || !t) return;
      $.getJSON('ajax/get_tarif.php', { gudang_id: g, jenis_transaksi: t })
       .done(d => {
         $('#fld-normal').val(d.tarif_normal||'');
         $('#fld-lembur').val(d.tarif_lembur||'');
       });
    });
  });
</script>
