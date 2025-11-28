<?php
// controllers/ProfileController.php - personal profile

declare(strict_types=1);

class ProfileController
{
    private User $userModel;
    private Contract $contractModel;
    private Notification $notificationModel;
    private ActivityLog $activityLog;

    public function __construct()
    {
        $this->userModel = new User();
        $this->contractModel = new Contract();
        $this->notificationModel = new Notification();
        $this->activityLog = new ActivityLog();
    }

    public function index(): void
    {
        requireAuth();
        $user = getCurrentUser();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('profile', 'Token CSRF non valido', 'danger');
                redirect('/profile');
            }
            $data = [
                'email' => sanitize($_POST['email'] ?? $user['email'])
            ];
            if (!empty($_POST['password'])) {
                $data['password'] = $_POST['password'];
            }
            $this->userModel->update($user['id'], $data);
            setFlash('profile', 'Profilo aggiornato', 'success');
            redirect('/profile');
        }
        $isAdmin = $user['role'] === 'superadmin';
        $contracts = $isAdmin
            ? $this->contractModel->getAll()
            : $this->contractModel->getAll($user['id'], $user['role']);
        $contractStats = $this->buildContractStats($contracts);
        $recentContracts = array_slice($contracts, 0, 4);
        $notifications = $this->notificationModel->getByUser($user['id'], 4);
        $recentActivities = $this->collectActivities((int) $user['id'], $isAdmin);
        $teamStats = $isAdmin ? $this->collectTeamStats() : [];
        $affiliateMetrics = $isAdmin ? [] : $this->collectAffiliateMetrics($contracts);
        $highlightStats = $isAdmin
            ? $this->buildAdminHighlights($contractStats, $teamStats)
            : $this->buildAffiliateHighlights($contractStats, $affiliateMetrics);
        include __DIR__ . '/../views/profile/index.php';
    }

    private function buildContractStats(array $contracts): array
    {
        $stats = [
            'totali' => count($contracts),
            'in_attesa' => 0,
            'in_lavorazione' => 0,
            'in_verifica' => 0,
            'attivato' => 0,
            'annullato' => 0,
            'rigettato' => 0,
        ];
        foreach ($contracts as $contract) {
            $status = $contract['status'] ?? 'in_attesa';
            if (isset($stats[$status])) {
                $stats[$status]++;
            }
        }
        return $stats;
    }

    private function collectActivities(int $userId, bool $isAdmin): array
    {
        $entries = $this->activityLog->latest(20);
        if ($isAdmin) {
            return array_slice($entries, 0, 6);
        }
        $filtered = array_values(array_filter($entries, static fn($entry) => (int) ($entry['user_id'] ?? 0) === $userId));
        return array_slice($filtered, 0, 6);
    }

    private function collectTeamStats(): array
    {
        $users = $this->userModel->getAll();
        $stats = [
            'totalUsers' => count($users),
            'totalAffiliates' => 0,
            'activeAffiliates' => 0,
            'suspendedAffiliates' => 0,
            'superadmins' => 0
        ];
        foreach ($users as $entry) {
            if (($entry['role'] ?? '') === 'affiliato') {
                $stats['totalAffiliates']++;
                if (($entry['status'] ?? 'active') === 'active') {
                    $stats['activeAffiliates']++;
                } else {
                    $stats['suspendedAffiliates']++;
                }
            }
            if (($entry['role'] ?? '') === 'superadmin') {
                $stats['superadmins']++;
            }
        }
        return $stats;
    }

    private function collectAffiliateMetrics(array $contracts): array
    {
        $monthAgo = strtotime('-30 days');
        $last30 = 0;
        $activated = 0;
        foreach ($contracts as $contract) {
            if (!empty($contract['created_at']) && strtotime($contract['created_at']) >= $monthAgo) {
                $last30++;
            }
            if (($contract['status'] ?? '') === 'attivato') {
                $activated++;
            }
        }
        $goal = 12;
        $progress = $goal > 0 ? (int) min(100, round(($last30 / $goal) * 100)) : 0;
        return [
            'last30' => $last30,
            'activated' => $activated,
            'goal' => $goal,
            'progress' => $progress
        ];
    }

    private function buildAdminHighlights(array $stats, array $teamStats): array
    {
        return [
            [
                'label' => 'Affiliati attivi',
                'value' => $teamStats['activeAffiliates'] ?? 0,
                'hint' => ($teamStats['totalAffiliates'] ?? 0) . ' totali'
            ],
            [
                'label' => 'Superadmin',
                'value' => $teamStats['superadmins'] ?? 0,
                'hint' => 'core team'
            ],
            [
                'label' => 'Contratti totali',
                'value' => $stats['totali'] ?? 0,
                'hint' => 'storico piattaforma'
            ],
            [
                'label' => 'In verifica',
                'value' => $stats['in_verifica'] ?? 0,
                'hint' => 'richiede attenzione'
            ]
        ];
    }

    private function buildAffiliateHighlights(array $stats, array $metrics): array
    {
        return [
            [
                'label' => 'Contratti totali',
                'value' => $stats['totali'] ?? 0,
                'hint' => 'storico personale'
            ],
            [
                'label' => 'Attivati',
                'value' => $stats['attivato'] ?? 0,
                'hint' => 'successi registrati'
            ],
            [
                'label' => 'In lavorazione',
                'value' => $stats['in_lavorazione'] ?? 0,
                'hint' => 'trattative aperte'
            ],
            [
                'label' => 'Ultimi 30gg',
                'value' => ($metrics['last30'] ?? 0) . '/' . ($metrics['goal'] ?? 12),
                'hint' => 'avanzamento obiettivo'
            ]
        ];
    }
}

?>
