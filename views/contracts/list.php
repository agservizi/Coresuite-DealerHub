<?php include __DIR__ . '/../layout/header.php'; ?>
<section class="ghost-page-header">
    <div>
        <p class="text-muted mb-1 text-uppercase small">Operations · Contracts</p>
        <h1>Gestione contratti</h1>
        <p class="text-muted mb-0">Visualizza, filtra e gestisci tutti i contratti <?= hasRole('superadmin') ? 'della rete' : 'che hai caricato'; ?>.</p>
    </div>
    <div class="ghost-page-actions">
        <?php if (hasRole('superadmin')): ?>
            <a href="<?= url('contracts/export'); ?>" class="ghost-button secondary"><i class="bi bi-download"></i>Export CSV</a>
        <?php endif; ?>
        <a href="<?= url('contracts/create'); ?>" class="ghost-button"><i class="bi bi-plus-circle"></i>Nuovo contratto</a>
    </div>
</section>
<section class="ghost-card">
    <div class="responsive-table ghost-table-wrapper">
        <table class="ghost-table align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Stato</th>
                    <th>Data</th>
                    <th class="text-end">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contracts as $contract): ?>
                    <tr>
                        <td>#<?= $contract['id']; ?></td>
                        <td><?= $contract['customer_name']; ?> <?= $contract['customer_surname']; ?></td>
                        <td class="text-capitalize"><?= $contract['type']; ?></td>
                        <td><?= formatStatusBadge($contract['status']); ?></td>
                        <td><?= formatDateTime($contract['created_at']); ?></td>
                        <td class="text-end">
                            <div class="ghost-page-actions justify-content-end">
                                <a href="<?= url('contracts/edit/' . $contract['id']); ?>" class="ghost-button secondary"><i class="bi bi-pencil"></i>Modifica</a>
                                <?php if (hasRole('superadmin')): ?>
                                    <a href="<?= url('contracts/delete/' . $contract['id']); ?>" class="ghost-button danger" onclick="return confirm('Confermi eliminazione?');"><i class="bi bi-trash"></i>Elimina</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/../layout/footer.php'; ?>
