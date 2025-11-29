-- PostgreSQL schema for DealerHub

DO $$
BEGIN
    CREATE TYPE user_role AS ENUM ('SUPERADMIN', 'AFFILIATO');
EXCEPTION
    WHEN duplicate_object THEN NULL;
END$$;

DO $$
BEGIN
    CREATE TYPE service_type AS ENUM ('MOBILE', 'FIBRA', 'LUCE', 'GAS');
EXCEPTION
    WHEN duplicate_object THEN NULL;
END$$;

DO $$
BEGIN
    CREATE TYPE contract_status AS ENUM ('NUOVO','IN_ELABORAZIONE','INVIATO','ACCETTATO','RIFIUTATO');
EXCEPTION
    WHEN duplicate_object THEN NULL;
END$$;

CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role user_role DEFAULT 'AFFILIATO',
    affiliate_id INTEGER NULL,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS user_tokens (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    token_hash CHAR(64) NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS user_tokens_token_idx ON user_tokens (token_hash);

CREATE TABLE IF NOT EXISTS contracts (
    id SERIAL PRIMARY KEY,
    affiliate_id INTEGER NOT NULL REFERENCES users(id),
    customer_name VARCHAR(160) NOT NULL,
    customer_email VARCHAR(160),
    customer_phone VARCHAR(60),
    provider VARCHAR(80) NOT NULL,
    service_type service_type DEFAULT 'MOBILE',
    status contract_status DEFAULT 'NUOVO',
    notes TEXT,
    document_front VARCHAR(255),
    document_back VARCHAR(255),
    signed_form VARCHAR(255),
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE OR REPLACE FUNCTION trigger_set_timestamp()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS set_timestamp ON contracts;
CREATE TRIGGER set_timestamp
BEFORE UPDATE ON contracts
FOR EACH ROW
EXECUTE FUNCTION trigger_set_timestamp();
