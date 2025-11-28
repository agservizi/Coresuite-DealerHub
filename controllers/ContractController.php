<?php
// controllers/ContractController.php - contract management

declare(strict_types=1);

class ContractController
{
    private Contract $contractModel;
    private Notification $notificationModel;
    private ActivityLog $log;
    private User $userModel;

    private const MAX_FILE_SIZE = 10485760; // 10 MB
    private const DOCUMENT_MIME = ['application/pdf', 'image/jpeg', 'image/png'];
    private const AUDIO_MIME = ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg'];
    private const PDF_MIME = ['application/pdf'];

    public function __construct()
    {
        $this->contractModel = new Contract();
        $this->notificationModel = new Notification();
        $this->log = new ActivityLog();
        $this->userModel = new User();
    }

    public function index(): void
    {
        requireAuth();
        $user = getCurrentUser();
        $contracts = $this->contractModel->getAll($user['id'], $user['role']);
        include __DIR__ . '/../views/contracts/list.php';
    }

    public function create(): void
    {
        requireAuth();
        $user = getCurrentUser();
        $affiliates = [];
        if ($user['role'] === 'superadmin') {
            $affiliates = $this->userModel->getAffiliates();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('contracts', 'Token CSRF non valido', 'danger');
                redirect('/contracts/create');
            }

            [$isValid, $errors] = $this->validateContractRequest($_POST, $_FILES);
            if (!$isValid) {
                setFlash('contracts', implode('<br>', $errors), 'danger');
                redirect('/contracts/create');
            }

            try {
                $data = $this->buildContractPayload($user);
            } catch (RuntimeException $exception) {
                setFlash('contracts', $exception->getMessage(), 'danger');
                redirect('/contracts/create');
            }

            $contractId = $this->contractModel->create($data);

            $this->notificationModel->create([
                'user_id' => null,
                'message' => 'Nuovo contratto #' . $contractId . ' caricato da ' . $user['username'],
                'type' => 'contract_created'
            ]);
            if ($data['user_id'] !== $user['id']) {
                $this->notificationModel->create([
                    'user_id' => $data['user_id'],
                    'message' => sprintf('Il superadmin %s ha caricato per te il contratto %s.', $user['username'], $data['contract_code']),
                    'type' => 'contract_assigned'
                ]);
            }
            $this->log->log($user['id'], 'create_contract', json_encode(['contract_id' => $contractId]));

            setFlash('contracts', 'Contratto creato con successo. Codice: ' . $data['contract_code'], 'success');
            redirect('/contracts');
        }

