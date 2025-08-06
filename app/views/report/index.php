<?php
// app/views/report/index.php

$months        = $months        ?? [];
$types         = $types         ?? [];
$gudangs       = $gudangs       ?? [];
$stoList       = $stoList       ?? [];
$invoices      = $invoices      ?? [];
$invoiceData   = $invoiceData   ?? [];
$invoiceLines  = $invoiceLines  ?? [];

// Siapkan data STO untuk Select2
$stoData    = [];
$stoOptions = [];
foreach ($stoList as $s) {
    $id   = (int)$s['id'];
    $norm = (float)($s['tonase_normal'] ?? 0);
    $lemb = (float)($s['tonase_lembur'] ?? 0);
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

// Deteksi mode Edit
$editId   = isset($_GET['id']) ? (int)$_GET['id'] : null;
$editMode = $editId && isset($invoiceData[$editId]);
$hdr      = $editMode ? $invoiceData[$editId] : null;
?>

<link
  href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
  rel="stylesheet"/>

<div class="container mt-4">
  <a href="index.php?page=dashboard" class="btn btn-secondary mb-3">← Back</a>
  <h2>Laporan STO</h2>

  <?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <!-- FORM CREATE / EDIT -->
  <form id="frm-report"
        method="POST"
        action="index.php?page=<?= $editMode ? 'report_update' : 'report_generate' ?><?=
                  $editMode ? '&id='.$editId : '' ?>">
    <?php if($editMode): ?>
      <input type="hidden" name="invoice_id" value="<?= $editId ?>">
    <?php endif; ?>

    <div class="row gy-3">
      <!-- Bulan -->
      <div class="col-md-3">
        <label>Bulan</label>
        <select name="bulan" class="form-control" required>
          <?php foreach ($months as $m): ?>
            <option value="<?= $m ?>"
              <?= $editMode && $hdr['bulan'] === $m ? 'selected' : '' ?>>
              <?= $m ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <!-- Jenis Pupuk -->
      <div class="col-md-3">
        <label>Jenis Pupuk</label>
        <input name="jenis_pupuk"
               value="<?= $editMode ? htmlspecialchars($hdr['jenis_pupuk']) : '' ?>"
               class="form-control"
               placeholder="Masukkan jenis pupuk…"
               required>
      </div>
      <!-- Gudang -->
      <div class="col-md-3">
        <label>Gudang</label>
        <select id="sel-gudang"
                name="gudang_id"
                class="form-control"
                required>
          <option value="">-- Pilih --</option>
          <?php foreach($gudangs as $g): ?>
            <option value="<?= $g['id'] ?>"
              <?= $editMode && $hdr['gudang_id']===$g['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($g['nama_gudang']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <!-- Jenis Transaksi -->
      <div class="col-md-3">
        <label>Jenis Transaksi</label>
        <select id="sel-trans"
                name="jenis_transaksi"
                class="form-control"
                required>
          <option value="">-- Pilih --</option>
          <?php foreach($types as $t): ?>
            <option value="<?= $t ?>"
              <?= $editMode && $hdr['jenis_transaksi']===$t ? 'selected' : '' ?>>
              <?= $t ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Uraian -->
      <div class="col-md-4">
        <label>Uraian Pekerjaan</label>
        <input name="uraian_pekerjaan"
               value="<?= $editMode ? htmlspecialchars($hdr['uraian_pekerjaan']) : '' ?>"
               class="form-control"
               placeholder="Misal: Bongkar Pupuk"
               required>
      </div>
      <!-- Tarif Normal -->
      <div class="col-md-4">
        <label>Tarif Normal (Rp)</label>
        <input id="fld-normal"
               name="tarif_normal"
               value="<?= $editMode ? $hdr['tarif_normal'] : '' ?>"
               class="form-control"
               readonly>
      </div>
      <!-- Tarif Lembur -->
      <div class="col-md-4">
        <label>Tarif Lembur (Rp)</label>
        <input id="fld-lembur"
               name="tarif_lembur"
               value="<?= $editMode ? $hdr['tarif_lembur'] : '' ?>"
               class="form-control"
               readonly>
      </div>
    </div>

    <hr>

    <!-- tabel STO dynamic rows -->
    <div class="table-responsive mb-3">
      <table class="table table-bordered" id="tbl-sto">
        <thead class="table-light">
          <tr>
            <th style="width:40px;">No</th>
            <th>Nomor STO</th>
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

    <button type="submit" class="btn btn-success">
      <?= $editMode ? 'Update Invoice' : 'Generate Invoice & QR' ?>
    </button>
  </form>

  <hr>

  <!-- daftar invoice -->
  <h4>Daftar Invoice</h4>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>#</th><th>ID</th><th>Bulan</th><th>Pupuk</th><th>Gudang</th>
        <th>Transaksi</th><th>Uraian Pekerjaan</th><th>Dibuat Pada</th><th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($invoices): foreach($invoices as $i=>$inv): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= $inv['id'] ?></td>
          <td><?= htmlspecialchars($inv['bulan']) ?></td>
          <td><?= htmlspecialchars($inv['jenis_pupuk']) ?></td>
          <td><?= htmlspecialchars($inv['nama_gudang']) ?></td>
          <td><?= htmlspecialchars($inv['jenis_transaksi']) ?></td>
          <td><?= htmlspecialchars($inv['uraian_pekerjaan']) ?></td>
          <td><?= $inv['created_at'] ?></td>
          <td>
            <button class="btn btn-sm btn-primary btn-view" data-id="<?= $inv['id'] ?>">
              View
            </button>
            <button class="btn btn-sm btn-warning btn-edit" data-id="<?= $inv['id'] ?>">
              Edit
            </button>
            <a href="index.php?page=invoice_delete&id=<?= $inv['id'] ?>"
               class="btn btn-sm btn-danger"
               onclick="return confirm('Hapus invoice #<?= $inv['id'] ?>?')">
              Hapus
            </a>
          </td>
        </tr>
      <?php endforeach; else: ?>
        <tr>
          <td colspan="9" class="text-center text-muted">
            Belum ada invoice yang dibuat.
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Modal detail -->
<div class="modal fade" id="modalInvoice" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content" id="modalInvoiceContent"></div>
  </div>
</div>

<!-- SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  const stoData      = <?= json_encode($stoData, JSON_UNESCAPED_UNICODE) ?>;
  const stoOpts      = <?= json_encode($stoOptions, JSON_UNESCAPED_UNICODE) ?>;
  const invoiceData  = <?= json_encode($invoiceData, JSON_UNESCAPED_UNICODE) ?>;
  const invoiceLines = <?= json_encode($invoiceLines, JSON_UNESCAPED_UNICODE) ?>;

  // renumber & toggle delete-button
  function renumber(){
    $('#tbl-sto tbody tr').each((i,tr)=>{
      $(tr).find('td.no').text(i+1);
      $(tr).find('.rm').toggle(i>0);
    });
  }

  // tambahkan satu row, optional sudah ter‐select
  function addRow(selectedId=null){
    const $r = $(`
      <tr>
        <td class="no"></td>
        <td><select name="sto_ids[]" class="form-control sto-sel" required>
              <option></option>
            </select>
        </td>
        <td class="tgl"></td><td class="gdg"></td><td class="trp"></td>
        <td class="norm"></td><td class="lemb"></td><td class="jml"></td><td class="ket"></td>
        <td class="text-center">
          <button type="button" class="btn btn-sm btn-outline-danger rm">–</button>
        </td>
      </tr>`);
    $('#tbl-sto tbody').append($r);
    renumber();

    const sel = $r.find('.sto-sel').select2({
      data: stoOpts, placeholder:'Cari Nomor STO…',
      allowClear:true, width:'100%'
    }).on('select2:select', e=>{
      const d = stoData[e.params.data.id]||{};
      $r.find('.tgl').text(d.tanggal);
      $r.find('.gdg').text(d.nama_gudang);
      $r.find('.trp').text(d.transportir);
      $r.find('.norm').text(d.tonase_normal);
      $r.find('.lemb').text(d.tonase_lembur);
      $r.find('.jml').text(d.jumlah);
      $r.find('.ket').text(d.keterangan);
      renumber();
    });

    if(selectedId){
      sel.val(selectedId).trigger('change');
      sel.trigger({
        type:'select2:select',
        params:{data:{id:selectedId}}
      });
    }

    $r.find('.rm').click(()=>{
      $r.remove(); renumber();
    });
  }

  $(function(){
    // 1 baris minimal
    $('#tbl-sto tbody').empty();
    addRow();
    $('#btn-add').click(()=>addRow());

    // pre‐fill saat Edit
    $(document).on('click','.btn-edit',function(){
      const id = $(this).data('id'),
            hdr = invoiceData[id],
            lines = invoiceLines[id]||[];

      // header
      $('select[name=bulan]').val(hdr.bulan);
      $('input[name=jenis_pupuk]').val(hdr.jenis_pupuk);
      $('#sel-gudang').val(hdr.gudang_id).trigger('change');
      $('#sel-trans').val(hdr.jenis_transaksi).trigger('change');
      $('input[name=uraian_pekerjaan]').val(hdr.uraian_pekerjaan);
      $('#fld-normal').val(hdr.tarif_normal);
      $('#fld-lembur').val(hdr.tarif_lembur);

      // baris STO
      $('#tbl-sto tbody').empty();
      lines.forEach(sid=> addRow(sid));

      // ubah action & tombol
      $('#frm-report')
        .attr('action','index.php?page=report_update&id='+id)
        .find('button[type=submit]')
        .text('Update Invoice');

      // scroll up
      $('html,body').animate({scrollTop:$('.container').offset().top},300);
    });

    // hitung tarif
    $('#sel-gudang,#sel-trans').on('change',()=>{
      const g=$('#sel-gudang').val(),t=$('#sel-trans').val();
      if(!g||!t) return;
      $.getJSON('ajax/get_tarif.php',{gudang_id:g,jenis_transaksi:t})
       .done(d=>{
         $('#fld-normal').val(d.tarif_normal);
         $('#fld-lembur').val(d.tarif_lembur);
       });
    });

    // view modal
    $(document).on('click','.btn-view',function(){
      const id=$(this).data('id');
      $.get('index.php?page=invoice_view_partial&id='+id,html=>{
        $('#modalInvoiceContent').html(html);
        new bootstrap.Modal('#modalInvoice').show();
      });
    });
  });
</script>
