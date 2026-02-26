<?php

namespace App\Controllers;

use App\Models\JamModel;
use App\Models\JadwalModel;

class JamPelajaran extends BaseController
{
    protected $jamModel;
    protected $jadwalModel;

    public function __construct()
    {
        $this->jamModel = new JamModel();
        $this->jadwalModel = new JadwalModel();
    }

    public function index()
    {
        $is_locked = $this->jadwalModel->countAllResults() > 0;
        $allJam = $this->jamModel->orderBy('waktu_mulai', 'ASC')->findAll();

        $groupedJam = [
            'Senin' => [], 'Selasa' => [], 'Rabu' => [], 
            'Kamis' => [], 'Jumat' => [], 'Sabtu' => []
        ];

        foreach ($allJam as $j) {
            if(isset($groupedJam[$j['hari']])) {
                $groupedJam[$j['hari']][] = $j;
            }
        }

        $data = [
            'jam_per_hari' => $groupedJam,
            'is_locked'    => $is_locked
        ];
        return view('jam_pelajaran/index', $data);
    }

    public function store()
    {
        if ($this->jadwalModel->countAllResults() > 0) return redirect()->to('/jam_pelajaran')->with('error', 'Terkunci.');

        $this->jamModel->save([
            'hari' => $this->request->getVar('hari'),
            'jam_ke' => $this->request->getVar('jam_ke'),
            'waktu_mulai' => $this->request->getVar('waktu_mulai'),
            'waktu_selesai' => $this->request->getVar('waktu_selesai'),
            'is_istirahat' => $this->request->getVar('is_istirahat') ? 1 : 0
        ]);

        return redirect()->to('/jam_pelajaran')->with('success', 'Berhasil disimpan.');
    }

    public function update()
    {
        if ($this->jadwalModel->countAllResults() > 0) return redirect()->to('/jam_pelajaran')->with('error', 'Terkunci.');

        $this->jamModel->save([
            'id_jam' => $this->request->getVar('id_jam'),
            'hari' => $this->request->getVar('hari'),
            'jam_ke' => $this->request->getVar('jam_ke'),
            'waktu_mulai' => $this->request->getVar('waktu_mulai'),
            'waktu_selesai' => $this->request->getVar('waktu_selesai'),
            'is_istirahat' => $this->request->getVar('is_istirahat') ? 1 : 0
        ]);

        return redirect()->to('/jam_pelajaran')->with('success', 'Berhasil diupdate.');
    }

    public function deleteMultiple()
    {
        if ($this->jadwalModel->countAllResults() > 0) return redirect()->to('/jam_pelajaran')->with('error', 'Terkunci.');
        
        $ids = $this->request->getVar('ids');
        if ($ids) {
            $this->jamModel->whereIn('id_jam', $ids)->delete();
            return redirect()->to('/jam_pelajaran')->with('success', 'Data dihapus.');
        }
        return redirect()->to('/jam_pelajaran');
    }

    public function delete($id)
    {
        if ($this->jadwalModel->countAllResults() > 0) return redirect()->to('/jam_pelajaran')->with('error', 'Terkunci.');
        $this->jamModel->delete($id);
        return redirect()->to('/jam_pelajaran')->with('success', 'Dihapus.');
    }

    // --- GENERATOR MULTIPLE BREAKS ---
    public function generate()
    {
        if ($this->jadwalModel->countAllResults() > 0) return redirect()->to('/jam_pelajaran')->with('error', 'Terkunci.');

        $days = $this->request->getVar('days');
        if (!$days) return redirect()->to('/jam_pelajaran')->with('error', 'Pilih hari dulu.');

        $startTime      = strtotime($this->request->getVar('start_time'));
        $slotDuration   = (int) $this->request->getVar('slot_duration');
        $totalSlots     = (int) $this->request->getVar('total_slots');
        $breakDuration  = (int) $this->request->getVar('break_duration');
        
        // AMBIL INPUT ARRAY BREAKS (CONTOH: [4, 7])
        $breaks = $this->request->getVar('breaks'); 
        if(!$breaks) $breaks = []; // Jika kosong, set array kosong

        // Hapus Data Lama
        $this->jamModel->whereIn('hari', $days)->delete(); 

        foreach ($days as $hari) {
            $current = $startTime;
            $jamKe = 1;

            for ($i = 1; $i <= $totalSlots; $i++) {
                // 1. Simpan Jam Belajar
                $start = date('H:i:s', $current);
                $current += ($slotDuration * 60);
                $end = date('H:i:s', $current);

                $this->jamModel->save([
                    'hari' => $hari, 
                    'jam_ke' => $jamKe, 
                    'waktu_mulai' => $start, 
                    'waktu_selesai' => $end, 
                    'is_istirahat' => 0
                ]);
                $jamKe++;

                // 2. Cek Apakah Slot Ini Ada Istirahatnya?
                // Kita cek apakah 'jam ke-i' ada di dalam array pilihan user
                if (in_array($i, $breaks) && $breakDuration > 0) {
                    $startBreak = date('H:i:s', $current);
                    $current += ($breakDuration * 60);
                    $endBreak = date('H:i:s', $current);

                    $this->jamModel->save([
                        'hari' => $hari, 
                        'jam_ke' => 0, // 0 Penanda Istirahat
                        'waktu_mulai' => $startBreak, 
                        'waktu_selesai' => $endBreak, 
                        'is_istirahat' => 1
                    ]);
                }
            }
        }
        return redirect()->to('/jam_pelajaran')->with('success', 'Jadwal Generated!');
    }
}