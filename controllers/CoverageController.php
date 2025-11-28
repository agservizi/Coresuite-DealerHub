<?php
// controllers/CoverageController.php - network coverage links

declare(strict_types=1);

class CoverageController
{
    private array $providers = [
        'Fastweb Wholesale' => 'https://fastweb.it/copertura',
        'OpenFiber' => 'https://www.openfiber.it/verifica-copertura',
        'FiberCop / TIM' => 'https://www.tim.it/fibra-ottica',
        'WindTre Wholesale' => 'https://www.windtrebusiness.it/copertura',
        'Iliad FTTH' => 'https://www.iliad.it/fibra',
        'Vodafone Rete Fissa' => 'https://www.vodafone.it/copertura'
    ];

    public function index(): void
    {
        requireAuth();
        if (!hasAnyRole(['affiliato', 'superadmin'])) {
            redirect('/dashboard');
        }
        $links = $this->providers;
        include __DIR__ . '/../views/contracts/coverage.php';
    }
}

?>
