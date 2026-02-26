<!DOCTYPE html>
<html>
<head>
    <title><?= $judul ?></title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #FFFF00; border: 1px solid #000; font-weight: bold; text-align: center; }
        td { border: 1px solid #000; vertical-align: middle; }
        .text-center { text-align: center; }
        .str { mso-number-format:"\@"; } /* Format Text untuk NIP agar tidak jadi E+ */
    </style>
</head>
<body>
    <h3><?= $judul ?></h3>
    <p>MAN SIPAGIMBAR</p>
    
    <table>
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
                <td class="text-center"><?= $no++ ?></td>

                <?php if($jenis == 'guru'): ?>
                    <td class="str"><?= $row['nip'] ?></td>
                    <td><?= $row['nama_guru'] ?></td>
                    <td><?= ($row['tugas_tambahan'] == 'Tidak Ada') ? 'Guru Mapel' : $row['tugas_tambahan'] ?></td>

                <?php elseif($jenis == 'mapel'): ?>
                    <td class="text-center"><?= $row['kode_mapel'] ?></td>
                    <td><?= $row['nama_mapel'] ?></td>
                    <td><?= $row['kelompok'] ?></td>
                    <td class="text-center"><?= $row['max_jam_per_minggu'] ?></td>

                <?php elseif($jenis == 'kelas'): ?>
                    <td><?= $row['nama_kelas'] ?></td>
                    <td><?= $row['wali_kelas'] ?? '-' ?></td>
                    <td><?= $row['homebase'] ?? '-' ?></td>
                    <td class="text-center"><?= $row['jumlah_siswa'] ?></td>

                <?php elseif($jenis == 'ruangan'): ?>
                    <td><?= $row['nama_ruangan'] ?></td>
                    <td><?= $row['jenis'] ?></td>
                    <td class="text-center"><?= $row['kapasitas'] ?></td>

                <?php elseif($jenis == 'jam'): ?>
                    <td><?= $row['hari'] ?></td>
                    <td class="str"><?= date('H:i', strtotime($row['waktu_mulai'])) ?> - <?= date('H:i', strtotime($row['waktu_selesai'])) ?></td>
                
                <?php elseif($jenis == 'pengampu'): ?>
                    <td><?= $row['nama_guru'] ?></td>
                    <td><?= $row['nama_mapel'] ?></td>
                    <td class="text-center"><?= $row['nama_kelas'] ?></td>
                    <td class="text-center"><?= $row['jumlah_jam'] ?></td>

                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>