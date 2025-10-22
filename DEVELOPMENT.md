# Bonaire - Development Guide

## Overview

Bonaire has been refactored to follow modern WordPress plugin development best practices with PSR-4 autoloading, dependency injection, and a modular architecture.

## Prerequisites

- PHP 7.4 or higher
- Node.js 14+ and npm
- Composer
- WordPress 5.5+
- Contact Form 7 plugin
- Flamingo plugin

## Initial Setup

### 1. Clone the Repository

```bash
git clone https://github.com/demispatti/bonaire.git
cd bonaire
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install JavaScript Dependencies

```bash
npm install
```

### 4. Build Assets

For development (with watch mode):
```bash
npm run dev
```

For production (minified):
```bash
npm run build
```

## Project Structure

```
bonaire/
├── assets/
│   ├── src/                    # Source files
│   │   ├── js/                 # JavaScript (ES6+ modules)
│   │   │   ├── admin/          # Admin JS
│   │   │   ├── utils/          # Utility functions
│   │   │   └── services/       # Service classes
│   │   └── scss/               # SCSS files
│   │       ├── config/         # Variables, mixins
│   │       ├── base/           # Base styles
│   │       ├── components/     # UI components
│   │       ├── layout/         # Page layouts
│   │       └── utilities/      # Helper classes
│   └── dist/                   # Built assets (gitignored)
│       ├── js/                 # Compiled JavaScript
│       └── css/                # Compiled CSS
├── languages/                  # Translation files
├── src/                        # PHP source code (PSR-4)
│   ├── Core/                   # Core plugin functionality
│   │   ├── Container.php       # DI Container
│   │   ├── Plugin.php          # Main plugin class
│   │   └── ServiceProvider.php # Base service provider
│   ├── Admin/                  # Admin functionality
│   │   ├── Controllers/        # Admin controllers
│   │   ├── Services/           # Admin services
│   │   └── Views/              # View classes
│   ├── Frontend/               # Frontend functionality
│   │   └── Controllers/        # Frontend controllers
│   └── Infrastructure/         # WordPress integration
│       ├── Services/           # Infrastructure services
│       └── ServiceProviders/   # Service provider classes
├── templates/                  # View templates
│   ├── admin/                  # Admin templates
│   └── frontend/               # Frontend templates
├── tests/                      # PHPUnit tests
│   ├── Unit/                   # Unit tests
│   └── Integration/            # Integration tests
├── vendor/                     # Composer dependencies (gitignored)
├── node_modules/               # npm dependencies (gitignored)
├── bonaire-refactored.php      # Main plugin file (new)
├── bonaire.php                 # Legacy plugin file (keep for compatibility)
├── composer.json               # PHP dependencies
├── package.json                # JavaScript dependencies
├── webpack.config.js           # Webpack configuration
├── phpcs.xml                   # PHP CodeSniffer rules
├── .eslintrc.json              # ESLint configuration
├── .editorconfig               # Editor configuration
└── .gitignore                  # Git ignore rules
```

## Architecture

### Dependency Injection

The plugin uses a custom PSR-11 compliant container for dependency injection:

```php
// In a service provider
$container->singleton(
    OptionsService::class,
    fn( Container $c ) => new OptionsService( $c->get( 'plugin.domain' ) )
);

// Retrieve from container
$options = $container->get( OptionsService::class );
```

### Service Providers

Service providers register services with the container:

```php
class AdminServiceProvider extends ServiceProvider {
    public function register( Container $container ): void {
        // Register services
    }

    public function boot( Container $container ): void {
        // Bootstrap services
    }
}
```

### Controllers

Controllers handle WordPress hooks and coordinate between services and views:

```php
class AdminController {
    public function register_hooks(): void {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    public function enqueue_assets( string $hook_suffix ): void {
        $this->asset_service->enqueue_admin_styles( $hook_suffix );
    }
}
```

### Services

Services encapsulate business logic:

```php
class OptionsService {
    public function get( int $group = 0 ) {
        // Get options from database
    }

