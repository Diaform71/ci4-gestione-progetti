<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Auth implements FilterInterface
{
    /**
     * Verifica se l'utente è autenticato.
     * Se non lo è, lo reindirizza alla pagina di login.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('utente_logged_in')) {
            return redirect()->to('/login')->with('error', 'Devi effettuare il login per accedere a questa pagina');
        }
    }

    /**
     * Non utilizzato in questo caso.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Non sono necessarie azioni post-filtro
    }
}
