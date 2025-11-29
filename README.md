# DealerHub

Portale professionale per la gestione dei contratti Telefonia / Luce / Gas con frontend Next.js (static export) e backend PHP puro, ottimizzato sia per hosting condivisi (Hostinger) sia per Render (deploy separato frontend/postgres backend).

## Struttura

```
/frontend   → Next.js + Tailwind, build static `npm run build && npm run export`
/backend    → API PHP REST (auth, users, contracts, upload, coverage, stats)
/uploads    → Directory pre-creata per i documenti caricati
```

## Setup frontend

```bash
cd frontend
npm install
npm run dev     # sviluppo
npm run build && npm run export   # output statico in frontend/out
```

Variabili utili in `.env.local`:

```
NEXT_PUBLIC_API_BASE_URL=https://dealer.coresuite.it/api
```

## Configurazione variabili ambiente backend

Impostare un file `.env` (o le Environment Variables su Render) con:

```
DATABASE_URL=postgres://user:password@host:port/dbname   # oppure DB_HOST / DB_NAME ...
UPLOAD_BASE_PATH=/opt/render/project/src/uploads/contratti
APP_KEY=chiave-segreta
APP_URL=https://dealerhub-frontend.onrender.com
NEXT_PUBLIC_API_BASE_URL=https://dealerhub-backend.onrender.com/api
```

`db.php` supporta automaticamente `DATABASE_URL` (Postgres) oppure la coppia `DB_HOST`, `DB_NAME`, ecc. Rimane la compatibilità con MySQL impostando `DB_DRIVER=mysql`.

## Deploy Hostinger

1. Eseguire `npm run build && npm run export` e caricare `frontend/out/*` in `public_html/`.
2. Caricare la cartella `backend/api` dentro `public_html/api` insieme a `db.php` e `helpers.php`.
3. Creare cartella `public_html/uploads/contratti` con permessi 775.
4. Configurare `.env`/`php.ini` con le credenziali MySQL richieste da `backend/db.php`.
5. Assicurarsi che `.htaccess` permetta l’esecuzione dei file PHP e CORS.

## API principali

- `auth.php`: login/logout/recover con token persistiti in `user_tokens`.
- `me.php`: restituisce profilo dell’utente loggato.
- `users.php`: CRUD affiliate (solo superadmin).
- `contracts.php`: CRUD contratti + upload documenti multipli.
- `upload.php`: upload singolo file (max 10MB, PDF/JPG/PNG).
- `coverage.php`: placeholder per verifiche copertura (simulato, da integrare con API reali).
- `stats.php`: statistiche aggregate.

## Database di riferimento (Postgres)

`backend/schema.sql` ora utilizza tipi ENUM Postgres e serial auto-increment: eseguire con `psql "$DATABASE_URL" -f backend/schema.sql`. Il trigger `set_timestamp` aggiorna automaticamente `updated_at` ad ogni modifica dei contratti.

## Sicurezza e middleware

- Token bearer generati con `issue_token()` e salvati in `user_tokens`.
- Middleware `authenticate()` obbligatorio su tutte le API (eccetto login/recover).
- Ruoli previsti: `SUPERADMIN`, `AFFILIATO` con sidebar dinamica e permessi sulle pagine.

## Workflow consigliato

1. Popolare tabella `users` con almeno un superadmin.
2. Effettuare login dal frontend (`/login`), vengono richieste email/password → token JWT-like.
3. L’affiliato carica contratti da `/contracts/new`, visualizza i propri in `/contracts` e controlla copertura da `/coverage`.
4. Il superadmin vede tutte le statistiche in `/dashboard` e `/stats`, gestisce affiliati da `/affiliates` ed esporta contratti.

Il progetto è pronto per iterazioni successive (integrazione API copertura reali, firma digitale, ecc.).
