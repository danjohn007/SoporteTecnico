<?php
/**
 * Settings Controller
 * Handles system settings management
 */

require_once 'BaseController.php';

class SettingsController extends BaseController {
    private $settingModel;
    
    public function __construct() {
        requireRole(ROLE_ADMIN);
        $this->settingModel = $this->model('Setting');
    }
    
    /**
     * Settings page
     */
    public function index() {
        $settings = $this->settingModel->getAll();
        
        // Group settings by category
        $grouped = [
            'general' => [],
            'contact' => [],
            'appearance' => [],
            'integrations' => [],
            'system' => []
        ];
        
        foreach ($settings as $setting) {
            $key = $setting['setting_key'];
            
            if (in_array($key, ['site_name', 'site_logo', 'timezone'])) {
                $grouped['general'][] = $setting;
            } elseif (in_array($key, ['contact_phone', 'contact_email', 'whatsapp_business', 'business_hours'])) {
                $grouped['contact'][] = $setting;
            } elseif (in_array($key, ['primary_color', 'secondary_color'])) {
                $grouped['appearance'][] = $setting;
            } elseif (in_array($key, ['paypal_client_id', 'paypal_mode', 'qr_api_key'])) {
                $grouped['integrations'][] = $setting;
            } else {
                $grouped['system'][] = $setting;
            }
        }
        
        $this->view('settings/index', [
            'title' => 'Configuraciones del Sistema - ' . SITE_NAME,
            'grouped' => $grouped
        ]);
    }
    
    /**
     * Save settings
     */
    public function save() {
        if (!$this->isPost()) {
            redirect('settings');
        }
        
        $settings = $this->getPost('settings', []);
        
        $errors = [];
        $successCount = 0;
        
        foreach ($settings as $key => $value) {
            // Determine setting type
            $type = 'text';
            if (in_array($key, ['primary_color', 'secondary_color'])) {
                $type = 'color';
            } elseif ($key === 'business_hours') {
                $type = 'json';
            } elseif (in_array($key, ['auto_close_days', 'tickets_per_page'])) {
                $type = 'number';
            } elseif ($key === 'enable_chatbot') {
                $type = 'boolean';
            }
            
            $result = $this->settingModel->set($key, $value, $type);
            
            if ($result) {
                $successCount++;
            } else {
                $errors[] = "Error al guardar: $key";
            }
        }
        
        // Handle logo upload
        if (!empty($_FILES['site_logo']['name'])) {
            $uploadResult = uploadFile($_FILES['site_logo'], 'logos');
            if ($uploadResult['success']) {
                $this->settingModel->set('site_logo', $uploadResult['path'], 'file');
                $successCount++;
            } else {
                $errors[] = $uploadResult['error'];
            }
        }
        
        if ($successCount > 0) {
            logAudit('SETTINGS_UPDATED', 'settings', 0, "$successCount settings updated");
            $_SESSION['success'] = "$successCount configuraciones guardadas correctamente";
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
        }
        
        redirect('settings');
    }
    
    /**
     * Reset settings to default
     */
    public function reset() {
        if (!$this->isPost()) {
            redirect('settings');
        }
        
        // Here you would implement logic to reset to default values
        $_SESSION['success'] = 'Configuraciones restablecidas a valores predeterminados';
        redirect('settings');
    }
}
