<?php $current = currentPath(); ?>
<aside class="ghost-sidebar collapse show" id="sidebarGhost">
    <div class="ghost-brand">
        <div class="ghost-logo-mark" role="img" aria-label="<?= APP_NAME; ?>">
            <img src="<?= asset('img/logosidebar.png'); ?>" alt="<?= APP_NAME; ?> Logo" loading="lazy">
        </div>
        <div class="ghost-brand-copy">
            <span><?= APP_NAME; ?></span>
            <small class="d-block text-muted">Contract Suite</small>
        </div>
    </div>
    <ul class="ghost-nav">
        <li>
            <a class="<?= $current === '/dashboard' ? 'active' : ''; ?>" href="<?= url('dashboard'); ?>" title="Vai alla dashboard">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a class="<?= str_starts_with($current, '/contracts') ? 'active' : ''; ?>" href="<?= url('contracts'); ?>" title="Apri i contratti">
                <i class="bi bi-files"></i>
                <span>Contratti</span>
            </a>
        </li>
        <li>
            <a class="<?= $current === '/contracts/create' ? 'active' : ''; ?>" href="<?= url('contracts/create'); ?>" title="Nuovo contratto">
                <i class="bi bi-plus-circle"></i>
                <span>Nuovo contratto</span>
            </a>
        </li>
        <?php if (hasAnyRole(['affiliato'])): ?>
            <li>
                <a class="<?= $current === '/coverage' ? 'active' : ''; ?>" href="<?= url('coverage'); ?>" title="Copertura rete">
                    <i class="bi bi-broadcast"></i>
                    <span>Copertura rete</span>
                </a>
            </li>
        <?php endif; ?>
        <?php if (hasRole('superadmin')): ?>
            <li>
                <a class="<?= str_starts_with($current, '/users') ? 'active' : ''; ?>" href="<?= url('users'); ?>" title="Gestisci affiliati">
                    <i class="bi bi-people"></i>
                    <span>Affiliati</span>
                </a>
            </li>
            <li>
                <a class="<?= str_starts_with($current, '/reports') ? 'active' : ''; ?>" href="<?= url('reports'); ?>" title="Statistiche">
                    <i class="bi bi-graph-up"></i>
                    <span>Statistiche</span>
                </a>
            </li>
        <?php endif; ?>
        <li>
            <a class="<?= $current === '/profile' ? 'active' : ''; ?>" href="<?= url('profile'); ?>" title="Profilo personale">
                <i class="bi bi-person-circle"></i>
                <span>Profilo</span>
            </a>
        </li>
        <li>
            <a href="<?= url('logout.php'); ?>" title="Esci dall'account">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
    <div class="ghost-sidebar-footer">
        <small>© <?= date('Y'); ?> <?= APP_NAME; ?><br>UI Ghost Edition</small>
    </div>
</aside>
