# ksf_ModuleBuilder - Test Plan

## Document Information

| Field | Value |
|-------|-------|
| **Document ID** | TP-MODULE-001 |
| **Module** | ksf_ModuleBuilder |
| **Project** | Module Builder/Generator |
| **Version** | 1.0.0 |
| **Author** | KS Fraser Development Team |
| **Created** | 2024-01-15 |

---

## 1. Test Scenarios

### 1.1 TC-001: Module Creation

**Test Case ID**: TC-001  
**Class**: `ModuleDefinition`  
**Method**: `create()`

#### Test Steps
1. Call `ModuleDefinition::create('Customer', 'Customer Management')`
2. Assert name is 'Customer'
3. Assert label is 'Customer Management'
4. Assert table is 'fa_customer' (auto-generated)
5. Assert prefix is 'fa_' (default)

#### Pass Criteria
- [ ] Module instance created
- [ ] Properties set correctly
- [ ] Table name auto-generated from name

---

### 1.2 TC-002: Optional Features

**Test Case ID**: TC-002  
**Class**: `ModuleDefinition`  
**Method**: `hasAttachments()`, `hasComments()`, `hasWorkflow()`, `hasRevision()`

#### Test Steps
1. Create module
2. Call `hasAttachments()`
3. Assert has_attachments is true
4. Call `hasComments()`
5. Assert has_comments is true

#### Pass Criteria
- [ ] All feature flags settable
- [ ] Fluent interface returns self

---

### 1.3 TC-003: Field Creation

**Test Case ID**: TC-003  
**Class**: `FieldDefinition`  
**Method**: `make()`

#### Test Data
```php
$field = FieldDefinition::make('email', 'email');
```

#### Test Steps
1. Call `FieldDefinition::make('email', 'email')`
2. Assert name is 'email'
3. Assert type is 'email'
4. Assert label is null (auto-generates later)
5. Assert required is false (default)

#### Pass Criteria
- [ ] Field instance created
- [ ] Properties set from constructor

---

### 1.4 TC-004: Field Configuration

**Test Case ID**: TC-004  
**Class**: `FieldDefinition`  
**Method**: Fluent configuration methods

#### Test Steps
1. Create field: `FieldDefinition::make('name', 'varchar')`
2. Chain: `->label('Full Name')->required()->length(100)->unique()->indexed()`
3. Assert label is 'Full Name'
4. Assert required is true
5. Assert length is 100
6. Assert unique is true
7. Assert indexed is true

#### Pass Criteria
- [ ] All fluent methods work
- [ ] Properties set correctly
- [ ] Fluent returns self

---

### 1.5 TC-005: SQL Generation

**Test Case ID**: TC-005  
**Class**: `ModuleDefinition`  
**Method**: `getSqlCreate()`

#### Test Data
```php
$module = ModuleDefinition::create('Customer', 'Customer');
$module->addField(FieldDefinition::make('name', 'varchar')->required()->length(100));
$module->addField(FieldDefinition::make('email', 'email')->unique());
```

#### Test Steps
1. Call `$module->getSqlCreate()`
2. Assert SQL contains 'CREATE TABLE'
3. Assert SQL contains 'id INT AUTO_INCREMENT PRIMARY KEY'
4. Assert SQL contains 'name VARCHAR(100) NOT NULL'
5. Assert SQL contains 'email VARCHAR(255) UNIQUE'
6. Assert SQL contains 'created_at DATETIME'
7. Assert SQL contains 'active INT DEFAULT 1'

#### Pass Criteria
- [ ] Valid CREATE TABLE statement
- [ ] All fields included
- [ ] Audit columns present
- [ ] Proper indexes

---

### 1.6 TC-006: Extension Class Generation

**Test Case ID**: TC-006  
**Class**: `CodeGenerator`  
**Method**: `generateFAExtensionClass()`

#### Test Data
```php
$module = ModuleDefinition::create('Customer', 'Customer Management');
$module->addField(FieldDefinition::make('name', 'varchar'));
$generator = CodeGenerator::forModule($module);
```

