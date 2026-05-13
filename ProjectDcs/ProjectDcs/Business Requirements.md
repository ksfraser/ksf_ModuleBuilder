# ksf_ModuleBuilder - Business Requirements

## Document Information

| Field | Value |
|-------|-------|
| **Document ID** | BRD-MODULE-001 |
| **Module** | ksf_ModuleBuilder |
| **Project** | Module Builder/Generator |
| **Version** | 1.0.0 |
| **Author** | KS Fraser Development Team |
| **Created** | 2024-01-15 |
| **Status** | Draft |

---

## 1. Project Overview

### 1.1 Project Name
**ksf_ModuleBuilder** - FrontAccounting Module Generator

### 1.2 Project Type
Code Generation Tool

### 1.3 Core Functionality Summary
The ksf_ModuleBuilder module provides a code generation framework for creating FrontAccounting modules. It enables developers to define module structures including database tables, fields, relations, permissions, and generate complete module code including SQL installation scripts, PHP extension classes, hook files, and UI forms.

### 1.4 Target Users
- **Module Developers**: Creating new FrontAccounting modules
- **System Integrators**: Building custom solutions
- **Extension Builders**: Adding functionality to FA

---

## 2. Problem Statement

### 2.1 Business Problem
Creating FrontAccounting modules from scratch requires:
- Understanding FA conventions and patterns
- Writing repetitive boilerplate code
- Manually creating SQL schemas
- Implementing standard CRUD operations
- Setting up permission systems

### 2.2 Current Solution Gaps

| Gap | Impact |
|-----|--------|
| Manual code writing | Slow development, errors |
| Inconsistent module structure | Maintenance difficulty |
| Repeated patterns | Code duplication |
| Complex permission setup | Security configuration errors |

### 2.3 Opportunity
The ksf_ModuleBuilder provides:
- Declarative module definition
- Automatic code generation
- Standardized module structure
- Consistent patterns and conventions
- Reduced development time

---

## 3. Project Scope

### 3.1 In-Scope Features

#### Core Generation
1. **Module Definition**
   - Define module name, label, description
   - Configure table names and prefixes
   - Set version information

2. **Field Definition**
   - Define database fields
   - Specify data types (varchar, int, text, date, etc.)
   - Configure validation rules
   - Set default values
   - Define indexes

3. **SQL Generation**
   - Generate CREATE TABLE statements
   - Create index definitions
   - Add foreign key constraints
   - Generate installation SQL
   - Generate uninstallation SQL

4. **Extension Class Generation**
   - Generate FA extension classes
   - CRUD operation methods
   - Permission checking methods
   - Attachment handling

5. **Hook File Generation**
   - Generate module hook files
   - Create menu entries
   - Register install/uninstall hooks

6. **Form View Generation**
   - Generate HTML form templates
   - Create list views
   - Configure form fields

### 3.2 Out-of-Scope Features
- Visual module builder UI
- Database migration management
- Module marketplace
- Automatic testing generation

### 3.3 Project Boundaries

```
┌─────────────────────────────────────────────────────────────┐
│               ksf_ModuleBuilder Module                       │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────┐ │
│  │ ModuleDefinition│  │FieldDefinition │  │   Relations │ │
│  │ - Name          │  │ - Name          │  │ - Table refs│ │
│  │ - Table         │  │ - Type         │  │ - Columns   │ │
│  │ - Label         │  │ - Validation   │  │ - Cascade   │ │
│  └─────────────────┘  └─────────────────┘  └─────────────┘ │
│                                                              │
│  ┌─────────────────────────────────────────────────────────┐│
│  │              CodeGenerator                               ││
│  │  - generateInstallationSql()                             ││
│  │  - generateExtensionClass()                              ││
│  │  - generateHookFile()                                    ││
│  │  - generateFormView()                                    ││
│  └─────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────┘
```

---

## 4. Module Features

### 4.1 Module Definition

#### F-001: Define Module
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-001 |
| **Priority** | High |
| **Complexity** | Medium |

**Specification**:
- Define module name (PascalCase)
- Define table name with prefix
- Set label for UI display
- Configure description
- Set version number

**Properties**:
| Property | Type | Default |
|----------|------|---------|
| name | string | Required |
| table | string | Auto-generated |
| prefix | string | 'fa_' |
| label | string | Required |
| description | string | '' |
| version | string | '1.0.0' |

#### F-002: Optional Module Features
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-002 |
| **Priority** | Medium |

**Optional Features**:
| Feature | Property | Generated Columns |
|---------|----------|-------------------|
| Attachments | has_attachments | file_path, file_name |
| Comments | has_comments | comment_count |
| Workflow | has_workflow | Workflow triggers |
| Revision | has_revision | revision counter |

### 4.2 Field Definition

#### F-003: Define Fields
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-003 |
| **Priority** | High |

**Data Types Supported**:
| Type | SQL Type | Notes |
|------|----------|-------|
| varchar | VARCHAR | Requires length |
| text | TEXT | No length |
| int/integer | INT | Full precision |
| bigint | BIGINT | Large numbers |
| decimal | DECIMAL(10,2) | Currency |
| float | FLOAT | Approximate |
| boolean/bool | TINYINT(1) | 0/1 |
| date | DATE | YYYY-MM-DD |
| datetime | DATETIME | Full timestamp |
| timestamp | TIMESTAMP | Auto-update |
| email | VARCHAR(255) | Email validation |
| phone | VARCHAR(50) | Phone format |
| url | VARCHAR(500) | URL validation |
| json | JSON | JSON data |
| file | VARCHAR(500) | File path |
| enum | ENUM | Set values |

