# Quick Start Guide - JobSeekers Frontend

## Project Setup (First Time)

```bash
# 1. Install PHP dependencies
composer install

# 2. Install Node dependencies (includes Alpine.js)
npm install

# 3. Create database and run migrations
php artisan migrate

# 4. Generate application key
php artisan key:generate
```

## Development (Daily Workflow)

**Terminal 1 - Start Laravel backend:**
```bash
php artisan serve
# Runs on http://localhost:8000
```

**Terminal 2 - Watch for frontend changes:**
```bash
npm run dev
# Automatically compiles CSS and JavaScript
# Rebuilds on any file change
```

**Browser:**
- Open http://localhost:8000
- Make changes to files in `resources/`
- Browser auto-refreshes with changes

## Key Directories

```
app/               → Backend PHP code (Controllers, Models)
resources/
  ├── views/       → Blade templates (HTML)
  ├── css/         → Tailwind CSS styles
  └── js/          → JavaScript (Alpine.js)
config/            → Configuration files
database/          → Migrations and seeders
public/            → Public assets (compiled CSS/JS)
routes/            → Application routes (web.php, api.php)
```

## Making Changes

### Add a New Page
1. Create new Blade file in `resources/views/` (e.g., `jobs/index.blade.php`)
2. Define route in `routes/web.php`
3. Extend layout: `@extends('layouts.app')`
4. Use existing components and utilities

### Add Interactive Feature
1. Use `x-data` in your Blade template to initialize Alpine
2. Use `@click`, `x-show`, `x-model` for interactivity
3. See `ALPINE_JS_GUIDE.md` for examples
4. No build step needed - just refresh browser

### Modify Styling
1. Edit `resources/css/app.css` or use Tailwind classes in templates
2. Use utility classes like `bg-blue-500`, `text-white`, `px-4`
3. For custom components, edit `tailwind.config.js`
4. Changes compile automatically

### Add JavaScript
1. Add to `resources/js/app.js`
2. Import any npm packages
3. Use Alpine.js instead of jQuery/vanilla JS
4. Changes compile automatically on save

## Important Files

| File | Purpose |
|------|---------|
| `routes/web.php` | All URL routes and handlers |
| `app/Models/User.php` | User model for database |
| `resources/views/layouts/app.blade.php` | Main layout template |
| `tailwind.config.js` | Tailwind CSS customization |
| `resources/js/app.js` | JavaScript entry point |
| `package.json` | Frontend dependencies |
| `composer.json` | Backend dependencies |

## Testing Features

### Test Authentication Flow
1. Go to http://localhost:8000/login
2. Use any email/password to register
3. Test password visibility toggle (eye icon)
4. Verify login/logout works

### Test Responsiveness
1. Open DevTools (F12)
2. Click mobile icon to enter responsive mode
3. Test at different widths: 320px, 768px, 1024px
4. Test navigation menu on mobile (hamburger icon)

### Test Alpine.js Features
1. Open DevTools Console (F12 → Console)
2. Watch for any errors (red messages)
3. Test password strength indicator on register page
4. Test dropdown menus on desktop
5. Test mobile menu toggle

## Building for Production

```bash
# Compile and minify all assets
npm run build

# This creates optimized files in public/build/
# Then deploy to server
```

## Common Commands

```bash
# Clear all caches
php artisan cache:clear

# Run database migrations
php artisan migrate

# Seed database with test data
php artisan db:seed

# Run tests
php artisan test

# Create new controller
php artisan make:controller JobController

# Create new model with migration
php artisan make:model Job -m

# Create new migration
php artisan make:migration create_jobs_table
```

## Tech Stack Reminder

- **Backend**: Laravel 11+, PHP 8.2+
- **Frontend**: Blade templates, Tailwind CSS v4, Alpine.js v3.13
- **Database**: MySQL 8.0+
- **Build**: Vite, Node.js
- **Deployment**: Any PHP 8.2+ hosting with MySQL

## Useful Resources

- Laravel Docs: https://laravel.com/docs
- Alpine.js Docs: https://alpinejs.dev
- Tailwind Docs: https://tailwindcss.com/docs
- Blade Guide: https://laravel.com/docs/blade
- Vite: https://vitejs.dev

## Troubleshooting

**Problem**: Changes not showing up
- Solution: Make sure `npm run dev` is running in Terminal 2
- Try hard refresh (Ctrl+Shift+R on Windows/Linux, Cmd+Shift+R on Mac)

**Problem**: `npm: command not found`
- Solution: Install Node.js from nodejs.org

**Problem**: Database errors
- Solution: Run `php artisan migrate:fresh` to reset database

**Problem**: Alpine.js not working
- Solution: Check DevTools Console for errors, verify `@vite` directive in layout

**Problem**: Tailwind classes not applying
- Solution: Verify class name is correct, check `tailwind.config.js`, rebuild with `npm run dev`

## Next Features to Build

1. **Jobs Listing Page** - Browse available jobs
2. **Job Detail Page** - View full job information
3. **Job Search** - Search and filter jobs
4. **User Profile** - User settings and resume upload
5. **Applications** - Track job applications
6. **Saved Jobs** - Bookmark favorite jobs
7. **Notifications** - Email/in-app notifications

## Getting Help

- Check error messages in browser DevTools (F12)
- Review `ALPINE_JS_GUIDE.md` for interactive component examples
- Check `FRONTEND_DESIGN_GUIDE.md` for design patterns
- Check `TECH_STACK.md` for architecture overview
- Read Laravel documentation for backend questions

---

**Version**: 1.0
**Last Updated**: August 18, 2026
**Status**: Ready to Use
