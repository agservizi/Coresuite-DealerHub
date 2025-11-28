<?php
$isEdit = isset($contract);
$pageTitle = $isEdit ? 'Modifica contratto' : 'Nuovo contratto';
?>
<?php include __DIR__ . '/../layout/header.php'; ?>
<section class="ghost-page-header">
    <div>
        <p class="text-muted mb-1 text-uppercase small">Onboarding · Contratti</p>
        <h1><?= $pageTitle; ?></h1>
        <p class="text-muted mb-0">Compila in modo guidato tutti i dati del cliente, scegli la tipologia di contratto e allega la documentazione obbligatoria.</p>
    </div>
    <div class="ghost-page-actions">
        <a href="/contracts" class="ghost-button secondary"><i class="bi bi-arrow-left"></i>Lista contratti</a>
    </div>
</section>
<form id="contractForm" class="ghost-form needs-validation" method="post" enctype="multipart/form-data" novalidate>
    <?= csrfTokenField(); ?>
    <input type="hidden" name="MAX_FILE_SIZE" value="10485760">
    <div id="formErrors" class="alert alert-danger d-none" role="alert">
        Sono presenti errori nei campi compilati. Verifica le sezioni evidenziate e riprova.
    </div>
    <section class="ghost-form-card">
        <header>
            <div>
                <h5 class="mb-0">1 · Dati cliente</h5>
                <small class="text-muted">Anagrafica completa, documento e contatti</small>
            </div>
        </header>
        <fieldset class="ghost-fieldset">
            <?php if (hasRole('superadmin')): ?>
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <label class="form-label">Assegna ad affiliato (opzionale)</label>
                        <select class="form-select" name="assigned_affiliate">
                            <option value="">Mantieni assegnazione a me</option>
                            <?php foreach (($affiliates ?? []) as $affiliate): ?>
                                <option value="<?= (int) $affiliate['id']; ?>">
                                    <?= htmlspecialchars($affiliate['username'] ?? $affiliate['email']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Il contratto comparirà nel pannello dell'affiliato selezionato.</small>
                    </div>
                </div>
            <?php endif; ?>
            <legend class="fw-semibold">Anagrafica</legend>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="customerName" name="customer_name" placeholder="Nome" required value="<?= htmlspecialchars($contract['customer_name'] ?? ''); ?>">
                        <label for="customerName">Nome *</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="customerSurname" name="customer_surname" placeholder="Cognome" required value="<?= htmlspecialchars($contract['customer_surname'] ?? ''); ?>">
                        <label for="customerSurname">Cognome *</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="cf" name="customer_cf" placeholder="Codice fiscale" pattern="^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$" maxlength="16" required value="<?= htmlspecialchars($contract['customer_cf'] ?? ''); ?>">
                        <label for="cf">Codice Fiscale *</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="date" class="form-control" id="birthDate" name="birth_date" placeholder="Data di nascita" required value="<?= htmlspecialchars($contract['birth_date'] ?? ''); ?>">
                        <label for="birthDate">Data di nascita *</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="birthPlace" name="birth_place" placeholder="Luogo di nascita" required value="<?= htmlspecialchars($contract['birth_place'] ?? ''); ?>">
                        <label for="birthPlace">Luogo di nascita *</label>
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-lg-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="street" name="address_street" placeholder="Via" required value="<?= htmlspecialchars($contract['address_street'] ?? ''); ?>">
                        <label for="street">Via *</label>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="streetNumber" name="address_number" placeholder="Civico" required value="<?= htmlspecialchars($contract['address_number'] ?? ''); ?>">
                        <label for="streetNumber">Civico *</label>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="city" name="address_city" placeholder="Città" required value="<?= htmlspecialchars($contract['address_city'] ?? ''); ?>">
                        <label for="city">Città *</label>
                    </div>
                </div>
                <div class="col-lg-1">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="zip" name="address_zip" placeholder="CAP" pattern="^[0-9]{5}$" maxlength="5" required value="<?= htmlspecialchars($contract['address_zip'] ?? ''); ?>">
                        <label for="zip">CAP *</label>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="province" name="address_province" placeholder="Provincia" maxlength="2" pattern="^[A-Z]{2}$" required value="<?= htmlspecialchars($contract['address_province'] ?? ''); ?>">
                        <label for="province">Provincia *</label>
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="customer_email" placeholder="Email" required value="<?= htmlspecialchars($contract['customer_email'] ?? ''); ?>">
                        <label for="email">Email *</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="tel" class="form-control" id="phone" name="customer_phone" placeholder="Telefono" pattern="^[0-9+\s]{7,15}$" required value="<?= htmlspecialchars($contract['customer_phone'] ?? ''); ?>">
                        <label for="phone">Telefono *</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <select class="form-select" id="documentType" name="document_type" required>
                            <?php $docType = $contract['document_type'] ?? ''; ?>
                            <option value="">Seleziona</option>
                            <option value="carta_identita" <?= $docType === 'carta_identita' ? 'selected' : ''; ?>>Carta d'identità</option>
                            <option value="patente" <?= $docType === 'patente' ? 'selected' : ''; ?>>Patente</option>
                            <option value="passaporto" <?= $docType === 'passaporto' ? 'selected' : ''; ?>>Passaporto</option>
                        </select>
                        <label for="documentType">Documento *</label>
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="documentNumber" name="document_number" placeholder="Numero documento" required value="<?= htmlspecialchars($contract['document_number'] ?? ''); ?>">
                        <label for="documentNumber">Numero documento *</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="date" class="form-control" id="documentRelease" name="document_issue_date" placeholder="Data rilascio" required value="<?= htmlspecialchars($contract['document_issue_date'] ?? ''); ?>">
                        <label for="documentRelease">Data rilascio *</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="date" class="form-control" id="documentExpiry" name="document_expiry_date" placeholder="Data scadenza" required value="<?= htmlspecialchars($contract['document_expiry_date'] ?? ''); ?>">
                        <label for="documentExpiry">Data scadenza *</label>
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label class="form-label">Documento fronte (PDF/JPG/PNG · max 10MB)</label>
                    <input class="form-control" type="file" accept="application/pdf,image/*" name="document_front" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Documento retro (PDF/JPG/PNG · max 10MB)</label>
                    <input class="form-control" type="file" accept="application/pdf,image/*" name="document_back" required>
                </div>
            </div>
        </fieldset>
    </section>

    <section class="ghost-form-card mt-4">
        <header>
            <div>
                <h5 class="mb-0">2 · Tipologia contratto</h5>
                <small class="text-muted">Seleziona la tipologia e compila la sezione dedicata</small>
            </div>
        </header>
        <fieldset class="ghost-fieldset">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label class="form-label">Tipo contratto *</label>
                    <?php $contractType = $contract['type'] ?? ''; ?>
                    <select class="form-select" id="contractType" name="type" required>
                        <option value="">Seleziona</option>
                        <option value="telefonia" <?= $contractType === 'telefonia' ? 'selected' : ''; ?>>Telefonia fissa</option>
                        <option value="luce" <?= $contractType === 'luce' ? 'selected' : ''; ?>>Luce</option>
                        <option value="gas" <?= $contractType === 'gas' ? 'selected' : ''; ?>>Gas</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <p class="text-muted mb-0">I blocchi sottostanti si attivano automaticamente in base alla tipologia selezionata.</p>
                </div>
            </div>
        </fieldset>
        <fieldset class="ghost-fieldset conditional-block" data-contract="telefonia">
            <legend class="fw-semibold">Telefonia fissa</legend>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tecnologia *</label>
                    <select class="form-select" name="tel_technology">
                        <option value="">Seleziona</option>
                        <option value="ftth">FTTH</option>
                        <option value="fttc">FTTC</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Operatore attuale</label>
                    <select class="form-select" name="tel_operator">
                        <option value="">Seleziona</option>
                        <option value="tim">TIM</option>
                        <option value="vodafone">Vodafone</option>
                        <option value="windtre">WindTre</option>
                        <option value="fastweb">Fastweb</option>
                        <option value="altro">Altro</option>
                    </select>
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="migrationCode" name="tel_migration_code" placeholder="Codice migrazione">
                        <label for="migrationCode">Codice migrazione</label>
                    </div>
                    <small class="text-muted">Obbligatorio se è prevista la portabilità.</small>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" name="tel_line_number" placeholder="Numero linea attuale">
                        <label>Numero linea attuale</label>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-floating">
                        <textarea class="form-control" name="tel_notes" placeholder="Note tecniche" style="height: 120px"></textarea>
                        <label>Note tecniche</label>
                    </div>
                </div>
            </div>
        </fieldset>
        <fieldset class="ghost-fieldset conditional-block" data-contract="luce">
            <legend class="fw-semibold">Luce</legend>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" name="energy_pod" placeholder="POD">
                        <label>POD *</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" name="energy_power" placeholder="Potenza impegnata">
                        <label>Potenza impegnata (kW)</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="number" class="form-control" name="energy_consumption" placeholder="Consumo annuo">
                        <label>Consumo annuo (kWh)</label>
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-4">
                    <label class="form-label">Tipologia cliente</label>
                    <select class="form-select" name="energy_customer_type">
                        <option value="privato">Privato</option>
                        <option value="business">Business</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Carica bolletta luce (PDF/JPG · max 10MB)</label>
                    <input class="form-control" type="file" name="energy_bill" accept="application/pdf,image/*">
                </div>
            </div>
        </fieldset>
        <fieldset class="ghost-fieldset conditional-block" data-contract="gas">
            <legend class="fw-semibold">Gas</legend>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" name="gas_pdr" placeholder="PDR">
                        <label>PDR *</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="number" class="form-control" name="gas_consumption" placeholder="Consumo annuo">
                        <label>Consumo annuo (Smc)</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipologia cliente</label>
                    <select class="form-select" name="gas_customer_type">
                        <option value="privato">Privato</option>
                        <option value="business">Business</option>
                    </select>
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-8">
                    <label class="form-label">Carica bolletta gas (PDF/JPG · max 10MB)</label>
                    <input class="form-control" type="file" name="gas_bill" accept="application/pdf,image/*">
                </div>
            </div>
        </fieldset>
    </section>

    <section class="ghost-form-card mt-4">
        <header>
            <div>
                <h5 class="mb-0">3 · Metodo di pagamento</h5>
                <small class="text-muted">Campi condizionali in base al metodo selezionato</small>
            </div>
        </header>
        <fieldset class="ghost-fieldset">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Metodo di pagamento *</label>
                    <select class="form-select" id="paymentMethod" name="payment_method" required>
                        <option value="">Seleziona</option>
                        <option value="rid">RID / Addebito Diretto</option>
                        <option value="carta">Carta</option>
                        <option value="bollettino">Bollettino Postale</option>
                    </select>
                </div>
            </div>
        </fieldset>
        <fieldset class="ghost-fieldset conditional-payment" data-payment="rid">
            <legend>RID / Addebito diretto</legend>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="iban" name="iban" placeholder="IBAN" pattern="^[A-Z]{2}[0-9A-Z]{13,30}$">
                        <label for="iban">IBAN *</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="ibanHolder" name="iban_holder" placeholder="Intestatario conto">
                        <label for="ibanHolder">Intestatario conto *</label>
                    </div>
                </div>
                <div class="col-12 form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="sddConsent" name="sdd_consent">
                    <label class="form-check-label" for="sddConsent">Autorizzo il mandato SDD *</label>
                </div>
            </div>
        </fieldset>
        <fieldset class="ghost-fieldset conditional-payment" data-payment="carta">
            <legend>Carta di pagamento</legend>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="cardNumber" name="card_number" inputmode="numeric" autocomplete="off" maxlength="19" placeholder="Numero carta">
                        <label for="cardNumber">Numero carta *</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="cardExpiry" name="card_expiry" placeholder="MM/YY" maxlength="5">
                        <label for="cardExpiry">Scadenza *</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="cardCvv" name="card_cvv" placeholder="CVV" maxlength="3">
                        <label for="cardCvv">CVV *</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="cardHolder" name="card_holder" placeholder="Intestatario carta">
                        <label for="cardHolder">Intestatario carta *</label>
                    </div>
                </div>
            </div>
        </fieldset>
        <fieldset class="ghost-fieldset conditional-payment" data-payment="bollettino">
            <legend>Bollettino postale</legend>
            <p class="mb-0 text-muted">Il pagamento avverrà tramite bollettino. Nessun dato di pagamento richiesto.</p>
        </fieldset>
    </section>

    <section class="ghost-form-card mt-4">
        <header>
            <div>
                <h5 class="mb-0">4 · Allegati contratto</h5>
                <small class="text-muted">Carica la documentazione a supporto</small>
            </div>
        </header>
        <fieldset class="ghost-fieldset">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Bolletta (solo luce/gas)</label>
                    <input class="form-control" type="file" name="generic_bill" accept="application/pdf,image/*">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Modulo di adesione (PDF)</label>
                    <input class="form-control" type="file" name="adhesion_form" accept="application/pdf">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Registrazione vocale (audio)</label>
                    <input class="form-control" type="file" name="voice_recording" accept="audio/*">
                </div>
                <div class="col-12">
                    <label class="form-label">Documentazione aggiuntiva (PDF)</label>
                    <input class="form-control" type="file" name="extra_documents[]" accept="application/pdf" multiple>
                </div>
            </div>
            <small class="text-muted d-block mt-2">Peso massimo 10MB per singolo file · I documenti vengono validati lato client e lato server.</small>
        </fieldset>
    </section>

    <?php if (hasRole('superadmin') || $isEdit): ?>
        <section class="ghost-form-card mt-4">
            <header>
                <div>
                    <h5 class="mb-0">5 · Stato del contratto</h5>
                    <small class="text-muted">Visibile solo ai profili amministrativi</small>
                </div>
            </header>
            <fieldset class="ghost-fieldset">
                <?php $status = $contract['status'] ?? 'in_attesa'; ?>
                <label class="form-label">Stato</label>
                <select class="form-select" name="status">
                    <option value="in_attesa" <?= $status === 'in_attesa' ? 'selected' : ''; ?>>In attesa</option>
                    <option value="in_lavorazione" <?= $status === 'in_lavorazione' ? 'selected' : ''; ?>>In lavorazione</option>
                    <option value="in_verifica" <?= $status === 'in_verifica' ? 'selected' : ''; ?>>In verifica operatore</option>
                    <option value="attivato" <?= $status === 'attivato' ? 'selected' : ''; ?>>Attivato</option>
                    <option value="annullato" <?= $status === 'annullato' ? 'selected' : ''; ?>>Annullato</option>
                    <option value="rigettato" <?= $status === 'rigettato' ? 'selected' : ''; ?>>Rigettato</option>
                </select>
            </fieldset>
        </section>
    <?php endif; ?>

    <section class="ghost-form-card mt-4">
        <fieldset class="ghost-fieldset">
            <div class="form-floating">
                <textarea class="form-control" name="notes" id="notes" style="height: 120px" placeholder="Note interne"><?= htmlspecialchars($contract['notes'] ?? ''); ?></textarea>
                <label for="notes">Note interne</label>
            </div>
        </fieldset>
        <div class="d-flex justify-content-end gap-2 mt-3">
            <button type="reset" class="ghost-button secondary"><i class="bi bi-arrow-counterclockwise"></i>Reset</button>
            <button type="submit" class="ghost-button" id="submitBtn">
                <span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner" role="status" aria-hidden="true"></span>
                <?= $isEdit ? 'Aggiorna contratto' : 'Salva contratto'; ?>
            </button>
        </div>
    </section>
</form>
<?php include __DIR__ . '/../layout/footer.php'; ?>
