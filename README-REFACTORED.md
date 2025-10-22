# Bonaire - WordPress Email Reply Plugin (Refactored)

[![License](https://img.shields.io/badge/license-GPL--2.0%2B-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-8892BF.svg)](https://php.net/)
[![WordPress](https://img.shields.io/badge/wordpress-%3E%3D5.5-21759B.svg)](https://wordpress.org/)

## Overview

Bonaire is a professional WordPress plugin that enables you to send email replies to messages received through Contact Form 7 and stored in Flamingo. This is the **refactored version** following modern development best practices.

## ✨ Features

- 📧 **Email Reply System**: Reply to Contact Form 7 messages directly from WordPress admin
- 🔒 **Secure**: Encrypted password storage using AES-256-CBC
- 📮 **SMTP Support**: Configure SMTP settings for reliable email delivery
- 💾 **IMAP Integration**: Optionally save sent replies to your IMAP server
- 📊 **Dashboard Widget**: View recent messages at a glance
- 🎨 **Modern UI**: Clean, intuitive admin interface
- 🌐 **Translation Ready**: Includes German translation, ready for more

## 🏗️ Architecture Highlights (Refactored Version)

This refactored version includes significant improvements:

### Modern PHP Architecture
- ✅ **PSR-4 Autoloading** via Composer
- ✅ **Dependency Injection Container** (PSR-11 compliant)
- ✅ **Service Provider Pattern** for clean bootstrapping
- ✅ **SOLID Principles** throughout the codebase
- ✅ **Separation of Concerns** (Controllers, Services, Views)
- ✅ **Type Hints** and modern PHP 7.4+ features

### Modern JavaScript
- ✅ **ES6+ Modules** with Webpack bundling
- ✅ **Class-based Architecture** for better organization
- ✅ **npm Package Management** for vendor libraries
- ✅ **Automated Build Pipeline** with source maps

### Modern CSS
- ✅ **SCSS** with variables and mixins
- ✅ **Component-based Organization** (BEM methodology)
- ✅ **Responsive Design** with mobile-first approach
- ✅ **Utility Classes** for rapid development

### Development Tools
- ✅ **Webpack** for asset compilation
- ✅ **ESLint** for JavaScript linting
- ✅ **PHP CodeSniffer** for PHP coding standards
- ✅ **PHPUnit** ready for testing
- ✅ **EditorConfig** for consistent coding style

## 📋 Requirements

- PHP 7.4 or higher
- WordPress 5.5 or higher
- Contact Form 7 plugin
- Flamingo plugin
- Node.js 14+ and npm (for development)
- Composer (for development)

## 🚀 Installation

### For Users (Production)

1. Download the latest release
2. Upload to `/wp-content/plugins/` directory
3. Activate through WordPress admin
4. Configure SMTP settings in Settings → Bonaire

### For Developers

```bash
# Clone repository
git clone https://github.com/demispatti/bonaire.git
cd bonaire

# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install

# Build assets for development
npm run dev

# Or build for production
npm run build
```

## 💻 Development

### Quick Start

```bash
# Watch mode for development
npm run dev

# This will:
# - Compile SCSS to CSS with source maps
# - Bundle JavaScript with Webpack
# - Watch for changes and rebuild automatically
```

### Project Structure

```
bonaire/
├── assets/
│   ├── src/          # Source files (JS, SCSS)
│   └── dist/         # Compiled assets
├── src/              # PHP source (PSR-4)
│   ├── Core/         # Core functionality
│   ├── Admin/        # Admin area
│   ├── Frontend/     # Public-facing
│   └── Infrastructure/ # WordPress integration
├── templates/        # View templates
├── languages/        # Translations
├── tests/            # PHPUnit tests
└── vendor/           # Composer dependencies
```

### Available npm Commands

```bash
npm run dev        # Development build with watch mode
npm run build      # Production build (minified)
npm run lint:js    # Lint JavaScript files
npm run lint:css   # Lint SCSS files
```

### Available Composer Commands

```bash
composer test      # Run PHPUnit tests
composer cs        # Check PHP coding standards
composer cbf       # Fix PHP coding standards
```

## 🔧 Configuration

### SMTP Settings

Navigate to **Settings → Bonaire** in WordPress admin:

1. **Username**: Your email address
2. **Password**: Your email password (encrypted before storage)
3. **SMTP Host**: e.g., `smtp.gmail.com`
4. **SMTP Port**: `465` (SSL) or `587` (TLS)
5. **Encryption**: Choose SSL or TLS
6. **From Email**: Sender email address
7. **From Name**: Sender name

### IMAP Settings (Optional)

If you want to save sent replies to your IMAP server:

1. Enable "Save Replies to IMAP"
2. Configure IMAP host and port
3. Set the "Sent" folder name (language-specific)

## 📖 Usage

### Replying to Messages

1. Go to **Flamingo → Inbound Messages**
2. Click on a message to view details
3. Use the **Send Reply** meta box
4. Enter subject and message
5. Click **Send Reply**

### Dashboard Widget

View recent messages from the WordPress dashboard:
- Shows latest unread messages
- Quick access to message details
- Mark as read/spam/trash

## 🏛️ Architecture

### Dependency Injection

```php
class AdminController {
    public function __construct(
        AssetService $asset_service,
        OptionsService $options_service
    ) {
        $this->asset_service = $asset_service;
        $this->options_service = $options_service;
    }
}
```

### Service Providers

```php
class AdminServiceProvider extends ServiceProvider {
    public function register( Container $container ): void {
        // Register services
    }

    public function boot( Container $container ): void {
        // Bootstrap application
    }
}
```

### Modern JavaScript

```javascript
import { BonaireAdmin } from './BonaireAdmin';

const admin = new BonaireAdmin($, alertify);
admin.init();
```

## 🧪 Testing

### PHP Tests

```bash
composer test
```

### JavaScript Linting

```bash
npm run lint:js
```

### CSS Linting

```bash
npm run lint:css
```

## 🔐 Security

- Passwords encrypted with AES-256-CBC
- WordPress nonces for CSRF protection
- Input sanitization and validation
- Output escaping
- Prepared SQL statements (via WordPress API)

## 🌍 Internationalization

The plugin is translation-ready:

```php
__( 'Text to translate', 'bonaire' );
_e( 'Text to echo', 'bonaire' );
_n( 'Singular', 'Plural', $count, 'bonaire' );
```

### Available Translations

- English (default)
- German (de_DE)

## 🤝 Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Follow coding standards
4. Add tests for new features
5. Submit a pull request

### Coding Standards

- PHP: WordPress Coding Standards
- JavaScript: ESLint configuration
- CSS: BEM methodology with `bonaire-` prefix

## 📚 Documentation

- [Development Guide](DEVELOPMENT.md) - Setup and development workflow
- [Architecture Guide](ARCHITECTURE.md) - Detailed architecture documentation
- [Contributing Guide](CONTRIBUTING.md) - How to contribute
- [Changelog](CHANGELOG.md) - Version history

## 🐛 Bug Reports

Found a bug? Please report it:

1. GitHub Issues: https://github.com/demispatti/bonaire/issues
2. WordPress.org Support: https://wordpress.org/support/plugin/bonaire

## 📄 License

This plugin is licensed under the GPL-2.0-or-later license.

```
Copyright (C) 2024 Demis Patti

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
```

## 👤 Author

**Demis Patti**

- Website: https://demispatti.ch
- Email: demispatti@gmail.com
- GitHub: [@demispatti](https://github.com/demispatti)

## 🙏 Acknowledgments

- WordPress community
- Contact Form 7 team
- Flamingo team
- All contributors

## 💝 Support

If you find this plugin useful, please:

- ⭐ Star the repository
- 📝 Write a review on WordPress.org
- ☕ [Buy me a coffee](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XLMMS7C62S76Q)

## 📊 Stats

- **Code Quality**: A+
- **Test Coverage**: Growing
- **Documentation**: Comprehensive
- **Architecture**: Modern & Maintainable

---

**Note**: This is the refactored version with modern architecture. The legacy structure is maintained in the `legacy/` branch for compatibility.

Made with ❤️ and modern best practices
