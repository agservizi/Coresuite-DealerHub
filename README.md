# DealerHub

Portale professionale per la gestione dei contratti Telefonia / Luce / Gas con frontend Next.js (static export) e backend PHP puro pensato per hosting Hostinger shared.

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
npm run build   # output statico in frontend/out grazie a output: "export"
```

Variabili utili in `.env.local`:

```
NEXT_PUBLIC_API_BASE_URL=https://dealer.coresuite.it/api
```

## Deploy Hostinger

1. Eseguire `npm run build` (produce già `frontend/out/*`) e caricare la cartella risultante in `public_html/`.
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

## Database di riferimento

Vedi `backend/schema.sql` per creare le tabelle `users`, `user_tokens`, `contracts`.

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

## Deploy su Render

### Backend (Web Service)

1. Crea un nuovo **Web Service** su Render.
2. Seleziona **Connect GitHub** e collega il tuo repo.
3. Imposta **Root Directory**: `backend`
4. **Build Command**: lascia vuoto o `echo "No build needed"`
5. **Start Command**: `php -S 0.0.0.0:$PORT -t .`
6. Aggiungi le seguenti **Environment Variables**:
   - `DB_HOST`: il tuo host MySQL Hostinger (es. `sql123.hostinger.com`)
   - `DB_NAME`: nome database
   - `DB_USER`: username DB
   - `DB_PASS`: password DB
   - `APP_KEY`: chiave segreta per token (es. `your-secret-key-here`)
   - `UPLOAD_DIR`: `/opt/render/project/src/uploads/contratti` (o percorso Render per uploads)
7. Deploya il servizio. Nota l'URL generato (es. `https://dealerhub-backend.onrender.com`).

### Frontend (Static Site)

1. Crea un nuovo **Static Site** su Render.
2. Seleziona **Connect GitHub** e collega il tuo repo.
3. Imposta **Root Directory**: `frontend`
4. **Build Command**: `npm run build`
5. **Publish Directory**: `out`
6. Aggiungi **Environment Variable**:
   - `NEXT_PUBLIC_API_BASE_URL`: URL del backend Render (es. `https://dealerhub-backend.onrender.com/api`)
7. Deploya il sito. Nota l'URL generato (es. `https://dealerhub-frontend.onrender.com`).

### Database e seeding

1. Su Hostinger, importa `backend/schema.sql` per creare le tabelle.
2. Importa `populate_db.sql` per inserire il superadmin (email: `ag.servizi16@gmail.com`, password: `Giogiu2123@`).
3. Assicurati che il DB sia accessibile dal backend Render (whitelist IP se necessario).

### Configurazione dominio

- Su Hostinger, configura il dominio `dealer.coresuite.it` per puntare al frontend Render (es. CNAME a `dealerhub-frontend.onrender.com`).
- Per il backend, usa l'URL Render direttamente nelle chiamate API.

### Test finale

- Accedi al frontend, effettua login con le credenziali superadmin.
- Verifica che le API funzionino (dashboard, contratti, ecc.).
- Se necessario, aggiorna `.env` locale per testare con Render URLs.

Questo deploy è ottimizzato per Render con separazione frontend/backend e DB esterno su Hostinger.
