<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Jika User Login tapi BUKAN ADMIN, tendang balik!
        if (session()->get('logged_in') && session()->get('role') != 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak! Menu ini hanya untuk Admin.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}