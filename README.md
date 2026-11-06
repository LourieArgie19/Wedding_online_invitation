# Wedding invitation

A Laravel 11 wedding invitation with a Filament admin panel.

## Deploy the free, read-only invitation to Render

[Deploy to Render](https://render.com/deploy?repo=https://github.com/LourieArgie19/Wedding_online_invitation)

Use the link above while signed in to Render, review the **Free** web service in **Singapore**, and deploy the Blueprint. The repository's `render.yaml` configures Docker and generates a separate production application key. No database service, paid disk, or copy of your local `.env` is needed.

The image builds Vite assets and installs production PHP dependencies. On startup it creates a local SQLite database, applies migrations, and seeds only Dy Ann and Dennis's invitation and gallery. The optimized photos and music are included in the image. Login, registration, admin pages, and write requests return 404 when `INVITATION_PUBLIC_ONLY=true`. Your local MySQL application and admin panel remain available with the default `false` setting.

Render may discard a free service's filesystem when it restarts. This setup recreates the published invitation from the seeder each time; make permanent content changes in the repository and redeploy. It does not copy local database records, admin accounts, or guest messages. Free services also sleep after 15 minutes of inactivity, so the first visitor after a quiet period may wait about a minute ([Render's free-service limits](https://render.com/docs/free)).

If you already opened Render's **New Web Service** form instead of using the Blueprint, use:

| Setting | Value |
| --- | --- |
| Repository / branch | `LourieArgie19/Wedding_online_invitation` / `main` |
| Language | Docker |
| Dockerfile Path | `./Dockerfile` |
| Root Directory / Docker Command | Leave blank |
| Region / instance | Singapore / Free |
| Health Check Path | `/` |
| Environment variable | `APP_KEY`: a new key from `php artisan key:generate --show` |

Do not import the local `.env`: its `127.0.0.1` MySQL address is your Mac, not an online database. The Docker image already supplies production mode, disabled debug output, Manila time, file sessions/cache, and the public-only SQLite configuration. The startup script obtains `APP_URL` and `ASSET_URL` from Render's assigned HTTPS URL. For a custom domain, set `APP_URL` to that HTTPS address. Do not reuse or commit your local application key.

To test the same image locally (requires Docker):

```bash
docker build -t wedding-invitation:render .
docker run --rm -p 8080:10000 \
  -e APP_KEY="$(php artisan key:generate --show)" \
  -e APP_URL=http://localhost:8080 \
  -e SESSION_SECURE_COOKIE=false \
  wedding-invitation:render
```

Open `http://localhost:8080`. The container uses its own database and does not connect to your local MySQL database.

## Local setup

Requires PHP 8.2+, Composer, Node.js 18+, and MySQL. PHP needs the MySQL and SQLite PDO extensions (SQLite is used for isolated tests).

1. Install dependencies and create `.env` if it does not already exist:

   ```bash
   composer install
   cp -n .env.example .env
   npm ci
   ```

2. Set your MySQL credentials in `.env`. The default database name is `wedding_invitation` and the local URL is `http://127.0.0.1:8000`. Create the database before migrating (adjust the MySQL user as needed):

   ```bash
   mysql -u root -p -e 'CREATE DATABASE IF NOT EXISTS wedding_invitation CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'
   ```

3. For a new installation, generate the application key and initialize the database:

   ```bash
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   npm run build
   ```

   Seed once on a new database. For an existing installation, use `php artisan migrate` to preserve its content. Keep the existing application key.

4. Start the application:

   ```bash
   php artisan serve --host=127.0.0.1 --port=8000
   ```

Open [the invitation](http://127.0.0.1:8000/?to=Wedding+Guest) or [the admin panel](http://127.0.0.1:8000/admin).

Seeded demo admin credentials:

- Email: `admin@codelogy.dev`
- Password: `123qweasd`

Replace the seeded demo administrator accounts before publishing.

For frontend development, run `npm run dev` in a separate terminal while the PHP server remains running. `npm run build` produces assets that work without the Vite dev server.

If you move or rename the project folder, run these commands from its new location to repair the public media link and clear cached view paths:

```bash
php artisan storage:unlink
ln -s ../storage/app/public public/storage
php artisan view:clear
```

The relative storage link keeps photos and music available after future folder renames. Restart any running development servers from the new location.

## Checks

```bash
php artisan test
npm run build
```

Tests use an in-memory SQLite database and do not reset your local MySQL data.

## Personalizing the invitation

The invitation for Dy Ann and Dennis opens from a floral envelope into one vertically scrolling page on desktop and mobile. A navigation bar stays at the top with links to each main section and a collapsible Menu on phones. Guests can swipe or scroll themselves, or use the play/pause control for a slow automatic scroll.

Clicking **Open Invitation** plays a single sequence: the wax seal turns clockwise for 2 seconds, the names fade for 2 seconds, and the envelope flap opens over 3 seconds to reveal the invitation. Repeated clicks cannot restart it, and reduced-motion preferences skip the animation. Opening also starts “For Life” by Moses Bliss from `storage/app/public/music/Moses Bliss - For Life [Official Video].mp3`. The song loops, with a music play/pause button available at the bottom left throughout the opening. Direct links to sections skip the opening animation, show the music button, and wait for a tap before playing.

On an existing installation, run `php artisan migrate` followed by `php artisan db:seed --class=DyAnnDennisSeeder`. The seeder creates the couple’s invitation once and deactivates the previous demo without deleting its records. On a fresh installation, the normal seed step includes this seeder.

In `/admin`, open **Weddings → Dy Ann & Dennis** to set the wedding date, welcome note, and portrait photos. The calendar and countdown appear only once a date is set, using the 3:30 PM ceremony time in `Asia/Manila`. The parents, officiant, entourage, venue, schedule, and motif are in `config/invitation.php`. The optimized photos in `storage/app/public/dy-ann-dennis/` were selected from `storage/app/public/all image/`; the original photos were not modified.

Add `?to=Guest+Name` to greet a guest. Open `/#entourage` or `/#celebration` to link directly to those sections. The public invitation is read-only, with a closing portrait, wedding details, and countdown. Wishes and RSVP forms are not displayed, and guest submissions are disabled. Manage existing records in **Guest → Rsvps** and **Guest → Wishes** in the admin panel.
