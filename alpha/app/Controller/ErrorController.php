<?php
namespace App\Controller;

class ErrorController
{
    public function notFound()
    {
        return $this->code('ERR001');
    }

    public function code($code){
        switch ($code) {
            case 'ERR001':
                http_response_code(404);
                render('errors/404');
                break;
            case 'ERR002':
                http_response_code(403);
                render('errors/403');
                break;
            default:
                http_response_code(400);
                echo "Errore sconosciuto";
        }
    }
}
