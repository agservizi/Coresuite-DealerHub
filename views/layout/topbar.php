<?php $notificationModel = new Notification(); $notifications = $notificationModel->getByUser($user['id'] ?? 0); $count = $notificationModel->getUnreadCount($user['id']); ?>
<header class="ghost-topbar">
    <button class="ghost-icon-button d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarGhost" aria-label="Apri menu">
        <i class="bi bi-list"></i>
    </button>
    <button class="ghost-icon-button d-none d-lg-grid" type="button" data-sidebar-toggle aria-label="Comprimi barra laterale" title="Comprimi barra laterale">
        <i class="bi bi-chevron-double-left"></i>
    </button>
    <form class="ghost-search d-none d-md-block" method="get" action="<?= url('contracts'); ?>">
        <i class="bi bi-search"></i>
        <input type="search" name="q" placeholder="Cerca contratti, clienti o ID...">
    </form>
    <div class="ghost-topbar-actions">
        <div class="dropdown ghost-dropdown">
            <button class="ghost-icon-button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell"></i>
                <?php if ($count > 0): ?>
                    <span class="ghost-badge-counter"><?= $count; ?></span>
                <?php endif; ?>
            </button>
            <div class="dropdown-menu dropdown-menu-end" style="min-width: 320px;">
                <div class="d-flex align-items-center justify-content-between mb-2 px-2">
                    <strong>Notifiche</strong>
                    <small class="text-muted"><?= $count; ?> nuove</small>
                </div>
                <div class="ghost-list" style="max-height: 280px; overflow-y: auto;">
                    <?php if (empty($notifications)): ?>
                        <div class="text-center text-muted py-3">Nessuna notifica</div>
                    <?php else: ?>
                        <?php foreach ($notifications as $note): ?>
                            <div class="d-flex flex-column py-2 px-2 border rounded-3 mb-1">
                                <small class="text-muted"><?= formatDateTime($note['created_at']); ?></small>
                                <span><?= $note['message']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="dropdown ghost-dropdown">
            <button class="ghost-user-chip" data-bs-toggle="dropdown">
                <span class="avatar"><?= strtoupper(substr($user['username'], 0, 1)); ?></span>
                <div>
                    <span class="d-block fw-semibold text-capitalize"><?= $user['username']; ?></span>
                    <small class="text-muted text-uppercase"><?= $user['role']; ?></small>
                </div>
                <i class="bi bi-chevron-down"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="<?= url('profile'); ?>"><i class="bi bi-person me-2"></i>Profilo</a>
                <a class="dropdown-item" href="<?= url('logout.php'); ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
            </div>
        </div>
    </div>
</header>
