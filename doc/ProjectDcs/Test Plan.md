# ModuleBuilder - Test Plan

**Document ID:** TP-MODULEB-001  
**Module:** ksf_ModuleBuilder  
**Version:** 1.0.0  

---

## 1. Test Scope

- ModuleDefinition creation
- FieldDefinition support
- SQL generation
- Serialization/deserialization

## 2. Test Cases

### 2.1 ModuleDefinition Tests

| ID | Test | Test Data | Pass Criteria |
|---------|-----------|-----------|---------------|
| TC-001 | testCreate | name='Test', label='Test Module' | Module created |
| TC-002 | testTableName | name='Customer', prefix='fa_' | table='fa_customer' |
| TC-003 | testCustomPrefix | prefix='custom_' | table uses prefix |
| TC-004 | testAddField | valid FieldDefinition | field added |
| TC-005 | testFieldsCount | 3 fields added | count = 3 |

### 2.2 FieldDefinition Tests

| ID | Test | Test Data | Pass Criteria |
|---------|-----------|-----------|---------------|
| TC-010 | testCreate | name='email', type='email' | field created |
| TC-011 | testRequired | required=true | NOT NULL in SQL |
| TC-012 | testUnique | unique=true | UNIQUE in SQL |
| TC-013 | testDefault | default='active' | DEFAULT in SQL |
| TC-014 | testLength | length=255 | VARCHAR(255) |

### 2.3 SQL Generation Tests

| ID | Test | Test Data | Pass Criteria |
|---------|-----------|-----------|---------------|
| TC-020 | testGetSqlCreate_Basic | module with 2 fields | Valid CREATE TABLE |
| TC-021 | testGetSqlCreate_AuditFields | any module | Contains created_at, updated_at |
| TC-022 | testGetSqlCreate_Index | indexed field | CREATE INDEX generated |
| TC-023 | testGetSqlCreate_HasAttachments | hasAttachments=true | Includes file fields |
| TC-024 | testGetSqlCreate_HasComments | hasComments=true | Includes comment_count |
| TC-025 | testGetSqlCreate_HasRevision | hasRevision=true | Includes revision field |

### 2.4 Serialization Tests

| ID | Test | Test Data | Pass Criteria |
|---------|-----------|-----------|---------------|
| TC-030 | testToArray | complete module | All properties in array |
| TC-031 | testFromArray | serialized data | Module reconstructed |
| TC-032 | testRoundTrip | full module | Original === fromArray(toArray()) |