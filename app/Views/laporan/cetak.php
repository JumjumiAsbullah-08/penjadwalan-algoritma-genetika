<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Jadwal - <?= $info_target ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* CSS KHUSUS PRINT F4 LANDSCAPE */
        @media print {
            @page { size: 330mm 215mm; margin: 10mm; } /* F4 Landscape */
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
        
        body { font-family: 'Arial', sans-serif; font-size: 12px; }
        
        /* TABEL TEGAS */
        .table-cetak { width: 100%; border-collapse: collapse; border: 2px solid #000; }
        .table-cetak th { background-color: #333 !important; color: white !important; padding: 8px; border: 1px solid #000; text-transform: uppercase; text-align: center; }
        .table-cetak td { border: 1px solid #000; vertical-align: top; padding: 0; height: 1px; }
        
        /* KOTAK ISI */
        .box-mapel { padding: 5px; height: 100%; border-left: 5px solid #000; font-size: 11px; position: relative; }
        .mapel-title { font-weight: bold; font-size: 12px; margin-bottom: 3px; display: block; }
        .mapel-info { font-style: italic; color: #333; display: block; }
        .badge-ruang { position: absolute; top: 2px; right: 2px; background: #000; color: #fff; padding: 1px 4px; font-size: 9px; border-radius: 2px; }
        
        /* KOP SURAT */
        .kop-surat { border-bottom: 3px double #000; margin-bottom: 20px; padding-bottom: 10px; text-align: center; }
        .kop-surat h2 { font-weight: bold; margin: 0; font-size: 24px; text-transform: uppercase; }
        .kop-surat p { margin: 0; font-size: 14px; }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print p-3 bg-light border-bottom mb-4 d-flex justify-content-between">
        <button onclick="window.history.back()" class="btn btn-secondary btn-sm">Kembali</button>
        <button onclick="window.print()" class="btn btn-primary btn-sm fw-bold">Cetak Sekarang</button>
    </div>

    <div class="container-fluid">
        
        <div class="kop-surat">
            <h2>KEMENTERIAN AGAMA REPUBLIK INDONESIA</h2>
            <h2 style="font-size: 28px;">MAN SIPAGIMBAR</h2>
            <p>Jalan Pendidikan No. 123, Sipagimbar, Tapanuli Selatan, Sumatera Utara</p>
            <p>Website: www.mansipagimbar.sch.id | Email: info@mansipagimbar.sch.id</p>
        </div>

        <div class="text-center mb-4">
            <h3 class="fw-bold text-uppercase text-decoration-underline">JADWAL PELAJARAN <?= strtoupper($tipe_cetak) ?></h3>
            <h4 class="fw-bold mt-1"><?= $info_target ?></h4>
            <span class="badge bg-white text-dark border border-dark">Semester Ganjil TA 2025/2026</span>
        </div>

        <table class="table-cetak">
            <thead>
                <tr>
                    <th width="5%">JAM</th>
                    <th width="8%">WAKTU</th>
                    <?php foreach($hari_list as $hari): ?>
                        <th width="14.5%"><?= strtoupper($hari) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php $skip = []; ?>
                <?php for($jam=1; $jam<=$max_jam; $jam++): ?>
                    
                    <?php if(isset($istirahat[$jam])): ?>
                        <tr style="background-color: #ddd !important;">
                            <td class="text-center fw-bold bg-secondary text-white" style="border: 1px solid #000;"><?= $jam ?></td>
                            <td class="text-center small fw-bold" style="border: 1px solid #000;"><?= $waktu_jam[$jam] ?? '' ?></td>
                            <td colspan="6" class="text-center fw-bold py-2" style="letter-spacing: 2px; border: 1px solid #000;">
                                ISTIRAHAT (<?= $istirahat[$jam] ?>)
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td class="text-center fw-bold bg-light"><?= $jam ?></td>
                            <td class="text-center small"><?= $waktu_jam[$jam] ?? '' ?></td>
                            
                            <?php foreach($hari_list as $hari): 
                                if(isset($skip[$hari][$jam])) continue;
                                $slot = $jadwal[$hari][$jam] ?? null;
                                $rowspan = 1;

                                if($slot) {
                                    for($n=$jam+1; $n<=$max_jam; $n++) {
                                        if(isset($istirahat[$n])) break;
                                        $next = $jadwal[$hari][$n] ?? null;
                                        if($next && $next['mapel'] == $slot['mapel'] && $next['info'] == $slot['info']) {
                                            $rowspan++; $skip[$hari][$n] = true;
                                        } else break;
                                    }
                                }
                            ?>
                                <td rowspan="<?= $rowspan ?>">
                                    <?php if($slot): ?>
                                        <div class="box-mapel" style="background-color: <?= $slot['warna'] ?>40; border-left-color: <?= $slot['warna'] ?>;">
                                            <span class="badge-ruang"><?= $slot['ruang'] ?></span>
                                            <span class="mapel-title"><?= $slot['mapel'] ?></span>
                                            <span class="mapel-info"><?= $slot['info'] ?></span>
                                            <?php if($rowspan > 1): ?><span style="font-size: 9px; font-weight: bold;">(<?= $rowspan ?> JP)</span><?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endif; ?>
                <?php endfor; ?>
            </tbody>
        </table>

        <div class="row mt-5 avoid-break">
            <div class="col-4 offset-8 text-center">
                <p class="mb-1">Sipagimbar, <?= date('d F Y') ?></p>
                <p class="mb-3">Mengetahui,<br><strong>Kepala Sekolah</strong></p>
                
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data=<?= urlencode("SAH." . $nama_kepsek) ?>" style="width: 70px;">
                
                <p class="fw-bold text-decoration-underline mt-3 mb-0"><?= $nama_kepsek ?></p>
                <small>NIP. <?= $nip_kepsek ?></small>
            </div>
        </div>

    </div>
</body>
</html>