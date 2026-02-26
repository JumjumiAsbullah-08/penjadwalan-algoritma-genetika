<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $judul ?></title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 12px; -webkit-print-color-adjust: exact; }
        
        /* KOP SURAT */
        .kop-header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-header h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .kop-header h1 { margin: 5px 0; font-size: 24px; font-weight: bold; text-transform: uppercase; }
        .kop-header p { margin: 0; font-size: 12px; }

        /* JUDUL LAPORAN */
        .judul-laporan { text-align: center; margin-bottom: 15px; font-weight: bold; text-decoration: underline; font-size: 14px; }

        /* TABEL DATA */
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-data th, .table-data td { border: 1px solid #000; padding: 6px 8px; vertical-align: middle; }
        .table-data th { background-color: #f0f0f0; text-align: center; text-transform: uppercase; font-size: 11px; font-weight: bold; }
        .center { text-align: center; }

        /* TOMBOL (HIDDEN SAAT PRINT) */
        .no-print { position: fixed; top: 0; right: 0; background: #333; color: #fff; padding: 10px; width: 100%; text-align: right; z-index: 999; }
        .btn-print { background: #fff; border: none; padding: 5px 15px; cursor: pointer; font-weight: bold; border-radius: 4px; }
        .btn-close { background: red; color: white; border: none; padding: 5px 15px; cursor: pointer; font-weight: bold; border-radius: 4px; margin-left: 10px; }

        @media print {
            .no-print { display: none; }
            @page { margin: 10mm 15mm; size: A4; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print">
        <span>Pratinjau Cetak</span>
        <button class="btn-print" onclick="window.print()">🖨️ CETAK</button>
        <button class="btn-close" onclick="window.close()">TUTUP</button>
    </div>
    <div style="height: 50px;" class="no-print"></div>

    <div class="kop-header">
        <h2>Kementerian Agama Republik Indonesia</h2>
        <h1>MAN SIPAGIMBAR</h1>
        <p>Jalan Pendidikan No. 123, Sipagimbar, Tapanuli Selatan, Sumatera Utara</p>
    </div>

    <div class="judul-laporan"><?= $judul ?></div>

    <table class="table-data">
        <thead>
            <tr>
                <?php foreach($kolom as $k): ?>
                    <th><?= $k ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; foreach($data as $row): ?>
            <tr>
                <td class="center" width="5%"><?= $no++ ?></td>

                <?php if($jenis == 'guru'): ?>
                    <td><?= !empty($row['nip']) ? $row['nip'] : '-' ?></td>
                    <td><?= $row['nama_guru'] ?></td>
                    <td class="center">
                        <?php if($row['tugas_tambahan'] == 'Tidak Ada'): ?>
                            Guru Mapel
                        <?php else: ?>
                            <strong><?= $row['tugas_tambahan'] ?></strong>
                        <?php endif; ?>
                    </td>

                <?php elseif($jenis == 'mapel'): ?>
                    <td class="center"><?= $row['kode_mapel'] ?></td>
                    <td><?= $row['nama_mapel'] ?></td>
                    <td class="center"><?= $row['kelompok'] ?></td>
                    <td class="center"><?= $row['max_jam_per_minggu'] ?> JP</td>

                <?php elseif($jenis == 'kelas'): ?>
                    <td><?= $row['nama_kelas'] ?></td>
                    <td><?= $row['wali_kelas'] ?? '<i style="color:red">Belum Ada</i>' ?></td>
                    <td class="center"><?= $row['homebase'] ?? '-' ?></td>
                    <td class="center"><?= $row['jumlah_siswa'] ?> Siswa</td>

                <?php elseif($jenis == 'ruangan'): ?>
                    <td><?= $row['nama_ruangan'] ?></td>
                    <td class="center"><?= $row['jenis'] ?></td>
                    <td class="center"><?= $row['kapasitas'] ?> Orang</td>

                <?php elseif($jenis == 'jam'): ?>
                    <td class="center"><?= $row['hari'] ?></td>
                    <!-- <td class="center"><?= $row['jam_ke'] ?></td> -->
                    <td class="center">
                        <?= date('H:i', strtotime($row['waktu_mulai'])) ?> - 
                        <?= date('H:i', strtotime($row['waktu_selesai'])) ?>
                    </td>

                <?php elseif($jenis == 'pengampu'): ?>
                    <td><?= $row['nama_guru'] ?></td>
                    <td><?= $row['nama_mapel'] ?></td>
                    <td class="center"><?= $row['nama_kelas'] ?></td>
                    <td class="center"><?= $row['jumlah_jam'] ?> JP</td>

                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="float: right; text-align: center; margin-top: 20px; page-break-inside: avoid;">
        <p>Sipagimbar, <?= date('d F Y') ?></p>
        <p>Mengetahui,<br>Kepala Sekolah</p>
        
        <div style="margin: 10px auto;">
            <?php 
                // Isi QR Code: Validasi Nama Kepsek & Tanggal Cetak
                $qrContent = "DOKUMEN VALID.\n\nDisetujui oleh:\n" . $nama_kepsek . "\nJabatan: Kepala Sekolah\nTanggal Cetak: " . date('d F Y H:i');
                $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=90x90&data=" . urlencode($qrContent);
            ?>
            <img src="<?= $qrUrl ?>" alt="QR Validasi" style="width: 85px; height: 85px; border: 1px solid #ccc; padding: 2px;">
            <br>
            <small style="font-size: 8px; color: #555;">Dokumen ini ditandatangani secara elektronik</small>
        </div>

        <p style="font-weight: bold; text-decoration: underline; margin-bottom: 2px;">
            <?= $nama_kepsek ?>
        </p>
        <p style="margin-top: 0;">NIP. <?= $nip_kepsek ?></p>
    </div>

</body>
</html>