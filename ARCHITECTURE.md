# Bonaire - Architecture Documentation

## Overview

Bonaire follows a modern, maintainable architecture based on SOLID principles, dependency injection, and separation of concerns.

## Core Principles

### 1. **Dependency Injection**

All dependencies are injected through constructors, making code testable and loosely coupled.

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

### 2. **Single Responsibility**

Each class has one reason to change:
- **Controllers**: Handle WordPress hooks
- **Services**: Contain business logic
- **Views**: Render templates
- **Repositories**: Data access (if needed)

### 3. **Separation of Concerns**

```
┌─────────────────────────────────────────────┐
│           WordPress (Framework)              │
└─────────────────┬───────────────────────────┘
                  │
┌─────────────────▼───────────────────────────┐
│     Infrastructure Layer                     │
│  (Service Providers, WordPress Integration)  │
└─────────────────┬───────────────────────────┘
                  │
┌─────────────────▼───────────────────────────┐
│         Application Layer                    │
│     (Controllers, Services, Views)           │
└─────────────────┬───────────────────────────┘
                  │
┌─────────────────▼───────────────────────────┐
│          Domain Layer                        │
│    (Business Logic, Value Objects)           │
└─────────────────────────────────────────────┘
```

## Component Structure

### Core Layer (`src/Core/`)

**Purpose**: Foundation of the plugin

**Components**:
- `Container`: PSR-11 dependency injection container
- `Plugin`: Main plugin orchestrator
- `ServiceProvider`: Base class for service providers

**Responsibilities**:
- Manage dependency injection
- Bootstrap plugin components
- Coordinate service providers

### Infrastructure Layer (`src/Infrastructure/`)

**Purpose**: WordPress integration and external dependencies

**Components**:
- `ServiceProviders/`: Register and bootstrap services
  - `CoreServiceProvider`: Core functionality
  - `AdminServiceProvider`: Admin area
  - `FrontendServiceProvider`: Frontend functionality
- `Services/`: Infrastructure services
  - `I18nService`: Internationalization
  - `ActivationService`: Plugin activation
  - `DeactivationService`: Plugin deactivation

**Responsibilities**:
- WordPress hooks integration
- Service registration
- Plugin lifecycle management
- Translation loading

### Application Layer

#### Admin (`src/Admin/`)

**Purpose**: Admin area functionality

**Structure**:
```
Admin/
├── Controllers/          # Handle WordPress hooks
│   ├── AdminController.php
│   ├── SettingsController.php
│   ├── AjaxController.php
│   ├── DashboardWidgetController.php
│   └── MetaBoxController.php
├── Services/            # Business logic
│   ├── OptionsService.php
│   ├── AssetService.php
│   └── MailService.php (to be added)
└── Views/               # Rendering logic
    └── SettingsPageView.php
```

**Responsibilities**:
- Enqueue admin assets
- Register admin pages
- Handle AJAX requests
- Render admin interfaces
- Manage plugin options

#### Frontend (`src/Frontend/`)

**Purpose**: Public-facing functionality

**Structure**:
```
Frontend/
└── Controllers/
    └── FrontendController.php
```

**Responsibilities**:
- Integrate with Contact Form 7
- Process form submissions
- Store messages in Flamingo

## Data Flow

### Admin Settings Save Flow

```
User submits form
       │
       ▼
JavaScript (BonaireAdmin.js)
       │
       ▼
AJAX Handler (AjaxHandler.js)
       │
       ▼
WordPress AJAX (admin-ajax.php)
       │
       ▼
AjaxController::save_options()
       │
       ▼
OptionsService::validate()
       │
       ▼
OptionsService::save()
       │
       ▼
WordPress Options API
       │
       ▼
Success/Error Response
       │
       ▼
User Interface Update
```

### Contact Form Submission Flow

```
User submits Contact Form 7
       │
       ▼
FrontendController::capture_form_data()
       │
       ▼
Store in transient
       │
       ▼
FrontendController::after_mail_sent()
       │
       ▼
Queue for Flamingo
       │
       ▼
Flamingo creates message
       │
       ▼
Admin can reply via MetaBox
```

## Service Provider Pattern

Service providers encapsulate service registration and bootstrapping:

### Lifecycle

1. **Register Phase**: Bind services to container
```php
public function register( Container $container ): void {
    $container->singleton(
        OptionsService::class,
        fn( Container $c ) => new OptionsService( $c->get( 'plugin.domain' ) )
    );
}
```

2. **Boot Phase**: Initialize services and hooks
```php
public function boot( Container $container ): void {
    $admin_controller = $container->get( AdminController::class );
    $admin_controller->register_hooks();
}
```

## Asset Management

### Build Pipeline

