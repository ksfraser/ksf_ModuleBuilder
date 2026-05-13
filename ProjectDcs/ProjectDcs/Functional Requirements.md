# ksf_ModuleBuilder - Functional Requirements

## Document Information

| Field | Value |
|-------|-------|
| **Document ID** | FRD-MODULE-001 |
| **Module** | ksf_ModuleBuilder |
| **Project** | Module Builder/Generator |
| **Version** | 1.0.0 |
| **Author** | KS Fraser Development Team |
| **Created** | 2024-01-15 |

---

## 1. Introduction

### 1.1 Purpose
This document defines functional requirements for the ksf_ModuleBuilder module covering module definition, field configuration, and code generation for FrontAccounting modules.

### 1.2 Scope
Requirements cover the builder API, code generation, and output formats.

---

## 2. Functional Requirements

### 2.1 Module Definition

#### FR-001: Create Module Definition
| Requirement ID | FR-001 |
|----------------|--------|
| **Priority** | High |
| **Complexity** | Low |

**Description**: Create a new module definition with basic properties.

**Properties**:
| Property | Type | Required | Default |
|----------|------|----------|---------|
| name | string | Yes | - |
| label | string | Yes | - |
| table | string | No | Auto from name |
| prefix | string | No | 'fa_' |
| description | string | No | '' |
| version | string | No | '1.0.0' |

**Fluent API**:
```php
$module = ModuleDefinition::create('Customer', 'Customer Management')
    ->prefix('fa_')
    ->description('Customer relationship management')
    ->version('1.2.0');
```

---

#### FR-002: Configure Optional Features
| Requirement ID | FR-002 |
|----------------|--------|
| **Priority** | Medium |

**Description**: Enable optional module features.

**Features**:
| Feature | Method | Adds Columns |
|---------|--------|-------------|
| Attachments | hasAttachments() | file_path, file_name |
| Comments | hasComments() | comment_count |
| Workflow | hasWorkflow() | Workflow triggers |
| Revision | hasRevision() | revision counter |

**Usage**:
```php
$module
    ->hasAttachments()
    ->hasComments()
    ->hasWorkflow()
    ->hasRevision();
```

---

### 2.2 Field Definition

#### FR-003: Add Fields to Module
| Requirement ID | FR-003 |
|----------------|--------|
| **Priority** | High |

**Description**: Add field definitions to a module.

**Field Properties**:
| Property | Type | Default |
|----------|------|---------|
| name | string | Required |
| type | string | 'varchar' |
| label | ?string | Auto-generated |
| required | bool | false |
| default | mixed | null |
| length | ?int | null |
| options | array | [] |
| unique | bool | false |
| indexed | bool | false |
| validation | ?string | null |

**Usage**:
```php
$module->addField(FieldDefinition::make('name', 'varchar')
    ->label('Customer Name')
    ->required()
    ->length(100)
    ->unique());
```

---

#### FR-004: Supported Field Types
| Requirement ID | FR-004 |
|----------------|--------|
| **Priority** | High |

**Data Types**:
| Type | SQL | Notes |
|------|------|-------|
| varchar | VARCHAR(n) | Requires length |
| text | TEXT | No length |
| int | INT | - |
| bigint | BIGINT | - |
| decimal | DECIMAL(10,2) | - |
| float | FLOAT | - |
| boolean | TINYINT(1) | - |
| date | DATE | - |
| datetime | DATETIME | - |
| timestamp | TIMESTAMP | - |
| email | VARCHAR(255) | - |
| phone | VARCHAR(50) | - |
| url | VARCHAR(500) | - |
| json | JSON | - |
| file | VARCHAR(500) | - |
| enum | ENUM | Requires options |

---

### 2.3 Code Generation

#### FR-005: Generate SQL Installation Script
| Requirement ID | FR-005 |
|----------------|--------|
| **Priority** | High |

**Description**: Generate SQL CREATE TABLE statement.

**Output**:
```sql
CREATE TABLE IF NOT EXISTS {prefix}{table} (
    id INT AUTO_INCREMENT PRIMARY KEY,
    {fields},
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by VARCHAR(50),
    updated_by VARCHAR(50),
    active INT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_{field} ON {prefix}{table}({field});
```

**Rules**:
- Primary key always named 'id'
- Audit columns always included
- Active column for soft delete
- Indexes for indexed fields

---

#### FR-006: Generate Extension Class
| Requirement ID | FR-006 |
|----------------|--------|
| **Priority** | High |

**Description**: Generate FrontAccounting extension class.

