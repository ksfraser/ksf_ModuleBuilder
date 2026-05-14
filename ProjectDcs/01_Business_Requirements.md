# Module Builder Module - Business Requirements

## Document Information

| Field | Value |
|-------|-------|
| Document Title | Business Requirements Specification |
| Module | ksf_ModuleBuilder |
| Version | 1.0.0 |
| Author | KSF Development Team |
| Last Updated | May 2026 |

---

## 1. Project Overview

### 1.1 Purpose Statement

The ModuleBuilder module provides a code generation framework for creating new KSF modules following established patterns and conventions. It enables developers to rapidly scaffold modules with proper namespace structure, PSR-4 autoloading, test infrastructure, and documentation templates.

### 1.2 Problem Statement

Creating new KSF modules requires:
- Copying existing module structures manually
- Ensuring PSR-4 compliance and namespace conventions
- Setting up test infrastructure from scratch
- Creating documentation templates

This leads to inconsistency and wasted development time.

### 1.3 Solution

ModuleBuilder automates module scaffolding with:
- Interactive module definition
- Template-based code generation
- Convention enforcement
- Documentation scaffolding

---

## 2. Scope Definition

### 2.1 In-Scope Features

#### Module Definition
- Define module metadata (name, version, description)
- Specify dependencies
- Define module type (Business Logic, UI, FA Adapter, WP Adapter)

#### Code Generation
- Generate composer.json with proper autoloading
- Generate source directory structure
- Generate entity, service, repository templates
- Generate test directory with PHPUnit setup
- Generate ProjectDcs documentation templates

#### Convention Enforcement
- PSR-4 namespace validation
- File-per-class requirement
- Proper use statements
- Exception hierarchy usage

### 2.2 Out-of-Scope Features
- Visual UI for module building
- Direct database schema generation
- Deployment automation

---

## 3. Feature Specifications

### 3.1 Module Definition

```php
$definition = new ModuleDefinition();
$definition->setName('CustomerPortal')
    ->setNamespace('Ksfraser\CustomerPortal')
    ->setType('Business Logic')
    ->setDependencies(['ksf_CRM', 'ksf_EmailManager'])
    ->addEntity('Customer')
    ->addService('CustomerService');
```

### 3.2 Generated Structure

```
ksf_CustomerPortal/
├── composer.json
├── src/
│   └── Ksfraser/
│       └── CustomerPortal/
│           ├── Entity/
│           │   └── Customer.php
│           ├── Service/
│           │   └── CustomerService.php
│           └── Events/
├── tests/
│   ├── Unit/
│   └── bootstrap.php
├── ProjectDcs/
│   ├── 01_Business_Requirements.md
│   ├── 02_Architecture.md
│   ├── 03_Functional_Requirements.md
│   ├── 04_Use_Case.md
│   ├── 05_Test_Plan.md
│   ├── 06_UAT_Plan.md
│   └── RTM.md
├── phpunit.xml
└── README.md
```

### 3.3 Field Definition

```php
$field = new FieldDefinition('email', 'string');
$field->setRequired(true)
    ->setMaxLength(255)
    ->setUnique(true);

$field = new FieldDefinition('status', 'enum');
$field->setOptions(['active', 'inactive', 'suspended']);
```

---

## 4. Integration

### 4.1 Dependencies

| Module | Purpose |
|--------|---------|
| None | Pure generation tool |

### 4.2 Generated Code Integration

Modules created by ModuleBuilder integrate with:
- `ksfraser/traits` - Reusable traits
- `ksfraser/exceptions` - Exception hierarchy
- `psr/event-dispatcher` - Event system

---

## 5. Revision History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0.0 | May 2026 | KSF Development Team | Initial specification |