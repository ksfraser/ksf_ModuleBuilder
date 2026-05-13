# ksf_ModuleBuilder - Use Case Specification

## Document Information

| Field | Value |
|-------|-------|
| **Document ID** | UCD-MODULE-001 |
| **Module** | ksf_ModuleBuilder |
| **Project** | Module Builder/Generator |
| **Version** | 1.0.0 |
| **Author** | KS Fraser Development Team |
| **Created** | 2024-01-15 |

---

## 1. Use Case Overview

### 1.1 Actor Definitions

| Actor | Description |
|-------|-------------|
| **Module Developer** | Creates new FrontAccounting modules |
| **System Integrator** | Builds custom solutions |
| **Framework Architect** | Maintains module standards |

### 1.2 Use Case Summary

| UC ID | Use Case | Actor | Priority |
|-------|----------|-------|----------|
| UC-001 | Define Module | Module Developer | High |
| UC-002 | Add Fields | Module Developer | High |
| UC-003 | Configure Relations | Module Developer | Medium |
| UC-004 | Generate SQL | Module Developer | High |
| UC-005 | Generate Extension Class | Module Developer | High |
| UC-006 | Generate Hook File | Module Developer | Medium |
| UC-007 | Export Configuration | System Integrator | Low |

---

## 2. Use Case Details

### 2.1 UC-001: Define Module

**Primary Actor**: Module Developer  
**Priority**: High

#### Description
Create a new module definition with basic properties.

#### Basic Flow
```
1. Developer calls ModuleDefinition::create('Customer', 'Customer Management')
2. System creates ModuleDefinition instance
3. Developer configures optional properties:
   a. prefix('fa_')
   b. description('CRM module')
   c. version('1.2.0')
4. Developer enables optional features:
   a. hasAttachments()
   b. hasComments()
5. Developer adds fields
6. System validates module definition
```

#### Post-Conditions
- Module definition created with unique identifier
- All properties configured
- Ready for code generation

---

### 2.2 UC-002: Add Fields

**Primary Actor**: Module Developer  
**Priority**: High

#### Description
Add database field definitions to a module.

#### Basic Flow
```
1. Developer has module definition
2. Developer creates field:
   FieldDefinition::make('name', 'varchar')
3. Developer configures field:
   ->label('Customer Name')
   ->required()
   ->length(100)
   ->unique()
4. Developer adds field to module:
   module->addField($field)
5. Repeat for additional fields
```

#### Field Types Example
```php
$module
    ->addField(FieldDefinition::make('name', 'varchar')->label('Customer Name')->required()->length(100)->unique())
    ->addField(FieldDefinition::make('email', 'email')->required())
    ->addField(FieldDefinition::make('phone', 'phone')->length(50))
    ->addField(FieldDefinition::make('status', 'enum')->options(['active', 'inactive'])))
    ->addField(FieldDefinition::make('notes', 'text'))
    ->addField(FieldDefinition::make('created_at', 'datetime'));
```

#### Post-Conditions
- All fields defined
- Field collection ready for code generation

---

### 2.3 UC-003: Configure Relations

**Primary Actor**: Module Developer  
**Priority**: Medium

#### Description
Define foreign key relationships between tables.

#### Basic Flow
```
1. Developer adds relation:
   module->addRelation([
       'table' => 'customer_addresses',
       'columns' => [
           ['name' => 'id', 'type' => 'INT'],
           ['name' => 'customer_id', 'type' => 'INT'],
           ['name' => 'address', 'type' => 'VARCHAR']
       ],
       'delete_cascade' => true
   ])
2. System validates relation structure
3. System prepares SQL generation
```

#### Post-Conditions
- Relations configured
- SQL generation includes foreign key creation

---

### 2.4 UC-004: Generate SQL

**Primary Actor**: Module Developer  
**Priority**: High

#### Description
Generate SQL installation script for module.

#### Basic Flow
```
1. Developer has complete module definition
2. Developer creates code generator:
   $generator = CodeGenerator::forModule($module)
3. Developer calls SQL generation:
   $sql = $generator->generateInstallationSql()
4. System constructs CREATE TABLE statement
5. System generates index statements
6. System returns complete SQL string
```

#### Alternative Flow: With Relations
```
4a.1. System also generates relation table SQL
4a.2. System generates foreign key constraints
```

#### Post-Conditions
- SQL script generated
- Ready for file output

---

### 2.5 UC-005: Generate Extension Class

**Primary Actor**: Module Developer  
**Priority**: High

#### Description
Generate FrontAccounting PHP extension class.

#### Basic Flow
```
1. Developer has module definition
2. Developer creates generator:
   $generator = CodeGenerator::forModule($module)
3. Developer generates class:
   $class = $generator->generateFAExtensionClass()
4. System generates class with all methods
5. System returns PHP code string
```

#### Generated Methods
- install() - Create tables
- uninstall() - Drop tables
- add(), update(), delete() - CRUD
- get(), get_all() - Retrieval
- can_view(), can_edit(), can_delete() - Permissions
- add_attachment(), get_attachments() - Attachments

#### Post-Conditions
- PHP class generated
- Follows FA conventions

---

### 2.6 UC-006: Generate Hook File

**Primary Actor**: Module Developer  
**Priority**: Medium

#### Description
Generate module hook file for FA integration.

#### Basic Flow
```
1. Developer has module definition
2. Developer generates hooks:
   $hooks = $generator->generateHookFile()
3. System generates hook file content:
   - Menu entry registration
   - Install function
   - Uninstall function
   - Custom hooks
4. System returns PHP code string
```

#### Post-Conditions
- Hook file generated
- Integration points defined

---

### 2.7 UC-007: Export Configuration

**Primary Actor**: System Integrator  
**Priority**: Low

#### Description
Export module definition as JSON for storage/versioning.

#### Basic Flow
```
1. Developer has module definition
2. Developer exports:
   $config = $module->toArray()
   // OR
   $json = $generator->generateJsonConfig()
3. System serializes definition
4. System returns JSON string
```

#### Alternative Flow: Import
```
1. Developer loads JSON
2. Developer imports:
   $module = ModuleDefinition::fromArray($json)
3. System deserializes
4. System returns ModuleDefinition
```

#### Post-Conditions
- Configuration exported/imported
- Versioning supported

---

## 3. Business Process Flow

### 3.1 Module Creation Workflow

```
┌─────────────┐    ┌─────────────────┐    ┌─────────────┐
│  Define     │───▶│  Add Fields     │───▶│  Configure │
│  Module     │    │                 │    │  Relations  │
└─────────────┘    └─────────────────┘    └─────────────┘
                                                 │
                    ┌─────────────┐              │
                    │ Configure  │◀─────────────┘
                    │  Permissions│
                    └─────────────┘
                          │
                          ▼
                    ┌─────────────┐    ┌─────────────┐
                    │  Generate   │───▶│  Write      │
                    │  Code       │    │  Files     │
                    └─────────────┘    └─────────────┘
```

---

## 4. Requirements Traceability

| Use Case | Requirements | Test Cases |
|----------|--------------|------------|
| UC-001 | FR-001, FR-002 | TC-001, TC-002 |
| UC-002 | FR-003, FR-004 | TC-003, TC-004 |
| UC-003 | FR-010 | TC-010 |
| UC-004 | FR-005 | TC-005 |
| UC-005 | FR-006 | TC-006 |
| UC-006 | FR-007 | TC-007 |
| UC-007 | FR-009 | TC-009 |

---

**Document Owner**: KS Fraser Development Team  
**Review Status**: Pending