**Generated Methods**:
| Method | Return | Description |
|--------|--------|-------------|
| install() | void | Create tables |
| uninstall() | void | Drop tables |
| get_access() | array | Get user access |
| can_view() | bool | Check view permission |
| can_edit() | bool | Check edit permission |
| can_delete() | bool | Check delete permission |
| add($data) | int | Insert and return ID |
| update($id, $data) | void | Update record |
| delete($id) | void | Soft delete |
| get($id) | array | Get single record |
| get_all($filter) | array | Get filtered records |
| add_attachment() | void | Attach file |
| get_attachments() | array | List attachments |
| list_view($params) | array | List view config |

---

#### FR-007: Generate Hook File
| Requirement ID | FR-007 |
|----------------|--------|
| **Priority** | Medium |

**Description**: Generate module hook file with install/uninstall hooks.

**Output**:
```php
<?php

add_menu_entry('{name}', '{label}', '{name}');

function ksf_fa_{nameLower}_install()
{
    FA{ModuleName}::install();
}

function ksf_fa_{nameLower}_uninstall()
{
    FA{ModuleName}::uninstall();
}

// Custom hooks
add_hook('{hook_name}', function($params) {
    return FA{ModuleName}::hook_{action}($params);
});
```

---

#### FR-008: Generate Form View
| Requirement ID | FR-008 |
|----------------|--------|
| **Priority** | Medium |

**Description**: Generate HTML form view for module data entry.

**Generated Form Fields**:
| Field Type | HTML Element | Notes |
|------------|--------------|-------|
| varchar | `<input type="text">` | With length |
| text | `<textarea>` | 4 rows |
| enum | `<select>` | With options |
| date | `<input type="date">` | - |
| datetime | `<input type="datetime">` | - |

**Output Template**:
```html
<div class="panel panel-default">
    <div class="panel-heading">
        <h3>{module_label}</h3>
    </div>
    <div class="panel-body">
        <form method="post" id="{name}_form">
            <input type="hidden" name="added_by" value="<?php echo get_current_user(); ?>">
            {fields}
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
```

---

#### FR-009: Generate JSON Configuration
| Requirement ID | FR-009 |
|----------------|--------|
| **Priority** | Low |

**Description**: Export module definition as JSON.

**Output Format**:
```json
{
    "name": "Customer",
    "table": "fa_customer",
    "prefix": "fa_",
    "label": "Customer Management",
    "description": "...",
    "version": "1.0.0",
    "has_attachments": true,
    "has_comments": false,
    "has_workflow": false,
    "has_revision": false,
    "fields": [...],
    "relations": [...],
    "permissions": [...],
    "workflow_triggers": [...],
    "hooks": [...]
}
```

---

### 2.4 Relations and Permissions

#### FR-010: Add Table Relations
| Requirement ID | FR-010 |
|----------------|--------|
| **Priority** | Medium |

**Description**: Define foreign key relationships.

**Definition**:
```php
$module->addRelation([
    'table' => 'customer_addresses',
    'columns' => [
        ['name' => 'id', 'type' => 'INT'],
        ['name' => 'customer_id', 'type' => 'INT'],
        ['name' => 'address', 'type' => 'VARCHAR']
    ],
    'delete_cascade' => true
]);
```

---

#### FR-011: Configure Permissions
| Requirement ID | FR-011 |
|----------------|--------|
| **Priority** | Medium |

**Description**: Define role-based permissions.

**Definition**:
```php
$module->addPermission([
    'role' => 1,  // Admin role
    'can_view' => 1,
    'can_edit' => 1,
    'can_delete' => 1
]);
```

---

## 3. Data Requirements

### 3.1 Input: Module Definition

| Field | Type | Validation |
|-------|------|-------------|
| name | string | Required, PascalCase |
| label | string | Required |
| table | string | Optional, lowercase with underscore |
| prefix | string | Optional, lowercase with underscore |
| fields | array | At least one field |

### 3.2 Input: Field Definition

| Field | Type | Validation |
|-------|------|-------------|
| name | string | Required, snake_case |
| type | string | Required, valid type |
| label | string | Optional |
| required | bool | Optional |
| default | mixed | Optional |
| length | int | Required for varchar |
| options | array | Required for enum |

### 3.3 Output: Generated SQL

| Component | Format |
|-----------|--------|
| Table name | `{prefix}{table}` |
| Primary key | `id INT AUTO_INCREMENT PRIMARY KEY` |
| Field columns | `{name} {type}{length}` |
| Indexes | `CREATE INDEX idx_{name}` |

---

## 4. Non-Functional Requirements

### 4.1 Performance
| Metric | Target |
|--------|--------|
| Module creation | < 100ms |
| SQL generation | < 50ms |
| Class generation | < 100ms |
| Full generation | < 500ms |

### 4.2 Code Quality
- Generated SQL valid MySQL 5.7+
- Generated PHP follows PSR-12
- No syntax errors in output
- Proper escaping of names

---

**Document Owner**: KS Fraser Development Team  
**Review Status**: Pending