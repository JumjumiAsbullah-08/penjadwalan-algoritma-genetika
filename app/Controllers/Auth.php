<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }
        return view('login');
    }

    public function login()
    {
        $session = session();
        $model = new UserModel();
        
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        
        $data = $model->where('username', $username)->first();
        
        if ($data) {
            // Cek Password
            if ($data['password'] == $password) { // Catatan: Sebaiknya nanti pakai password_verify()
                
                $ses_data = [
                    'id_user'       => $data['id_user'],
                    'nama_lengkap'  => $data['nama_lengkap'],
                    'role'          => $data['role'],
                    'logged_in'     => TRUE
                ];
                $session->set($ses_data);
                
                // TAMBAHAN 1: Set Flashdata Sukses Login
                // Pesan ini akan kita panggil di view dashboard/template
                $session->setFlashdata('login_sukses', 'Selamat datang, ' . $data['nama_lengkap'] . '!');
                
                return redirect()->to('/dashboard');
            } else {
                // Flashdata Error (Tetap gunakan yang lama atau sesuaikan)
                $session->setFlashdata('error', 'Password Salah. Silakan coba lagi.');
                return redirect()->to('/auth');
            }
        } else {
            $session->setFlashdata('error', 'Username tidak ditemukan.');
            return redirect()->to('/auth');
        }
    }

    public function logout()
    {
        session()->destroy();
        // Opsional: Kirim pesan sukses logout ke halaman login
        return redirect()->to('/')->with('success', 'Anda berhasil keluar sistem.'); 
    }
}