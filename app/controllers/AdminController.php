<?php
/**
 * Admin Controller
 * Handles administration tasks
 */

require_once 'BaseController.php';

class AdminController extends BaseController {
    private $userModel;
    private $ticketModel;
    private $categoryModel;
    private $faqModel;
    private $settingModel;
    
    public function __construct() {
        requireRole(ROLE_ADMIN);
        $this->userModel = $this->model('User');
        $this->ticketModel = $this->model('Ticket');
        $this->categoryModel = $this->model('Category');
        $this->faqModel = $this->model('FAQ');
        $this->settingModel = $this->model('Setting');
    }
    
    /**
     * Admin dashboard
     */
    public function index() {
        redirect('dashboard');
    }
    
    /**
     * Manage users
     */
    public function users() {
        $users = $this->userModel->getAll();
        
        $this->view('admin/users', [
            'title' => 'Gestión de Usuarios - ' . SITE_NAME,
            'users' => $users
        ]);
    }
    
    /**
     * Manage categories
     */
    public function categories() {
        $categories = $this->categoryModel->getAll();
        
        $this->view('admin/categories', [
            'title' => 'Gestión de Categorías - ' . SITE_NAME,
            'categories' => $categories
        ]);
    }
    
    /**
     * Create/Edit category
     */
    public function saveCategory() {
        if (!$this->isPost()) {
            redirect('admin/categories');
        }
        
        $id = $this->getPost('id');
        $nombre = sanitize($this->getPost('nombre'));
        $descripcion = sanitize($this->getPost('descripcion'));
        
        if (empty($nombre)) {
            $_SESSION['errors'] = ['El nombre es requerido'];
            redirect('admin/categories');
        }
        
        if ($id) {
            $result = $this->categoryModel->update($id, $nombre, $descripcion);
            $message = 'Categoría actualizada correctamente';
        } else {
            $result = $this->categoryModel->create($nombre, $descripcion);
            $message = 'Categoría creada correctamente';
        }
        
        if ($result) {
            $_SESSION['success'] = $message;
        } else {
            $_SESSION['errors'] = ['Error al guardar la categoría'];
        }
        
        redirect('admin/categories');
    }
    
    /**
     * Delete category
     */
    public function deleteCategory($id) {
        if (!$this->isPost()) {
            redirect('admin/categories');
        }
        
        $result = $this->categoryModel->delete($id);
        
        if ($result) {
            $_SESSION['success'] = 'Categoría eliminada correctamente';
        } else {
            $_SESSION['errors'] = ['Error al eliminar la categoría'];
        }
        
        redirect('admin/categories');
    }
    
    /**
     * Manage FAQs
     */
    public function faqs() {
        $faqs = $this->faqModel->getAll();
        $categories = $this->categoryModel->getAll();
        
        $this->view('admin/faqs', [
            'title' => 'Gestión de FAQs - ' . SITE_NAME,
            'faqs' => $faqs,
            'categories' => $categories
        ]);
    }
    
    /**
     * Create/Edit FAQ
     */
    public function saveFaq() {
        if (!$this->isPost()) {
            redirect('admin/faqs');
        }
        
        $id = $this->getPost('id');
        $categoryId = $this->getPost('category_id') ?: null;
        $pregunta = sanitize($this->getPost('pregunta'));
        $respuesta = sanitize($this->getPost('respuesta'));
        $keywords = sanitize($this->getPost('keywords'));
        
        if (empty($pregunta) || empty($respuesta)) {
            $_SESSION['errors'] = ['Pregunta y respuesta son requeridos'];
            redirect('admin/faqs');
        }
        
        if ($id) {
            $result = $this->faqModel->update($id, $categoryId, $pregunta, $respuesta, $keywords);
            $message = 'FAQ actualizada correctamente';
        } else {
            $result = $this->faqModel->create($categoryId, $pregunta, $respuesta, $keywords);
            $message = 'FAQ creada correctamente';
        }
        
        if ($result) {
            $_SESSION['success'] = $message;
        } else {
            $_SESSION['errors'] = ['Error al guardar la FAQ'];
        }
        
        redirect('admin/faqs');
    }
    
    /**
     * Delete FAQ
     */
    public function deleteFaq($id) {
        if (!$this->isPost()) {
            redirect('admin/faqs');
        }
        
        $result = $this->faqModel->delete($id);
        
        if ($result) {
            $_SESSION['success'] = 'FAQ eliminada correctamente';
        } else {
            $_SESSION['errors'] = ['Error al eliminar la FAQ'];
        }
        
        redirect('admin/faqs');
    }
    
    /**
     * Update user role
     */
    public function updateUserRole() {
        if (!$this->isPost()) {
            redirect('admin/users');
        }
        
        $userId = $this->getPost('user_id');
        $role = $this->getPost('role');
        
        if (!in_array($role, [ROLE_USER, ROLE_AGENT, ROLE_ADMIN])) {
            $_SESSION['errors'] = ['Rol inválido'];
            redirect('admin/users');
        }
        
        $result = $this->userModel->update($userId, ['role' => $role]);
        
        if ($result) {
            logAudit('USER_ROLE_CHANGED', 'user', $userId, "Role changed to: $role");
            $_SESSION['success'] = 'Rol actualizado correctamente';
        } else {
            $_SESSION['errors'] = ['Error al actualizar el rol'];
        }
        
        redirect('admin/users');
    }
    
    /**
     * Deactivate user
     */
    public function deactivateUser($userId) {
        if (!$this->isPost()) {
            redirect('admin/users');
        }
        
        $result = $this->userModel->deactivate($userId);
        
        if ($result) {
            logAudit('USER_DEACTIVATED', 'user', $userId, 'User deactivated');
            $_SESSION['success'] = 'Usuario desactivado correctamente';
        } else {
            $_SESSION['errors'] = ['Error al desactivar el usuario'];
        }
        
        redirect('admin/users');
    }
    
    /**
     * Reports and metrics
     */
    public function reports() {
        $stats = $this->ticketModel->getStatistics();
        
        // Get ticket trends (last 30 days)
        $dateFrom = date('Y-m-d', strtotime('-30 days'));
        $dateTo = date('Y-m-d');
        
        $this->view('admin/reports', [
            'title' => 'Reportes y Métricas - ' . SITE_NAME,
            'stats' => $stats,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
    }
}
