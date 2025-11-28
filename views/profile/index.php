<?php include __DIR__ . '/../layout/header.php'; ?>
<?php $user = getCurrentUser(); $isAdmin = $user['role'] === 'superadmin'; ?>
<section class="ghost-page-header">
    <div>
        <p class="text-muted mb-1 text-uppercase small">Profilo · <?= htmlspecialchars($user['role']); ?></p>
        <h1><?= htmlspecialchars($user['username']); ?></h1>
        <p class="text-muted mb-0">
            <?= $isAdmin ? 'Configura il centro di controllo e supervisiona la rete.' : 'Rivedi le tue performance e mantieni i dati aggiornati.'; ?>
        </p>
    </div>
    <div class="ghost-page-actions">
        <a href="<?= url('dashboard'); ?>" class="ghost-button secondary"><i class="bi bi-speedometer2"></i>Dashboard</a>
        <a href="<?= url('logout.php'); ?>" class="ghost-button"><i class="bi bi-box-arrow-right"></i>Logout</a>
    </div>
</section>

<?php if (!empty($highlightStats)): ?>
    <section class="ghost-grid kpi">
        <?php foreach ($highlightStats as $card): ?>
            <article class="ghost-kpi">
                <span><?= htmlspecialchars($card['label']); ?></span>
                <strong><?= htmlspecialchars((string) $card['value']); ?></strong>
                <small class="text-muted"><?= htmlspecialchars($card['hint']); ?></small>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<section class="ghost-grid" style="grid-template-columns: minmax(0, 3fr) minmax(0, 2fr); gap: 1.5rem;">
    <article class="ghost-card">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h5 class="mb-0">Dati account</h5>
            <span class="ghost-badge text-capitalize"><span class="dot"></span><?= htmlspecialchars($user['role']); ?></span>
        </div>
        <form class="ghost-form" method="post">
            <?= csrfTokenField(); ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-muted">Username</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']); ?>" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted">Ruolo</label>
                    <input type="text" class="form-control text-capitalize" value="<?= htmlspecialchars($user['role']); ?>" disabled>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Email di riferimento</label>
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Registrato il</label>
                    <input type="text" class="form-control" value="<?= !empty($user['created_at']) ? formatDateTime($user['created_at']) : 'N/D'; ?>" disabled>
                </div>
            </div>
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Nuova password</label>
                    <input type="password" class="form-control" name="password" placeholder="Lascia vuoto per non cambiare">
                </div>
                <div class="col-md-4 text-md-end">
                    <button class="ghost-button w-100 w-md-auto" type="submit"><i class="bi bi-save"></i>Salva modifiche</button>
                </div>
            </div>
        </form>
    </article>
    <article class="ghost-card">
        <?php if ($isAdmin): ?>
            <h5 class="mb-3">Panoramica team</h5>
            <div class="ghost-fieldset mb-3">
                <p class="text-muted mb-1">Affiliati attivi</p>
                <strong style="font-size:2rem;"><?= $teamStats['activeAffiliates'] ?? 0; ?></strong>
                <small class="text-muted">su <?= $teamStats['totalAffiliates'] ?? 0; ?> totali</small>
            </div>
            <div class="ghost-fieldset mb-3">
                <p class="text-muted mb-1">Affiliati sospesi</p>
                <strong><?= $teamStats['suspendedAffiliates'] ?? 0; ?></strong>
                <small class="text-muted">monitorare e riattivare</small>
            </div>
            <div class="ghost-fieldset">
                <p class="text-muted mb-1">Superadmin</p>
                <strong><?= $teamStats['superadmins'] ?? 0; ?></strong>
                <small class="text-muted">guardiani piattaforma</small>
            </div>
        <?php else: ?>
            <h5 class="mb-3">Progressi personali</h5>
            <p class="text-muted">Obiettivo mese: <?= $affiliateMetrics['goal'] ?? 12; ?> contratti caricati.</p>
            <div class="progress mb-2" role="progressbar" aria-valuenow="<?= $affiliateMetrics['progress'] ?? 0; ?>" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar bg-primary" style="width: <?= $affiliateMetrics['progress'] ?? 0; ?>%"></div>
            </div>
            <small class="text-muted d-block mb-3">Completati <?= $affiliateMetrics['last30'] ?? 0; ?> / <?= $affiliateMetrics['goal'] ?? 12; ?> negli ultimi 30 giorni.</small>
            <div class="ghost-fieldset mb-3">
                <p class="text-muted mb-1">Contratti attivati</p>
                <strong><?= $affiliateMetrics['activated'] ?? 0; ?></strong>
                <small class="text-muted">storico personale</small>
            </div>
            <div class="ghost-fieldset">
                <p class="text-muted mb-1">Supporto dedicato</p>
                <strong>help@dealerhub.it</strong>
                <small class="text-muted">o +39 02 1234 5678 (lun‑ven)</small>
            </div>
        <?php endif; ?>
    </article>
