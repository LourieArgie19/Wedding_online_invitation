# Wedding invitation

A Laravel 11 wedding invitation with a Filament admin panel.

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
