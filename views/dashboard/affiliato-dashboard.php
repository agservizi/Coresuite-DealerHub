<?php include __DIR__ . '/../layout/header.php'; ?>
<?php $contractModel = new Contract(); $user = getCurrentUser(); $contracts = $contractModel->getAll($user['id'], $user['role']); ?>
<section class="ghost-page-header">
    <div>
        <p class="text-muted mb-1 text-uppercase small">Affiliate workspace</p>
        <h1>Ciao <?= ucfirst($user['username']); ?></h1>
        <p class="text-muted mb-0">Controlla lo stato delle tue pratiche e carica rapidamente nuovi contratti.</p>
    </div>
    <div class="ghost-page-actions">
        <a href="<?= url('contracts/create'); ?>" class="ghost-button"><i class="bi bi-plus-circle"></i>Nuovo contratto</a>
    </div>
</section>
<section class="ghost-grid kpi">
    <article class="ghost-kpi">
        <span>Contratti caricati</span>
        <strong><?= count($contracts); ?></strong>
        <small class="text-muted">Totale personale</small>
    </article>
    <article class="ghost-kpi">
        <span>In verifica</span>
        <strong><?= count(array_filter($contracts, fn($c) => $c['status'] === 'in_verifica')); ?></strong>
        <small class="text-muted">Compliance team</small>
    </article>
    <article class="ghost-kpi">
        <span>In lavorazione</span>
        <strong><?= count(array_filter($contracts, fn($c) => $c['status'] === 'in_lavorazione')); ?></strong>
        <small class="text-muted">Backoffice</small>
    </article>
</section>
<section class="ghost-grid" style="grid-template-columns: minmax(0, 3fr) minmax(0, 2fr); margin-top:1.5rem;">
    <article class="ghost-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">I miei contratti</h5>
            <a class="ghost-button secondary" href="<?= url('contracts'); ?>">Vedi tutti</a>
        </div>
        <div class="ghost-table-wrapper">
            <table class="ghost-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Stato</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($contracts, 0, 6) as $contract): ?>
                        <tr>
                            <td>#<?= $contract['id']; ?></td>
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
        <h5 class="mb-3">Copertura rete</h5>
        <div class="ghost-list">
            <a class="ghost-button secondary" href="https://fastweb.it/copertura" target="_blank">Fastweb Wholesale</a>
            <a class="ghost-button secondary" href="https://www.openfiber.it/verifica-copertura" target="_blank">OpenFiber</a>
            <a class="ghost-button secondary" href="https://fibercop.it" target="_blank">FiberCop / TIM</a>
            <a class="ghost-button secondary" href="https://www.windtrebusiness.it/copertura" target="_blank">WindTre Wholesale</a>
            <a class="ghost-button secondary" href="https://www.iliad.it/fibra" target="_blank">Iliad FTTH</a>
            <a class="ghost-button secondary" href="https://www.vodafone.it/copertura" target="_blank">Vodafone Rete Fissa</a>
        </div>
    </article>
</section>
<?php include __DIR__ . '/../layout/footer.php'; ?>
