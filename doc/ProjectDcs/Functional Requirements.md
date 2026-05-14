# ModuleBuilder - Functional Requirements

**Document ID:** FR-MODULEB-001  
**Module:** ksf_ModuleBuilder  
**Version:** 1.0.0  

---

## 1. Functional Requirements

### 1.1 Module Definition

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-001 | System SHALL create ModuleDefinition with name and label | MUST |
| FR-002 | System SHALL generate table name from prefix and name | MUST |
| FR-003 | System SHALL support custom table prefix | MUST |
| FR-004 | System SHALL support module description | MUST |
| FR-005 | System SHALL support module versioning | MUST |

### 1.2 Field Management

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-010 | System SHALL add FieldDefinition to module | MUST |
| FR-011 | System SHALL map field types to database types | MUST |
| FR-012 | System SHALL support field lengths for varchar | MUST |
| FR-013 | System SHALL apply default values | MUST |
| FR-014 | System SHALL enforce NOT NULL on required fields | MUST |
| FR-015 | System SHALL create UNIQUE indexes when specified | MUST |

### 1.3 SQL Generation

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-020 | System SHALL generate valid CREATE TABLE statement | MUST |
| FR-021 | System SHALL include audit fields (created_at, updated_at, created_by) | MUST |
| FR-022 | System SHALL create indexes for indexed fields | MUST |
| FR-023 | System SHALL generate JSON columns when type is json | MUST |
| FR-024 | System SHALL set InnoDB engine with utf8mb4 | MUST |

### 1.4 Feature Support

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-030 | System SHALL add attachment fields when hasAttachments enabled | MUST |
| FR-031 | System SHALL add comment_count when hasComments enabled | MUST |
| FR-032 | System SHALL add revision when hasRevision enabled | MUST |
| FR-033 | System SHALL support workflow trigger configuration | SHOULD |
| FR-034 | System SHALL support hook configuration | SHOULD |

### 1.5 Serialization

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-040 | System SHALL serialize ModuleDefinition to array | MUST |
| FR-041 | System SHALL deserialize ModuleDefinition from array | MUST |
| FR-042 | Serialized data SHALL include all fields and relations | MUST |

## 2. Example Usage

```php
$module = ModuleDefinition::create('Customer', 'Customer Management')
    ->description('Customer relationship management')
    ->version('1.0.0')
    ->addField(FieldDefinition::create('name', 'varchar')->required()->length(255))
    ->addField(FieldDefinition::create('email', 'email')->required()->unique())
    ->addField(FieldDefinition::create('phone', 'phone')->length(50))
    ->hasAttachments()
    ->hasComments();

$sql = $module->getSqlCreate();
```