<?php include __DIR__ . '/../layout/header.php'; ?>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Statistiche per tipo</h5>
                <button class="btn btn-sm btn-outline-secondary">Esporta PDF</button>
            </div>
            <div class="card-body">
                <canvas id="typeChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header"><h5 class="mb-0">Statistiche per stato</h5></div>
            <div class="card-body">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
<script>
window.reportData = <?= json_encode($stats); ?>;
</script>
<?php include __DIR__ . '/../layout/footer.php'; ?>
