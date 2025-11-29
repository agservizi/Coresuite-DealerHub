-- Popolamento database DealerHub
-- Esegui questo script su MySQL per creare l'utente superadmin

-- Assicurati che le tabelle esistano (vedi backend/schema.sql)

-- Inserisci superadmin (se non esiste già)
INSERT INTO users (name, email, password_hash, role, active, created_at)
VALUES (
    'Superadmin Coresuite',
    'ag.servizi16@gmail.com',
    '$2y$12$xnu6Uuf8pJrCcP7b93csdeVCSvuwpuqDVvVoloaxj6a7qnPwWxBjO',  -- Hash di 'Giogiu2123@'
    'SUPERADMIN',
    1,
    NOW()
)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    password_hash = VALUES(password_hash),
    role = VALUES(role),
    active = VALUES(active);

-- Nota: Sostituisci '$2y$10$example.hash.here' con l'hash reale di 'Giogiu2123@'
-- Puoi generarlo con: php -r "echo password_hash('Giogiu2123@', PASSWORD_BCRYPT);"