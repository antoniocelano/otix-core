<?php
namespace App\Controller;
use App\Core\Notify;

class SiteController
{
    /**
     * Mostra la pagina principale del sito.
     * Questo metodo viene raggiunto SOLO se is_site è true.
     */
    public function index(): void
    {
        $lang = current_lang();
        render('index', compact('lang'));
    }

    public function dbGuide(): void
    {
        $lang = current_lang();
        render('db_guide', compact('lang'));
    }

    public function admin(?string $page = null): void
    {
        $lang = current_lang();
        $sub = $page ?: 'index';
        $view = "admin/{$sub}";
        $viewPath = __DIR__ . "/../../resources/views/" . THEME_DIR . "/{$view}.php";

        // Se la vista non esiste nel tema, cerca nella cartella base delle viste
        if (!file_exists($viewPath)) {
            $viewPath = __DIR__ . "/../../resources/views/{$view}.php";
        }
        
        // Se non esiste neanche lì, errore 404
        if (!file_exists($viewPath)) {
            (new \App\Controller\ErrorController())->code('ERR001');
            return;
        }

        render($view, compact('lang'));
    }
}