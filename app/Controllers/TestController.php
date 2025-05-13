<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class TestController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Test Controller',
            'is_installed' => file_exists(WRITEPATH . 'installed.txt'),
            'request_path' => service('uri')->getPath(),
            'php_version' => PHP_VERSION,
            'ci_version' => \CodeIgniter\CodeIgniter::CI_VERSION,
            'time' => date('Y-m-d H:i:s'),
            'install_file_path' => WRITEPATH . 'installed.txt',
            'writepath_writable' => is_really_writable(WRITEPATH),
            'writepath_exists' => is_dir(WRITEPATH) ? 'SI' : 'NO',
            'env_writable' => is_really_writable(ROOTPATH . '.env')
        ];
        
        // Controlla se è una richiesta AJAX
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($data);
        }
        
        // Altrimenti mostra una semplice pagina HTML
        $html = '<html><head><title>Test Diagnostic</title></head><body>';
        $html .= '<h1>Test diagnostico installazione</h1>';
        $html .= '<ul>';
        foreach ($data as $key => $value) {
            $html .= '<li><strong>' . $key . ':</strong> ' . (is_bool($value) ? ($value ? 'SI' : 'NO') : $value) . '</li>';
        }
        $html .= '</ul>';
        $html .= '<p><a href="' . site_url('install') . '">Vai alla pagina di installazione</a></p>';
        $html .= '<p><a href="' . site_url() . '">Vai alla home</a></p>';
        $html .= '</body></html>';
        
        return $this->response->setBody($html);
    }
} 