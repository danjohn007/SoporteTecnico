<?php
/**
 * Dashboard Controller
 * User dashboard with tickets overview
 */

require_once 'BaseController.php';

class DashboardController extends BaseController {
    private $ticketModel;
    private $userModel;
    
    public function __construct() {
        requireLogin();
        $this->ticketModel = $this->model('Ticket');
        $this->userModel = $this->model('User');
    }
    
    /**
     * Dashboard index - Different views based on role
     */
    public function index() {
        $user = getCurrentUser();
        
        if (isAdmin()) {
            return $this->adminDashboard();
        } elseif (isAgent()) {
            return $this->agentDashboard();
        } else {
            return $this->userDashboard();
        }
    }
    
    /**
     * User Dashboard
     */
    private function userDashboard() {
        $userId = $_SESSION['user_id'];
        
        // Get user tickets
        $tickets = $this->ticketModel->getByUser($userId);
        
        // Get user stats
        $stats = $this->userModel->getStats($userId);
        
        $this->view('dashboard/user', [
            'title' => 'Mi Dashboard - ' . SITE_NAME,
            'tickets' => $tickets,
            'stats' => $stats
        ]);
    }
    
    /**
     * Agent Dashboard
     */
    private function agentDashboard() {
        $agentId = $_SESSION['user_id'];
        
        // Get assigned tickets
        $myTickets = $this->ticketModel->getByAgent($agentId);
        
        // Get unassigned tickets
        $unassignedTickets = $this->ticketModel->getAll(['agent_id' => null], 20);
        
        // Get statistics
        $stats = $this->ticketModel->getStatistics(['agent_id' => $agentId]);
        
        $this->view('dashboard/agent', [
            'title' => 'Panel de Agente - ' . SITE_NAME,
            'myTickets' => $myTickets,
            'unassignedTickets' => $unassignedTickets,
            'stats' => $stats
        ]);
    }
    
    /**
     * Admin Dashboard
     */
    private function adminDashboard() {
        // Get all tickets with filters
        $filters = [
            'estado' => $this->getQuery('estado'),
            'prioridad' => $this->getQuery('prioridad')
        ];
        
        $tickets = $this->ticketModel->getAll($filters, 50);
        
        // Get overall statistics
        $stats = $this->ticketModel->getStatistics();
        
        // Get all users
        $users = $this->userModel->getAll();
        
        $this->view('dashboard/admin', [
            'title' => 'Panel de Administración - ' . SITE_NAME,
            'tickets' => $tickets,
            'stats' => $stats,
            'users' => $users,
            'filters' => $filters
        ]);
    }
}