    public function save( array $data, int $group = 0 ): bool {
        // Save options to database
    }
}
```

## Development Workflow

### JavaScript Development

1. **Start watch mode:**
   ```bash
   npm run dev
   ```

2. **Write ES6+ modules** in `assets/src/js/`

3. **Webpack automatically bundles** into `assets/dist/js/`

### SCSS Development

1. **Edit SCSS files** in `assets/src/scss/`

2. **Webpack compiles** to `assets/dist/css/`

3. **Use variables and mixins** from `config/` folder

### PHP Development

1. **Follow PSR-4 autoloading:**
   - Namespace: `Bonaire\`
   - Directory: `src/`

2. **Use dependency injection:**
   ```php
   public function __construct( OptionsService $options_service ) {
       $this->options_service = $options_service;
   }
   ```

3. **Register services** in service providers

4. **Follow WordPress Coding Standards:**
   ```bash
   composer run cs    # Check coding standards
   composer run cbf   # Fix coding standards
   ```

## Testing

### Run PHPUnit Tests

```bash
composer test
```

### Run JavaScript Linting

```bash
npm run lint:js
```

### Run CSS Linting

```bash
npm run lint:css
```

## Coding Standards

### PHP

- Follow WordPress PHP Coding Standards
- Use type hints
- Document with PHPDoc
- Use dependency injection

### JavaScript

- ES6+ modules
- Use JSDoc comments
- Follow ESLint rules
- Avoid jQuery where possible (except WordPress admin)

### CSS/SCSS

- Use BEM naming convention for custom classes
- Prefix custom classes with `bonaire-`
- Use variables for colors, spacing, etc.
- Mobile-first responsive design

## Debugging

### Enable Debug Mode

In `wp-config.php`:
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'SCRIPT_DEBUG', true );
```

### View Logs

```bash
tail -f wp-content/debug.log
```

### Browser DevTools

- JavaScript source maps available in dev mode
- Use Vue DevTools for component inspection (if using Vue)

## Common Tasks

### Adding a New Admin Page

1. Create controller in `src/Admin/Controllers/`
2. Create view in `src/Admin/Views/`
3. Register in `AdminServiceProvider`
4. Add route in controller

### Adding AJAX Endpoint

1. Add method in `AjaxController`
2. Register hook in `register_hooks()`
3. Add corresponding JavaScript in `assets/src/js/admin/AjaxHandler.js`

### Adding New Styles

1. Create SCSS partial in appropriate folder
2. Import in `assets/src/scss/admin.scss`
3. Rebuild with `npm run build`

### Adding Translation

1. Use `__()`, `_e()`, `_n()` functions
2. Update POT file:
   ```bash
   wp i18n make-pot . languages/bonaire.pot
   ```

## Deployment

### Build for Production

```bash
# Install dependencies
composer install --no-dev --optimize-autoloader
npm install --production

# Build assets
npm run build

# Create zip
zip -r bonaire.zip . -x "node_modules/*" "assets/src/*" "tests/*" "*.git/*"
```

### Version Update Checklist

1. Update version in `bonaire-refactored.php`
2. Update version in `package.json`
3. Update version in `composer.json`
4. Update `CHANGELOG.md`
5. Update `README.txt` (WordPress.org)
6. Build production assets
7. Create git tag
8. Deploy to WordPress.org

## Resources

- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [PSR-11 Container Interface](https://www.php-fig.org/psr/psr-11/)
- [Webpack Documentation](https://webpack.js.org/)
- [SASS Documentation](https://sass-lang.com/)

## Support

For issues and questions:
- GitHub Issues: https://github.com/demispatti/bonaire/issues
- WordPress.org Support: https://wordpress.org/support/plugin/bonaire

## License

GPL-2.0-or-later