        include __DIR__ . '/../views/forms/contract_form.php';
    }

    public function edit(int $id): void
    {
        requireAuth();
        $user = getCurrentUser();
        $contract = $this->contractModel->getById($id, $user['id'], $user['role']);
        if (!$contract) {
            setFlash('contracts', 'Contratto non trovato', 'danger');
            redirect('/contracts');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                setFlash('contracts', 'Token CSRF non valido', 'danger');
                redirect('/contracts/edit/' . $id);
            }

            $payload = ['status' => sanitize($_POST['status'] ?? 'in_attesa')];
            if ($user['role'] === 'superadmin') {
                $payload = array_merge($payload, $this->collectEditableFields());
            }

            $this->contractModel->update($id, $payload);
            $this->log->log($user['id'], 'update_contract', json_encode(['contract_id' => $id]));
            setFlash('contracts', 'Contratto aggiornato', 'success');
            redirect('/contracts');
        }

        include __DIR__ . '/../views/forms/contract_edit.php';
    }

    public function delete(int $id): void
    {
        requireAuth();
        $user = getCurrentUser();
        if ($user['role'] !== 'superadmin') {
            redirect('/contracts');
        }
        $this->contractModel->delete($id);
        $this->log->log($user['id'], 'delete_contract', json_encode(['contract_id' => $id]));
        setFlash('contracts', 'Contratto eliminato', 'success');
        redirect('/contracts');
    }

    public function export(): void
    {
        requireAuth();
        if (!hasRole('superadmin')) {
            redirect('/dashboard');
        }
        $contracts = $this->contractModel->getAll();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="contracts.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Tipo', 'Cliente', 'Stato', 'Data']);
        foreach ($contracts as $contract) {
            fputcsv($out, [
                $contract['id'],
                $contract['type'],
                $contract['customer_name'] . ' ' . $contract['customer_surname'],
                $contract['status'],
                $contract['created_at']
            ]);
        }
        fclose($out);
        exit;
    }

    private function buildContractPayload(array $currentUser): array
    {
        $input = $_POST;
        $uploads = $this->handleUploads();
        $formattedAddress = $this->formatAddress($input);
        $ownerId = $this->resolveContractOwner($currentUser, $input);

        $data = [
            'user_id' => $ownerId,
            'contract_code' => $this->generateContractCode(),
            'type' => sanitize($input['type'] ?? 'telefonia'),
            'subtype' => null,
            'customer_name' => sanitize($input['customer_name'] ?? ''),
            'customer_surname' => sanitize($input['customer_surname'] ?? ''),
            'customer_cf' => strtoupper(sanitize($input['customer_cf'] ?? '')),
            'customer_email' => sanitize($input['customer_email'] ?? ''),
            'customer_phone' => sanitize($input['customer_phone'] ?? ''),
            'customer_address' => $formattedAddress,
            'birth_date' => sanitize($input['birth_date'] ?? null),
            'birth_place' => sanitize($input['birth_place'] ?? ''),
            'address_street' => sanitize($input['address_street'] ?? ''),
            'address_number' => sanitize($input['address_number'] ?? ''),
            'address_city' => sanitize($input['address_city'] ?? ''),
            'address_zip' => sanitize($input['address_zip'] ?? ''),
            'address_province' => strtoupper(sanitize($input['address_province'] ?? '')),
            'document_type' => sanitize($input['document_type'] ?? ''),
            'document_number' => sanitize($input['document_number'] ?? ''),
            'document_issue_date' => sanitize($input['document_issue_date'] ?? ''),
            'document_expiry' => sanitize($input['document_expiry_date'] ?? ''),
            'status' => sanitize($input['status'] ?? 'in_attesa'),
            'payment_method' => sanitize($input['payment_method'] ?? ''),
            'payment_details' => json_encode($this->buildPaymentDetails($input)) ?: null,
            'technical_details' => json_encode($this->buildTechnicalDetails($input)) ?: null,
            'notes' => sanitize($input['notes'] ?? '')
        ];

        $data = array_merge($data, $uploads['files']);
        $data['attachments'] = $uploads['attachments'] ? json_encode($uploads['attachments']) : null;

        return $data;
    }

    private function resolveContractOwner(array $currentUser, array $input): int
    {
        if (($currentUser['role'] ?? '') !== 'superadmin') {
            return (int) $currentUser['id'];
        }

        $requestedId = isset($input['assigned_affiliate']) ? (int) $input['assigned_affiliate'] : 0;
        if ($requestedId <= 0) {
            return (int) $currentUser['id'];
        }

        $affiliate = $this->userModel->findById($requestedId);
        if (!$affiliate || ($affiliate['role'] ?? '') !== 'affiliato') {
            throw new RuntimeException('L\'affiliato selezionato non è valido.');
        }
        if (($affiliate['status'] ?? '') !== 'active') {
            throw new RuntimeException('L\'affiliato selezionato non è attivo.');
        }

        return (int) $affiliate['id'];
    }

    private function generateContractCode(int $length = 8): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $maxIndex = strlen($characters) - 1;

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, $maxIndex)];
            }

            if (!$this->contractModel->codeExists($code)) {
                return $code;
            }
        }

        throw new RuntimeException('Impossibile generare un codice contratto univoco. Riprova.');
    }

    private function buildTechnicalDetails(array $input): array
    {
        $type = $input['type'] ?? '';
        if ($type === 'telefonia') {
            return array_filter([
                'technology' => sanitize($input['tel_technology'] ?? ''),
                'operator' => sanitize($input['tel_operator'] ?? ''),
                'migration_code' => sanitize($input['tel_migration_code'] ?? ''),
                'line_number' => sanitize($input['tel_line_number'] ?? ''),
                'notes' => sanitize($input['tel_notes'] ?? '')
            ]);
        }

        if ($type === 'luce') {
            return array_filter([
                'pod' => sanitize($input['energy_pod'] ?? ''),
                'power' => sanitize($input['energy_power'] ?? ''),
                'consumption' => sanitize($input['energy_consumption'] ?? ''),
                'customer_type' => sanitize($input['energy_customer_type'] ?? 'privato')
            ]);
        }

        if ($type === 'gas') {
            return array_filter([
                'pdr' => sanitize($input['gas_pdr'] ?? ''),
                'consumption' => sanitize($input['gas_consumption'] ?? ''),
                'customer_type' => sanitize($input['gas_customer_type'] ?? 'privato')
            ]);
        }

        return [];
    }

    private function buildPaymentDetails(array $input): array
    {
        $method = $input['payment_method'] ?? '';
        switch ($method) {
            case 'rid':
                return [
                    'iban' => strtoupper(str_replace(' ', '', sanitize($input['iban'] ?? ''))),
                    'iban_holder' => sanitize($input['iban_holder'] ?? ''),
                    'sdd_consent' => !empty($input['sdd_consent'])
                ];
            case 'carta':
                return [
                    'card_number' => preg_replace('/\s+/', '', sanitize($input['card_number'] ?? '')),
                    'card_expiry' => sanitize($input['card_expiry'] ?? ''),
                    'card_cvv' => sanitize($input['card_cvv'] ?? ''),
                    'card_holder' => sanitize($input['card_holder'] ?? '')
                ];
            default:
                return [];
        }
    }

    private function validateContractRequest(array $input, array $files): array
    {
        $errors = [];
        if (empty(trim($input['customer_name'] ?? ''))) {
            $errors[] = 'Il nome del cliente è obbligatorio.';
        }
        if (empty(trim($input['customer_surname'] ?? ''))) {
            $errors[] = 'Il cognome del cliente è obbligatorio.';
        }
        $cf = strtoupper(trim($input['customer_cf'] ?? ''));
        if (!preg_match('/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/', $cf)) {
            $errors[] = 'Codice fiscale non valido.';
        }
        if (!filter_var($input['customer_email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email non valida.';
        }
        if (!preg_match('/^[0-9+\s]{7,15}$/', $input['customer_phone'] ?? '')) {
            $errors[] = 'Numero di telefono non valido.';
        }
        if (empty($input['type'])) {
            $errors[] = 'Seleziona una tipologia di contratto.';
        }
        if (empty($input['payment_method'])) {
            $errors[] = 'Seleziona un metodo di pagamento.';
        }

        if (($input['payment_method'] ?? '') === 'rid' && !preg_match('/^[A-Z]{2}[0-9A-Z]{13,30}$/', strtoupper(str_replace(' ', '', $input['iban'] ?? '')))) {
            $errors[] = 'IBAN non valido.';
        }
        if (($input['payment_method'] ?? '') === 'carta' && !preg_match('/^[0-9]{13,19}$/', preg_replace('/\s+/', '', $input['card_number'] ?? ''))) {
            $errors[] = 'Numero carta non valido.';
        }
        if (($input['payment_method'] ?? '') === 'carta' && !preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $input['card_expiry'] ?? '')) {
            $errors[] = 'La scadenza della carta deve essere nel formato MM/YY.';
        }
        if (($input['payment_method'] ?? '') === 'carta' && !preg_match('/^[0-9]{3}$/', $input['card_cvv'] ?? '')) {
            $errors[] = 'Il CVV deve contenere 3 cifre.';
        }

        if (($input['type'] ?? '') === 'telefonia' && !empty($input['tel_line_number']) && empty($input['tel_migration_code'])) {
            $errors[] = 'Per la portabilità è richiesto il codice di migrazione.';
        }
        if (($input['type'] ?? '') === 'luce' && empty($input['energy_pod'])) {
            $errors[] = 'Il POD è obbligatorio per la luce.';
        }
        if (($input['type'] ?? '') === 'gas' && empty($input['gas_pdr'])) {
            $errors[] = 'Il PDR è obbligatorio per il gas.';
        }

        $issue = $input['document_issue_date'] ?? '';
        $expiry = $input['document_expiry_date'] ?? '';
        if ($issue && $expiry && strtotime($issue) > strtotime($expiry)) {
            $errors[] = 'La data di rilascio del documento deve precedere la scadenza.';
        }
        if ($expiry && strtotime($expiry) < strtotime(date('Y-m-d'))) {
            $errors[] = 'La data di scadenza deve essere futura.';
        }

        $this->validateFileField($files, 'document_front', self::DOCUMENT_MIME, $errors, true);
        $this->validateFileField($files, 'document_back', self::DOCUMENT_MIME, $errors, true);
        $this->validateFileField($files, 'generic_bill', self::DOCUMENT_MIME, $errors, false);
        $this->validateFileField($files, 'energy_bill', self::DOCUMENT_MIME, $errors, false);
        $this->validateFileField($files, 'gas_bill', self::DOCUMENT_MIME, $errors, false);
        $this->validateFileField($files, 'adhesion_form', self::PDF_MIME, $errors, false);
        $this->validateFileField($files, 'voice_recording', self::AUDIO_MIME, $errors, false);
        $this->validateFileField($files, 'extra_documents', self::PDF_MIME, $errors, false, true);

        return [empty($errors), $errors];
    }

    private function validateFileField(array $files, string $field, array $allowedMime, array &$errors, bool $required = false, bool $multiple = false): void
    {
        if (!isset($files[$field])) {
            if ($required) {
                $errors[] = sprintf('Il file %s è obbligatorio.', str_replace('_', ' ', $field));
            }
            return;
        }

        $entries = $multiple ? $this->normalizeFiles($files[$field]) : [$files[$field]];
        foreach ($entries as $entry) {
            if ($entry['error'] === UPLOAD_ERR_NO_FILE) {
                if ($required) {
                    $errors[] = sprintf('Il file %s è obbligatorio.', str_replace('_', ' ', $field));
                }
                continue;
            }
            if ($entry['error'] !== UPLOAD_ERR_OK) {
                $errors[] = sprintf('Errore durante il caricamento di %s.', str_replace('_', ' ', $field));
                continue;
            }
            if ($entry['size'] > self::MAX_FILE_SIZE) {
                $errors[] = sprintf('Il file %s supera i 10MB consentiti.', str_replace('_', ' ', $field));
            }
            $mime = $this->detectMime($entry['tmp_name']);
            if ($mime && !in_array($mime, $allowedMime, true)) {
                $errors[] = sprintf('Il file %s ha un formato non consentito.', str_replace('_', ' ', $field));
            }
        }
    }

    private function handleUploads(): array
    {
        $files = [];
        $attachments = [];

        foreach (['document_front', 'document_back'] as $field) {
            $path = $this->processUpload($field, self::DOCUMENT_MIME);
            if ($path) {
                $files[$field] = $path;
            }
        }

        $genericBill = $this->processUpload('generic_bill', self::DOCUMENT_MIME);
        if ($genericBill) {
            $files['bill'] = $genericBill;
            $attachments['generic_bill'] = $genericBill;
        }

        foreach (['energy_bill', 'gas_bill'] as $billField) {
            $path = $this->processUpload($billField, self::DOCUMENT_MIME);
            if ($path) {
                $attachments[$billField] = $path;
            }
        }

        foreach ([
            'adhesion_form' => self::PDF_MIME,
            'voice_recording' => self::AUDIO_MIME
        ] as $field => $mimeList) {
            $path = $this->processUpload($field, $mimeList);
            if ($path) {
                $files[$field] = $path;
            }
        }

        $extraDocuments = $this->processMultipleUpload('extra_documents', self::PDF_MIME);
        if ($extraDocuments) {
            $attachments['extra_documents'] = $extraDocuments;
        }

        return ['files' => $files, 'attachments' => $attachments];
    }

    private function processUpload(string $field, array $allowedMime): ?string
    {
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        $file = $_FILES[$field];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        $this->assertFileValid($file, $allowedMime);
        return $this->storeFile($file);
    }

    private function processMultipleUpload(string $field, array $allowedMime): array
    {
        if (!isset($_FILES[$field])) {
            return [];
        }
        $paths = [];
        foreach ($this->normalizeFiles($_FILES[$field]) as $file) {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                continue;
            }
            $this->assertFileValid($file, $allowedMime);
            $paths[] = $this->storeFile($file);
        }
        return $paths;
    }

    private function normalizeFiles(array $file): array
    {
        if (!is_array($file['name'])) {
            return [$file];
        }
        $normalized = [];
        foreach ($file['name'] as $index => $name) {
            $normalized[] = [
                'name' => $name,
                'type' => $file['type'][$index],
                'tmp_name' => $file['tmp_name'][$index],
                'error' => $file['error'][$index],
                'size' => $file['size'][$index]
            ];
        }
        return $normalized;
    }

    private function assertFileValid(array $file, array $allowedMime): void
    {
        if ($file['size'] > self::MAX_FILE_SIZE) {
            throw new RuntimeException('Uno dei file supera i 10MB consentiti.');
        }
        $mime = $this->detectMime($file['tmp_name']);
        if ($mime && !in_array($mime, $allowedMime, true)) {
            throw new RuntimeException('Uno dei file caricati non ha un formato consentito.');
        }
    }

    private function detectMime(string $path): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? (finfo_file($finfo, $path) ?: '') : '';
        if ($finfo) {
            finfo_close($finfo);
        }
        return $mime;
    }

    private function formatAddress(array $input): string
    {
        $street = sanitize($input['address_street'] ?? '');
        $number = sanitize($input['address_number'] ?? '');
        $city = sanitize($input['address_city'] ?? '');
        $zip = sanitize($input['address_zip'] ?? '');
        $province = strtoupper(sanitize($input['address_province'] ?? ''));
        return trim(sprintf('%s %s, %s (%s) %s', $street, $number, $city, $province, $zip));
    }

    private function storeFile(array $file): string
    {
        $uploadDir = __DIR__ . '/../storage/uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid('doc_', true) . '.' . $ext;
        $destination = $uploadDir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new RuntimeException('Impossibile salvare il file caricato.');
        }
        return '/storage/uploads/' . $filename;
    }

    private function collectEditableFields(): array
    {
        $data = [
            'customer_name' => sanitize($_POST['customer_name'] ?? ''),
            'customer_surname' => sanitize($_POST['customer_surname'] ?? ''),
            'customer_cf' => sanitize($_POST['customer_cf'] ?? ''),
            'customer_email' => sanitize($_POST['customer_email'] ?? ''),
            'customer_phone' => sanitize($_POST['customer_phone'] ?? ''),
            'customer_address' => sanitize($_POST['customer_address'] ?? ''),
            'document_type' => sanitize($_POST['document_type'] ?? ''),
            'document_number' => sanitize($_POST['document_number'] ?? ''),
            'document_expiry' => sanitize($_POST['document_expiry'] ?? ''),
            'notes' => sanitize($_POST['notes'] ?? '')
        ];

        if (isset($_POST['birth_date'])) {
            $data['birth_date'] = sanitize($_POST['birth_date']);
        }
        if (isset($_POST['birth_place'])) {
            $data['birth_place'] = sanitize($_POST['birth_place']);
        }
        if (isset($_POST['address_street'])) {
            $data['address_street'] = sanitize($_POST['address_street']);
            $data['address_number'] = sanitize($_POST['address_number'] ?? '');
            $data['address_city'] = sanitize($_POST['address_city'] ?? '');
            $data['address_zip'] = sanitize($_POST['address_zip'] ?? '');
            $data['address_province'] = strtoupper(sanitize($_POST['address_province'] ?? ''));
            $data['customer_address'] = $this->formatAddress($_POST);
        }
        if (isset($_POST['document_issue_date'])) {
            $data['document_issue_date'] = sanitize($_POST['document_issue_date']);
        }

        return $data;
    }
}

?>
