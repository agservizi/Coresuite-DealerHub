<?php include __DIR__ . '/../layout/header.php'; ?>
<?php
$contractModel = new Contract();
$stats = $contractModel->stats();
$totals = [
    'totali' => 0,
    'telefonia' => 0,
    'luce' => 0,
    'gas' => 0,
];
$statusCounters = [
    'in_attesa' => 0,
    'in_lavorazione' => 0,
    'in_verifica' => 0,
    'attivato' => 0
];
foreach ($stats as $item) {
    $totals['totali'] += $item['total'];
    $totals[$item['type']] = ($totals[$item['type']] ?? 0) + $item['total'];
    $statusCounters[$item['status']] = ($statusCounters[$item['status']] ?? 0) + $item['total'];
}
$recentContracts = array_slice($contractModel->getAll(), 0, 3);
$log = new ActivityLog();
$entries = $log->latest();
?>
<section class="ghost-page-header">
    <div>
        <p class="text-muted mb-1 text-uppercase small">Dashboard</p>
        <h1>Overview contratti</h1>
        <p class="text-muted mb-0">Monitoraggio in tempo reale delle performance rete.</p>
    </div>
    <div class="ghost-page-actions">
        <a href="<?= url('reports'); ?>" class="ghost-button secondary"><i class="bi bi-graph-up"></i>Report avanzati</a>
        <a href="<?= url('contracts/create'); ?>" class="ghost-button"><i class="bi bi-rocket"></i>Nuovo contratto</a>
    </div>
</section>
<section class="ghost-grid kpi">
    <article class="ghost-kpi">
        <span>Contratti totali</span>
        <strong><?= $totals['totali']; ?></strong>
        <small class="text-muted">somma complessiva</small>
    </article>
    <article class="ghost-kpi">
        <span>Telefonia</span>
        <strong><?= $totals['telefonia']; ?></strong>
        <small class="text-muted">linee voce e dati</small>
    </article>
    <article class="ghost-kpi">
        <span>Luce</span>
        <strong><?= $totals['luce']; ?></strong>
        <small class="text-muted">fornitura energia</small>
    </article>
    <article class="ghost-kpi">
        <span>Gas</span>
        <strong><?= $totals['gas']; ?></strong>
        <small class="text-muted">utenze gas</small>
    </article>
    <article class="ghost-kpi">
        <span>In verifica</span>
        <strong><?= $statusCounters['in_verifica'] ?? 0; ?></strong>
        <small class="text-muted">compliance team</small>
    </article>
    <article class="ghost-kpi">
        <span>Attivati</span>
        <strong><?= $statusCounters['attivato'] ?? 0; ?></strong>
        <small class="text-muted">ultimi 30 giorni</small>
    </article>
</section>
<section class="ghost-grid" style="grid-template-columns: minmax(0, 3fr) minmax(0, 2fr);">
    <article class="ghost-card ghost-chart">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h5 class="mb-0">Andamento contratti</h5>
                <small class="text-muted">Trend mensile categorie</small>
            </div>
            <button class="ghost-button secondary" type="button"><i class="bi bi-download"></i>Esporta</button>
        </div>
        <canvas id="contractsChart" data-chart="contracts"></canvas>
    </article>
    <article class="ghost-card">
        <h5 class="mb-3">Stato pipeline</h5>
        <div class="ghost-grid" style="grid-template-columns: repeat(2, minmax(0,1fr));">
            <?php foreach ($statusCounters as $status => $count): ?>
                <div class="ghost-fieldset">
                    <p class="text-uppercase small text-muted mb-1"><?= str_replace('_', ' ', $status); ?></p>
                    <strong style="font-size:1.6rem;"><?= $count; ?></strong>
                    <div><?= formatStatusBadge($status); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
</section>
<section class="ghost-grid" style="grid-template-columns: minmax(0, 3fr) minmax(0, 2fr); margin-top:1.5rem;">
    <article class="ghost-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Contratti recenti</h5>
            <a href="<?= url('contracts'); ?>" class="ghost-button secondary">Vedi tutti</a>
        </div>
        <div class="ghost-table-wrapper">
            <table class="ghost-table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Stato</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentContracts as $contract): ?>
                        <tr>
                            <td><?= $contract['customer_name']; ?> <?= $contract['customer_surname']; ?></td>
                            <td class="text-capitalize"><?= $contract['type']; ?></td>
                            <td><?= formatStatusBadge($contract['status']); ?></td>
                            <td><?= formatDateTime($contract['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
    <article class="ghost-card">
        <h5 class="mb-3">Attività recenti</h5>
        <ul class="timeline">
            <?php foreach ($entries as $entry): ?>
                <li>
                    <strong><?= $entry['action']; ?></strong>
                    <small class="d-block text-muted"><?= formatDateTime($entry['created_at']); ?> · <?= $entry['username']; ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    </article>
</section>
<?php include __DIR__ . '/../layout/footer.php'; ?>
