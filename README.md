Ambelz JSON-to-Form Demo
========================

This demo application showcases the **ambelz/json-to-form** bundle capabilities with **Symfony Live Components**. It demonstrates how to create dynamic, interactive forms from JSON configurations without writing custom JavaScript.

## What is json-to-form?

The `ambelz/json-to-form` bundle is an open-source Symfony package that transforms JSON schema definitions into fully functional Symfony forms. This demo shows real-world use cases and advanced features.

## Key Features Demonstrated

### 🚀 Live Components Integration
- **Real-time form rendering** from JSON configurations
- **Interactive form updates** without page reloads
- **Dynamic field validation** and error handling
- **Seamless user experience** with modern UX patterns

### 📋 Real-World Form Examples
The demo includes 5 practical form types commonly used in web applications:

1. **Registration Forms** - User signup with validation
2. **Login Forms** - Authentication with security features  
3. **Contact Forms** - Communication with file uploads
4. **Document Upload** - File handling with progress tracking
5. **Complex Forms** - Advanced use cases (orders, quotes, surveys)

### 🎨 Advanced Capabilities
- **Timezone handling** - Automatic timezone field normalization
- **Collection management** - Dynamic add/remove of form sections
- **Custom field types** - Extended form field support
- **Theme customization** - Flexible styling options
- **API integration** - Form generation via REST API

## Requirements

- **PHP 8.2+** with latest PHP 8 features
- **Symfony 7.x** with Live Components
- **PDO-SQLite** for demo data storage
- Standard Symfony application requirements

## Quick Start

### Installation

```bash
# Clone the repository
git clone https://github.com/ambelz/json-to-form-demo.git
cd json-to-form-demo

# Install dependencies
composer install

# Set up the database
php bin/console doctrine:migrations:migrate
```

### Running the Demo

**Option 1: Symfony CLI (Recommended)**
```bash
symfony serve
```

**Option 2: Built-in PHP Server**
```bash
php -S localhost:8000 -t public/
```

Visit `http://localhost:8000` to explore the interactive demo and enjoy !