```
Source Files (assets/src/)
       │
       ▼
Webpack Processing
       │
       ├─ Babel (JavaScript)
       ├─ SASS (Stylesheets)
       └─ Asset optimization
       │
       ▼
Distribution Files (assets/dist/)
       │
       ▼
WordPress Asset Enqueue
       │
       ▼
Browser
```

### Asset Organization

**JavaScript**:
- Modular ES6+ code
- Class-based architecture
- Separation of concerns
- Webpack bundling

**Stylesheets**:
- SCSS with variables and mixins
- Component-based organization
- BEM naming convention
- Mobile-first responsive

## Frontend Architecture (JavaScript)

### Module Structure

```
BonaireAdmin (main class)
├── FormHandler (form operations)
├── AjaxHandler (AJAX requests)
└── UIManager (UI updates)
```

### Communication Pattern

```
User Action
     │
     ▼
BonaireAdmin (coordinator)
     │
     ├─► FormHandler (get data)
     ├─► AjaxHandler (send request)
     └─► UIManager (update UI)
```

## Database Schema

### Options Structure

```php
bonaire_options = [
    0 => [  // Settings
        'username' => '',
        'password' => '',  // Encrypted
        'smtp_host' => '',
        'smtp_port' => 465,
        'smtpsecure' => 'ssl',
        // ... more settings
    ],
    1 => [  // Status
        'smtp_status' => 'orange',  // green|orange|red
        'imap_status' => 'inactive',
    ]
];
```

### Post Meta (Flamingo Messages)

```php
// Message recipient email
_field_email => 'user@example.com'

// Message sender name
_field_your-name => 'John Doe'

// Reply tracking
_bonaire_replied => '2024-01-01 12:00:00'
```

## Security Considerations

### Input Validation

1. **Sanitization**: All input sanitized before use
2. **Validation**: Type and format validation
3. **Nonce verification**: CSRF protection for forms/AJAX

### Data Storage

1. **Password encryption**: AES-256-CBC with WordPress salts
2. **Options sanitization**: Before saving to database
3. **SQL injection prevention**: Use WordPress database API

### Output Escaping

1. **HTML contexts**: `esc_html()`, `esc_attr()`
2. **URL contexts**: `esc_url()`
3. **JavaScript contexts**: `wp_json_encode()`

## Testing Strategy

### Unit Tests

- Test individual classes in isolation
- Mock dependencies
- Fast execution

### Integration Tests

- Test component interaction
- Use WordPress test framework
- Database transactions

### End-to-End Tests

- Test complete workflows
- Browser automation
- User perspective

## Performance Optimization

### Backend

1. **Autoloading**: PSR-4 autoloader (Composer)
2. **Lazy loading**: Services loaded on-demand
3. **Caching**: Transients for temporary data
4. **Database queries**: Efficient queries with proper indexes

### Frontend

1. **Asset minification**: Production builds minified
2. **Code splitting**: Separate admin/frontend bundles
3. **Lazy loading**: Load scripts only where needed
4. **CSS optimization**: Remove unused styles

## Extensibility

### Hooks and Filters

Plugin provides hooks for extensibility:

```php
// Action hooks
do_action( 'bonaire_activated' );
do_action( 'bonaire_before_send_reply', $reply_data );
do_action( 'bonaire_after_send_reply', $reply_data, $result );

// Filter hooks
$options = apply_filters( 'bonaire_options_meta', $options_meta );
$validated = apply_filters( 'bonaire_validate_options', $validated );
```

### Custom Service Providers

Developers can add custom service providers:

```php
add_filter( 'bonaire_service_providers', function( $providers ) {
    $providers[] = MyCustomServiceProvider::class;
    return $providers;
});
```

## Migration Notes

### From Legacy to Refactored

**Key Changes**:

1. **Namespace**: `Bonaire\` (consistent)
2. **Autoloading**: Composer PSR-4
3. **Dependency Injection**: Constructor injection
4. **Service Providers**: Centralized registration
5. **Modern JavaScript**: ES6+ modules
6. **SCSS**: Organized stylesheets

**Backward Compatibility**:

- Old hooks still work
- Options structure unchanged
- Database schema compatible

## Future Enhancements

### Planned Improvements

1. **Repository Pattern**: Data access abstraction
2. **Event System**: Domain events
3. **Command Bus**: CQRS pattern
4. **API Layer**: REST API endpoints
5. **Vue.js Components**: Modern UI components

### Scalability

- Microservices architecture (if needed)
- Queue system for async tasks
- Caching layer (Redis/Memcached)
- CDN for static assets

## Conclusion

This architecture provides:
- **Maintainability**: Clear structure, easy to understand
- **Testability**: Dependency injection, isolated components
- **Extensibility**: Hooks, filters, service providers
- **Performance**: Optimized assets, efficient code
- **Security**: Input validation, output escaping, encryption

The architecture follows WordPress best practices while incorporating modern PHP and JavaScript patterns for a professional, scalable codebase.
