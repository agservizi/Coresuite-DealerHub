<?php
// controllers/ReportsController.php - KPI and charts

declare(strict_types=1);

class ReportsController
{
    private Contract $contractModel;

    public function __construct()
    {
        $this->contractModel = new Contract();
    }

    public function index(): void
    {
        requireAuth();
        if (!hasRole('superadmin')) {
            redirect('/dashboard');
        }
        $stats = $this->contractModel->stats();
        include __DIR__ . '/../views/reports/index.php';
    }
}

?>
