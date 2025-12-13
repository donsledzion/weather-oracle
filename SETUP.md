# Setup Instructions

## Local Development

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Configure Environment

Copy `.env.example` to `.env` and update:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=weather_oracle
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Generate application key:

```bash
php artisan key:generate
```

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Build Assets & Start Server

```bash
npm run dev
php artisan serve
```

Visit: http://localhost:8000

---

## Server Deployment (MyDevil)

### Initial Setup (One-time)

1. **Clone repository:**
   ```bash
   cd ~/domains/weather-oracle.softaware.pl
   git clone <repo-url> .
   ```

2. **Create symlink for public_html:**
   ```bash
   ln -s public public_html
   ```

3. **Set permissions:**
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

4. **Configure .env:**
   ```bash
   cp .env.example .env
   nano .env
   ```

   Update database credentials and generate key:
   ```bash
   php82 artisan key:generate
   ```

5. **Install dependencies:**
   ```bash
   php82 $(which composer) install --no-dev
   npm install
   npm run build
   ```

6. **Run migrations:**
   ```bash
   php82 artisan migrate --force
   ```

### Automatic Deployment

After initial setup, GitHub Actions automatically deploys on push to `main`:
- Pulls latest code
- Installs dependencies
- Builds assets
- Runs migrations
- Clears cache

**Required GitHub Secrets:**
- `SSH_PRIVATE_KEY`
- `SSH_HOST`
- `SSH_PORT`
- `SSH_USER`

---

## Tech Stack

- **Laravel 12** - Backend framework
- **Livewire 3** - Reactive components (no API needed!)
- **Alpine.js** - Lightweight JS for micro-interactions
- **Tailwind CSS** - Utility-first styling
- **MySQL** - Database

## Livewire Examples

The project includes a demo `MonitoringForm` component showing:
- Two-way data binding with `wire:model`
- Form validation
- Loading states with `wire:loading`
- Session flash messages

## Alpine.js Examples

Check `resources/views/home.blade.php` for a simple toggle example using Alpine.js.

---

## Development Notes

### Creating Livewire Components

```bash
php artisan make:livewire ComponentName
```

This creates:
- Class: `app/Livewire/ComponentName.php`
- View: `resources/views/livewire/component-name.blade.php`

### Using Components

```blade
@livewire('component-name')
```

or

```blade
<livewire:component-name />
```
