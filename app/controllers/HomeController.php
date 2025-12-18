<?php
/**
 * Home Controller
 * Handles homepage and public pages
 */

require_once 'BaseController.php';

class HomeController extends BaseController {
    
    public function index() {
        // If user is logged in, redirect to dashboard
        if (isLoggedIn()) {
            redirect('dashboard');
        }
        
        $faqModel = $this->model('FAQ');
        $popularFaqs = $faqModel->getPopular(6);
        
        $this->view('home/index', [
            'title' => 'Inicio - ' . SITE_NAME,
            'popularFaqs' => $popularFaqs
        ]);
    }
    
    public function about() {
        $this->view('home/about', [
            'title' => 'Acerca de - ' . SITE_NAME
        ]);
    }
    
    public function contact() {
        $this->view('home/contact', [
            'title' => 'Contacto - ' . SITE_NAME
        ]);
    }
    
    public function notFound() {
        http_response_code(404);
        $this->view('errors/404', [
            'title' => 'Página no encontrada'
        ]);
    }
}
