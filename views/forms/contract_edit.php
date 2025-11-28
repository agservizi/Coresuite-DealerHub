<?php include __DIR__ . '/../layout/header.php'; ?>
<section class="ghost-page-header">
    <div>
        <p class="text-muted mb-1 text-uppercase small">Workflow</p>
        <h1>Modifica contratto #<?= $contract['id']; ?></h1>
        <p class="text-muted mb-0">Aggiorna stato e dati anagrafici per mantenere la pipeline allineata.</p>
    </div>
    <div class="ghost-page-actions">
        <a href="<?= url('contracts'); ?>" class="ghost-button secondary"><i class="bi bi-arrow-left"></i>Lista contratti</a>
    </div>
</section>
<section class="ghost-form-card">
    <header>
        <div>
            <h5 class="mb-0">Dettagli contratto</h5>
            <small class="text-muted">Ultimo aggiornamento <?= formatDateTime($contract['created_at']); ?></small>
        </div>
        <?= formatStatusBadge($contract['status']); ?>
    </header>
    <form class="ghost-form" method="post">
        <?= csrfTokenField(); ?>
        <fieldset class="ghost-fieldset">
            <legend>Stato pratica</legend>
            <div class="ghost-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
                <div>
                    <label class="form-label">Stato</label>
                    <select class="ghost-select" name="status">
                        <?php $states = ['in_attesa','in_lavorazione','in_verifica','attivato','annullato','rigettato']; ?>
                        <?php foreach ($states as $state): ?>
                            <option value="<?= $state; ?>" <?= $contract['status'] === $state ? 'selected' : ''; ?>><?= ucfirst(str_replace('_',' ', $state)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </fieldset>
        <?php if (hasRole('superadmin')): ?>
            <fieldset class="ghost-fieldset">
                <legend>Anagrafica cliente</legend>
                <div class="ghost-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
                    <div>
                        <label class="form-label">Nome</label>
                        <input type="text" class="ghost-input" name="customer_name" value="<?= $contract['customer_name']; ?>" required>
                    </div>
                    <div>
                        <label class="form-label">Cognome</label>
                        <input type="text" class="ghost-input" name="customer_surname" value="<?= $contract['customer_surname']; ?>" required>
                    </div>
                    <div>
                        <label class="form-label">CF</label>
                        <input type="text" class="ghost-input" name="customer_cf" value="<?= $contract['customer_cf']; ?>" required>
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" class="ghost-input" name="customer_email" value="<?= $contract['customer_email']; ?>" required>
                    </div>
                    <div>
                        <label class="form-label">Telefono</label>
                        <input type="text" class="ghost-input" name="customer_phone" value="<?= $contract['customer_phone']; ?>" required>
                    </div>
                    <div>
                        <label class="form-label">Indirizzo</label>
                        <input type="text" class="ghost-input" name="customer_address" value="<?= $contract['customer_address']; ?>" required>
                    </div>
                </div>
            </fieldset>
            <fieldset class="ghost-fieldset">
                <legend>Documenti</legend>
                <div class="ghost-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
                    <div>
                        <label class="form-label">Tipo documento</label>
                        <input type="text" class="ghost-input" name="document_type" value="<?= $contract['document_type']; ?>" required>
                    </div>
                    <div>
                        <label class="form-label">Numero documento</label>
                        <input type="text" class="ghost-input" name="document_number" value="<?= $contract['document_number']; ?>" required>
                    </div>
                    <div>
                        <label class="form-label">Scadenza</label>
                        <input type="date" class="ghost-input" name="document_expiry" value="<?= $contract['document_expiry']; ?>" required>
                    </div>
                </div>
            </fieldset>
        <?php endif; ?>
        <fieldset class="ghost-fieldset">
            <legend>Note</legend>
            <textarea class="ghost-textarea" name="notes" rows="4"><?= $contract['notes']; ?></textarea>
        </fieldset>
        <div class="d-flex justify-content-end gap-2">
            <button type="submit" class="ghost-button">Aggiorna</button>
        </div>
    </form>
</section>
<?php include __DIR__ . '/../layout/footer.php'; ?>
<?php include __DIR__ . '/../layout/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Modifica contratto #<?= $contract['id']; ?></h4>
                <a href="<?= url('contracts'); ?>" class="btn btn-sm btn-outline-secondary">Indietro</a>
            </div>
            <form class="card-body" method="post">
                <?= csrfTokenField(); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Stato</label>
                        <select class="form-select" name="status">
                            <?php $states = ['in_attesa','in_lavorazione','in_verifica','attivato','annullato','rigettato']; ?>
                            <?php foreach ($states as $state): ?>
                                <option value="<?= $state; ?>" <?= $contract['status'] === $state ? 'selected' : ''; ?>><?= ucfirst(str_replace('_',' ', $state)); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if (hasRole('superadmin')): ?>
                        <div class="col-md-6">
                            <label class="form-label">Nome</label>
                            <input type="text" class="form-control" name="customer_name" value="<?= $contract['customer_name']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cognome</label>
                            <input type="text" class="form-control" name="customer_surname" value="<?= $contract['customer_surname']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CF</label>
                            <input type="text" class="form-control" name="customer_cf" value="<?= $contract['customer_cf']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="customer_email" value="<?= $contract['customer_email']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefono</label>
                            <input type="text" class="form-control" name="customer_phone" value="<?= $contract['customer_phone']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Indirizzo</label>
                            <input type="text" class="form-control" name="customer_address" value="<?= $contract['customer_address']; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tipo documento</label>
                            <input type="text" class="form-control" name="document_type" value="<?= $contract['document_type']; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Numero documento</label>
                            <input type="text" class="form-control" name="document_number" value="<?= $contract['document_number']; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Scadenza</label>
                            <input type="date" class="form-control" name="document_expiry" value="<?= $contract['document_expiry']; ?>" required>
                        </div>
                    <?php endif; ?>
                    <div class="col-12">
                        <label class="form-label">Note interne</label>
                        <textarea class="form-control" name="notes" rows="3"><?= $contract['notes']; ?></textarea>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">Aggiorna</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layout/footer.php'; ?>