#### Test Steps
1. Call `generateFAExtensionClass()`
2. Assert contains 'class FACustomer'
3. Assert contains 'public static $table'
4. Assert contains 'public static function install()'
5. Assert contains 'public static function add($data)'
6. Assert contains 'public static function get_all($filter = [])'

#### Pass Criteria
- [ ] Valid PHP class
- [ ] Static properties defined
- [ ] CRUD methods present
- [ ] Permission methods present

---

### 1.7 TC-007: Hook File Generation

**Test Case ID**: TC-007  
**Class**: `CodeGenerator`  
**Method**: `generateHookFile()`

#### Test Steps
1. Call `generateHookFile()`
2. Assert contains 'add_menu_entry'
3. Assert contains 'ksf_fa_customer_install'
4. Assert contains 'ksf_fa_customer_uninstall'
5. Assert contains 'FA Customer::install()'

#### Pass Criteria
- [ ] Hook functions generated
- [ ] Menu entry created
- [ ] Install/uninstall hooks present

---

### 1.8 TC-008: Form View Generation

**Test Case ID**: TC-008  
**Class**: `CodeGenerator`  
**Method**: `generateFormView()`

#### Test Steps
1. Call `generateFormView()`
2. Assert contains '<form'
3. Assert contains form inputs
4. Assert contains '<select>' for enum fields
5. Assert contains '<textarea>' for text fields
6. Assert contains submit button

#### Pass Criteria
- [ ] Form HTML generated
- [ ] All field types handled
- [ ] Form structure valid

---

### 1.9 TC-009: JSON Export/Import

**Test Case ID**: TC-009  
**Class**: `ModuleDefinition`  
**Methods**: `toArray()`, `fromArray()`

#### Test Steps
1. Create module with fields
2. Call `toArray()`
3. Assert valid JSON
4. Assert contains 'name', 'fields', etc.
5. Call `fromArray($json)`
6. Assert new module matches original

#### Pass Criteria
- [ ] toArray() returns valid structure
- [ ] fromArray() recreates module
- [ ] Round-trip preserves data

---

### 1.10 TC-010: Type Mapping

**Test Case ID**: TC-010  
**Class**: `ModuleDefinition`  
**Method**: `mapType()` (private, tested via getSqlCreate())

#### Test Data
| PHP Type | Expected SQL Type |
|----------|------------------|
| varchar | VARCHAR |
| text | TEXT |
| int | INT |
| bigint | BIGINT |
| decimal | DECIMAL(10,2) |
| float | FLOAT |
| boolean | TINYINT(1) |
| date | DATE |
| datetime | DATETIME |
| email | VARCHAR(255) |
| json | JSON |

#### Test Steps
1. Create module with each field type
2. Generate SQL
3. Assert each type maps correctly

#### Pass Criteria
- [ ] All type mappings correct
- [ ] Length applied to varchar
- [ ] Default lengths for special types

---

## 2. Test Execution Matrix

| Test Case | Class | Method | Priority | Status |
|-----------|-------|--------|----------|--------|
| TC-001 | ModuleDefinition | create() | High | Pending |
| TC-002 | ModuleDefinition | hasAttachments() | Medium | Pending |
| TC-003 | FieldDefinition | make() | High | Pending |
| TC-004 | FieldDefinition | fluent methods | High | Pending |
| TC-005 | ModuleDefinition | getSqlCreate() | High | Pending |
| TC-006 | CodeGenerator | generateFAExtensionClass() | High | Pending |
| TC-007 | CodeGenerator | generateHookFile() | Medium | Pending |
| TC-008 | CodeGenerator | generateFormView() | Medium | Pending |
| TC-009 | ModuleDefinition | toArray/fromArray | Low | Pending |
| TC-010 | ModuleDefinition | mapType() | High | Pending |

---

**Document Owner**: KS Fraser Development Team  
**Review Status**: Pending