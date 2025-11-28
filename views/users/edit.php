<?php include __DIR__ . '/../layout/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Modifica utente</h5>
            </div>
            <form class="card-body" method="post">
                <?= csrfTokenField(); ?>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?= $user['email']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nuova password (opzionale)</label>
                    <input type="password" class="form-control" name="password">
                </div>
                <div class="mb-3">
                    <label class="form-label">Ruolo</label>
                    <select class="form-select" name="role">
                        <option value="affiliato" <?= $user['role'] === 'affiliato' ? 'selected' : ''; ?>>Affiliato</option>
                        <option value="superadmin" <?= $user['role'] === 'superadmin' ? 'selected' : ''; ?>>SuperAdmin</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Stato</label>
                    <select class="form-select" name="status">
                        <option value="active" <?= $user['status'] === 'active' ? 'selected' : ''; ?>>Attivo</option>
                        <option value="suspended" <?= $user['status'] === 'suspended' ? 'selected' : ''; ?>>Sospeso</option>
                    </select>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= url('users'); ?>" class="btn btn-outline-secondary">Annulla</a>
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layout/footer.php'; ?>