**Field Properties**:
| Property | Type | Default |
|----------|------|---------|
| name | string | Required |
| type | string | 'varchar' |
| label | string | Auto-generated |
| required | bool | false |
| default | mixed | null |
| length | int | null |
| options | array | [] |
| unique | bool | false |
| indexed | bool | false |
| validation | string | null |

### 4.3 Code Generation

#### F-004: SQL Installation Script
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-004 |
| **Priority** | High |

**Generated SQL**:
```sql
CREATE TABLE IF NOT EXISTS 0_module_name (
    id INT AUTO_INCREMENT PRIMARY KEY,
    [field definitions],
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by VARCHAR(50),
    updated_by VARCHAR(50),
    active INT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### F-005: Extension Class
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-005 |
| **Priority** | High |

**Generated Methods**:
| Method | Purpose |
|--------|---------|
| install() | Create tables |
| uninstall() | Drop tables |
| get_access() | Check permissions |
| can_view() | View permission |
| can_edit() | Edit permission |
| can_delete() | Delete permission |
| add() | Insert record |
| update() | Update record |
| delete() | Soft delete |
| get() | Get single record |
| get_all() | Get all records |
| add_attachment() | Attach file |
| get_attachments() | List attachments |

#### F-006: Hook File
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-006 |
| **Priority** | Medium |

**Generated Hooks**:
- Menu registration
- Install hook
- Uninstall hook
- Custom action hooks

### 4.4 Relations and Permissions

#### F-007: Table Relations
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-007 |
| **Priority** | Medium |

**Relation Definition**:
```php
$module->addRelation([
    'table' => 'related_table',
    'columns' => [
        ['name' => 'id', 'type' => 'INT'],
        ['name' => 'module_id', 'type' => 'INT']
    ],
    'delete_cascade' => true
]);
```

#### F-008: Permission Configuration
| Attribute | Value |
|-----------|-------|
| **Feature ID** | F-008 |
| **Priority** | Medium |

**Permission Definition**:
```php
$module->addPermission([
    'role' => 1,  // Admin role
    'can_view' => 1,
    'can_edit' => 1,
    'can_delete' => 1
]);
```

---

## 5. Integration Dependencies

### 5.1 Platform Dependencies

| Component | Required | Description |
|-----------|----------|-------------|
| FrontAccounting | Yes | Target platform |
| PHP | 7.3+ | Language |
| MySQL | 5.7+ | Database |

### 5.2 Code Generation Targets

| Target | Generated File | Content |
|--------|---------------|---------|
| SQL Install | `sql/install.sql` | CREATE TABLE statements |
| SQL Uninstall | `sql/uninstall.sql` | DROP TABLE statements |
| Extension Class | `includes/{Module}.php` | PHP class |
| Hook File | `includes/{module}_hooks.php` | Hook functions |
| Form View | `pages/{module}.php` | UI page |

---

## 6. Usage Example

### 6.1 Basic Module Definition

```php
use Ksfraser\ModuleBuilder\ModuleDefinition;
use Ksfraser\ModuleBuilder\FieldDefinition;
use Ksfraser\ModuleBuilder\CodeGenerator;

// Define module
$module = ModuleDefinition::create('Customer', 'Customer Management')
    ->description('Customer relationship management')
    ->version('1.0.0')
    ->hasAttachments()
    ->hasComments();

// Add fields
$module
    ->addField(FieldDefinition::make('name', 'varchar')->label('Customer Name')->required()->length(100))
    ->addField(FieldDefinition::make('email', 'email')->required()->unique())
    ->addField(FieldDefinition::make('phone', 'phone')->length(50))
    ->addField(FieldDefinition::make('company', 'varchar')->length(200))
    ->addField(FieldDefinition::make('status', 'enum')->options(['active', 'inactive', 'prospect']));

// Generate code
$generator = CodeGenerator::forModule($module);

$sql = $generator->generateInstallationSql();
$class = $generator->generateExtensionClass();
$hooks = $generator->generateHookFile();
$form = $generator->generateFormView();
```

### 6.2 Generated Output Example

**Extension Class**:
```php
class FACustomer
{
    public static $table = 'fa_customer';
    public static $prefix = 'fa_';
    public static $module_name = 'Customer';
    
    public static function install() { ... }
    public static function add($data) { ... }
    public static function update($id, $data) { ... }
    public static function delete($id) { ... }
    public static function get($id) { ... }
    public static function get_all($filter = []) { ... }
}
```

---

## 7. Success Criteria

### 7.1 Functional Criteria
- [ ] Module definitions created correctly
- [ ] Field definitions accepted
- [ ] SQL generated accurately
- [ ] Extension classes generated
- [ ] Hook files created
- [ ] Form views generated

### 7.2 Code Quality Criteria
- [ ] Generated SQL is valid MySQL
- [ ] PHP classes follow PSR standards
- [ ] Code matches FA conventions
- [ ] No syntax errors in generated code

### 7.3 Performance Criteria
- [ ] Generation completes < 1 second
- [ ] Memory usage < 64MB
- [ ] Handles 100+ fields

---

**Document Owner**: KS Fraser Development Team  
**Approval Status**: Pending Review