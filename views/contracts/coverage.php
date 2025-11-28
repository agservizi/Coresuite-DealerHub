<?php include __DIR__ . '/../layout/header.php'; ?>
<div class="row g-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Copertura rete telefonia</h5>
                <span class="text-muted">Link aggiornati dai superadmin</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ($links as $name => $url): ?>
                        <div class="col-md-4">
                            <div class="coverage-tile">
                                <h6><?= $name; ?></h6>
                                <a href="<?= $url; ?>" target="_blank" class="btn btn-outline-primary btn-sm">Apri portale</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layout/footer.php'; ?>
