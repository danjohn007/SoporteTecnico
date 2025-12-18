<?php
/**
 * FAQ Controller
 * Handles FAQ listing and search
 */

require_once 'BaseController.php';

class FaqController extends BaseController {
    private $faqModel;
    private $categoryModel;
    
    public function __construct() {
        $this->faqModel = $this->model('FAQ');
        $this->categoryModel = $this->model('Category');
    }
    
    /**
     * List all FAQs
     */
    public function index() {
        $categoryId = $this->getQuery('category');
        $search = $this->getQuery('search');
        
        if ($search) {
            $faqs = $this->faqModel->search($search);
        } elseif ($categoryId) {
            $faqs = $this->faqModel->getAll($categoryId);
        } else {
            $faqs = $this->faqModel->getAll();
        }
        
        $categories = $this->categoryModel->getAll();
        
        $this->view('faq/index', [
            'title' => 'Preguntas Frecuentes - ' . SITE_NAME,
            'faqs' => $faqs,
            'categories' => $categories,
            'currentCategory' => $categoryId,
            'searchQuery' => $search
        ]);
    }
    
    /**
     * View single FAQ
     */
    public function view($faqId) {
        $faq = $this->faqModel->findById($faqId);
        
        if (!$faq) {
            $_SESSION['errors'] = ['FAQ no encontrada'];
            redirect('faq');
        }
        
        // Increment views
        $this->faqModel->incrementViews($faqId);
        
        // Get related FAQs
        $relatedFaqs = [];
        if ($faq['category_id']) {
            $relatedFaqs = array_slice($this->faqModel->getAll($faq['category_id']), 0, 5);
        }
        
        $this->view('faq/view', [
            'title' => $faq['pregunta'] . ' - ' . SITE_NAME,
            'faq' => $faq,
            'relatedFaqs' => $relatedFaqs
        ]);
    }
    
    /**
     * Mark FAQ as helpful
     */
    public function helpful($faqId) {
        if (!$this->isPost()) {
            redirect('faq/view/' . $faqId);
        }
        
        $isHelpful = $this->getPost('helpful') === 'yes';
        
        $this->faqModel->markHelpful($faqId, $isHelpful);
        
        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => 'Gracias por tu feedback']);
        } else {
            $_SESSION['success'] = 'Gracias por tu feedback';
            redirect('faq/view/' . $faqId);
        }
    }
    
    /**
     * Search FAQs (AJAX)
     */
    public function search() {
        if (!$this->isAjax()) {
            redirect('faq');
        }
        
        $query = $this->getQuery('q', '');
        
        if (empty($query)) {
            $this->json(['results' => []]);
        }
        
        $faqs = $this->faqModel->search($query);
        
        $this->json(['results' => $faqs]);
    }
}
