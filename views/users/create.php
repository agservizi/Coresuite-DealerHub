<?php include __DIR__ . '/../layout/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Nuovo affiliato</h5>
            </div>
            <form class="card-body" method="post">
                <?= csrfTokenField(); ?>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ruolo</label>
                    <select class="form-select" name="role">
                        <option value="affiliato">Affiliato</option>
                        <option value="superadmin">SuperAdmin</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Stato</label>
                    <select class="form-select" name="status">
                        <option value="active">Attivo</option>
                        <option value="suspended">Sospeso</option>
                    </select>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="/users" class="btn btn-outline-secondary">Annulla</a>
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layout/footer.php'; ?>
