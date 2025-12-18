<?php
/**
 * Auth Controller
 * Handles user authentication (register, login, logout)
 */

require_once 'BaseController.php';

class AuthController extends BaseController {
    private $userModel;
    private $sessionModel;
    
    public function __construct() {
        $this->userModel = $this->model('User');
        $this->sessionModel = $this->model('Session');
    }
    
    /**
     * Show registration form
     */
    public function register() {
        if (isLoggedIn()) {
            redirect('dashboard');
        }
        
        $this->view('auth/register', [
            'title' => 'Registro - ' . SITE_NAME
        ]);
    }
    
    /**
     * Process registration
     */
    public function doRegister() {
        if (!$this->isPost()) {
            redirect('auth/register');
        }
        
        $nombreCompleto = sanitize($this->getPost('nombre_completo'));
        $whatsapp = $this->getPost('whatsapp');
        
        // Validate inputs
        $errors = [];
        
        if (empty($nombreCompleto)) {
            $errors[] = 'El nombre completo es requerido';
        }
        
        if (empty($whatsapp)) {
            $errors[] = 'El número de WhatsApp es requerido';
        } else {
            $whatsapp = validateWhatsApp($whatsapp);
            if (!$whatsapp) {
                $errors[] = 'Formato de WhatsApp inválido. Use el formato: +52 442 123 4567';
            }
        }
        
        // Check if WhatsApp already exists
        if ($whatsapp && $this->userModel->findByWhatsApp($whatsapp)) {
            $errors[] = 'Este número de WhatsApp ya está registrado. <a href="' . url('auth/login') . '" class="underline">Inicia sesión aquí</a>';
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            redirect('auth/register');
        }
        
        // Create user
        $userId = $this->userModel->create($nombreCompleto, $whatsapp, ROLE_USER);
        
        if ($userId) {
            // Create session
            $token = $this->sessionModel->create($userId);
            
            if ($token) {
                // Set session data
                $_SESSION['user_id'] = $userId;
                $_SESSION['token'] = $token;
                $_SESSION['user_data'] = $this->userModel->findById($userId);
                
                // Log audit
                logAudit('USER_REGISTERED', 'user', $userId, 'New user registered');
                
                $_SESSION['success'] = '¡Bienvenido! Tu cuenta ha sido creada exitosamente.';
                redirect('dashboard');
            }
        }
        
        $_SESSION['errors'] = ['Error al crear la cuenta. Por favor intenta de nuevo.'];
        redirect('auth/register');
    }
    
    /**
     * Show login form
     */
    public function login() {
        if (isLoggedIn()) {
            redirect('dashboard');
        }
        
        $this->view('auth/login', [
            'title' => 'Iniciar Sesión - ' . SITE_NAME
        ]);
    }
    
    /**
     * Process login
     */
    public function doLogin() {
        if (!$this->isPost()) {
            redirect('auth/login');
        }
        
        $whatsapp = $this->getPost('whatsapp');
        
        // Validate input
        if (empty($whatsapp)) {
            $_SESSION['errors'] = ['El número de WhatsApp es requerido'];
            redirect('auth/login');
        }
        
        $whatsapp = validateWhatsApp($whatsapp);
        if (!$whatsapp) {
            $_SESSION['errors'] = ['Formato de WhatsApp inválido'];
            redirect('auth/login');
        }
        
        // Find user
        $user = $this->userModel->findByWhatsApp($whatsapp);
        
        if (!$user) {
            $_SESSION['errors'] = ['No existe una cuenta con este número de WhatsApp. <a href="' . url('auth/register') . '" class="underline">Regístrate aquí</a>'];
            redirect('auth/login');
        }
        
        // Create session
        $token = $this->sessionModel->create($user['id']);
        
        if ($token) {
            // Set session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['token'] = $token;
            $_SESSION['user_data'] = $user;
            
            // Log audit
            logAudit('USER_LOGIN', 'user', $user['id'], 'User logged in');
            
            // Redirect to intended page or dashboard
            $redirectTo = $_SESSION['redirect_after_login'] ?? 'dashboard';
            unset($_SESSION['redirect_after_login']);
            
            redirect($redirectTo);
        }
        
        $_SESSION['errors'] = ['Error al iniciar sesión. Por favor intenta de nuevo.'];
        redirect('auth/login');
    }
    
    /**
     * Logout
     */
    public function logout() {
        if (isset($_SESSION['token'])) {
            $this->sessionModel->delete($_SESSION['token']);
            logAudit('USER_LOGOUT', 'user', $_SESSION['user_id'] ?? null, 'User logged out');
        }
        
        session_destroy();
        redirect('home');
    }
}
