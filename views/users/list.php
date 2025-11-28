<?php include __DIR__ . '/../layout/header.php'; ?>
<section class="ghost-page-header">
    <div>
        <p class="text-muted mb-1 text-uppercase small">Operations · Affiliati</p>
        <h1>Gestione affiliati</h1>
        <p class="text-muted mb-0">Crea, attiva o sospendi i rivenditori affiliati.</p>
    </div>
    <div class="ghost-page-actions">
        <a href="<?= url('users/create'); ?>" class="ghost-button"><i class="bi bi-person-plus"></i>Nuovo affiliato</a>
    </div>
</section>
<section class="ghost-card">
    <div class="responsive-table ghost-table-wrapper">
        <table class="ghost-table align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Ruolo</th>
                    <th>Stato</th>
                    <th class="text-end">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $item): ?>
                    <tr>
                        <td><?= $item['id']; ?></td>
                        <td><?= $item['username']; ?></td>
                        <td><?= $item['email']; ?></td>
                        <td><span class="badge bg-secondary text-uppercase"><?= $item['role']; ?></span></td>
                        <td>
                            <span class="badge bg-<?= $item['status'] === 'active' ? 'success' : 'warning'; ?>">
                                <?= ucfirst($item['status']); ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="ghost-page-actions justify-content-end">
                                <a href="<?= url('users/edit/' . $item['id']); ?>" class="ghost-button secondary"><i class="bi bi-pencil"></i>Modifica</a>
                                <a href="<?= url('users/toggle/' . $item['id']); ?>" class="ghost-button secondary"><i class="bi bi-arrow-repeat"></i>Toggle</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/../layout/footer.php'; ?>
