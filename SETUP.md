# Setup Instructions

## Quick Start

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Build Assets

```bash
npm run dev
```

### 3. Configure Database

Update `.env` file with your MySQL credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=weather_oracle
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. Start Development Server

```bash
php artisan serve
```

Visit: http://localhost:8000

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