</section>

<section class="ghost-grid" style="grid-template-columns: minmax(0, 3fr) minmax(0, 2fr); margin-top: 1.5rem; gap: 1.5rem;">
    <article class="ghost-card">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h5 class="mb-0">Contratti recenti</h5>
            <a href="<?= url('contracts'); ?>" class="ghost-button secondary"><i class="bi bi-files"></i>Vai alla lista</a>
        </div>
        <?php if (!empty($recentContracts)): ?>
            <div class="ghost-table-wrapper">
                <table class="ghost-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Stato</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentContracts as $contract): ?>
                            <tr>
                                <td><?= htmlspecialchars(($contract['customer_name'] ?? '') . ' ' . ($contract['customer_surname'] ?? '')); ?></td>
                                <td class="text-capitalize"><?= htmlspecialchars($contract['type'] ?? ''); ?></td>
                                <td><?= isset($contract['status']) ? formatStatusBadge($contract['status']) : ''; ?></td>
                                <td><?= !empty($contract['created_at']) ? formatDateTime($contract['created_at']) : 'N/D'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted mb-0">Non ci sono contratti da mostrare al momento.</p>
        <?php endif; ?>
    </article>
    <article class="ghost-card">
        <h5 class="mb-3">Attività recenti</h5>
        <?php if (!empty($recentActivities)): ?>
            <ul class="timeline">
                <?php foreach ($recentActivities as $entry): ?>
                    <li>
                        <strong><?= htmlspecialchars($entry['action'] ?? ''); ?></strong>
                        <small class="d-block text-muted">
                            <?= !empty($entry['created_at']) ? formatDateTime($entry['created_at']) : 'N/D'; ?>
                            <?php if ($isAdmin && !empty($entry['username'])): ?>
                                · <?= htmlspecialchars($entry['username']); ?>
                            <?php endif; ?>
                        </small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted mb-0">Nessuna attività recente registrata.</p>
        <?php endif; ?>
    </article>
</section>

<section class="ghost-grid" style="grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); margin-top: 1.5rem; gap: 1.5rem;">
    <article class="ghost-card">
        <h5 class="mb-3">Notifiche personali</h5>
        <?php if (!empty($notifications)): ?>
            <div class="ghost-list">
                <?php foreach ($notifications as $notification): ?>
                    <div class="ghost-fieldset">
                        <p class="fw-semibold mb-1"><?= htmlspecialchars($notification['message']); ?></p>
                        <small class="text-muted"><?= !empty($notification['created_at']) ? formatDateTime($notification['created_at']) : 'N/D'; ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted mb-0">Nessuna notifica da mostrare.</p>
        <?php endif; ?>
    </article>
    <article class="ghost-card">
        <?php if ($isAdmin): ?>
            <h5 class="mb-3">Linee guida qualità</h5>
            <div class="ghost-list">
                <div class="ghost-fieldset">
                    <p class="fw-semibold mb-1">Checklist verifica documenti</p>
                    <small class="text-muted">Scarica la procedura aggiornata e condividila con il team.</small>
                </div>
                <div class="ghost-fieldset">
                    <p class="fw-semibold mb-1">Politiche sicurezza account</p>
                    <small class="text-muted">Richiedi MFA per gli affiliati critici e monitora gli accessi.</small>
                </div>
                <div class="ghost-fieldset">
                    <p class="fw-semibold mb-1">Calendario audit</p>
                    <small class="text-muted">Pianifica i controlli settimanali sulle pratiche in verifica.</small>
                </div>
            </div>
        <?php else: ?>
            <h5 class="mb-3">Risorse utili</h5>
            <div class="ghost-list">
                <div class="ghost-fieldset">
                    <p class="fw-semibold mb-1">Manuale onboarding</p>
                    <small class="text-muted">Linee guida aggiornate per la raccolta dei documenti cliente.</small>
                </div>
                <div class="ghost-fieldset">
                    <p class="fw-semibold mb-1">Calendario formazioni</p>
                    <small class="text-muted">Iscriviti al prossimo webinar commerciale dal portale Academy.</small>
                </div>
                <div class="ghost-fieldset">
                    <p class="fw-semibold mb-1">Assistenza prioritaria</p>
                    <small class="text-muted">Apri un ticket dedicato su help.dealerhub.it per richieste urgenti.</small>
                </div>
            </div>
        <?php endif; ?>
    </article>
</section>

<?php include __DIR__ . '/../layout/footer.php'; ?>
