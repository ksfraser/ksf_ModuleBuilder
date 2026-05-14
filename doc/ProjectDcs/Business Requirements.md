# ModuleBuilder - Business Requirements

**Document ID:** BR-MODULEB-001  
**Module:** ksf_ModuleBuilder  
**Version:** 1.0.0  

---

## 1. Overview

ModuleBuilder provides a code generation framework for creating FrontAccounting modules. It defines module structures, field definitions, and generates the SQL and PHP code required for new module development.

## 2. Purpose

The module accelerates FrontAccounting module development by providing reusable code generation patterns, standardized module structures, and consistent database schema definitions.

## 3. Scope

### 3.1 Core Features

- **Module Definition**
  - Define module metadata (name, label, description, version)
  - Configure table prefix
  - Define field collections
  - Configure relations and permissions

- **Field Definition**
  - Support for all common field types
  - Validation rules (required, unique, default)
  - Indexing options
  - Type mapping to database columns

- **Code Generation**
  - Generate CREATE TABLE SQL
  - Generate entity classes
  - Generate repository interfaces
  - Generate workflow triggers

- **Optional Features**
  - Attachments support
  - Comments system
  - Workflow triggers
  - Revision tracking

### 3.2 Out of Scope

- UI generation
- Migration scripts
- Installation scripts
- Unit test generation

## 4. Integration Dependencies

| Module | Dependency Type | Purpose |
|--------|-----------------|---------|
| FrontAccounting Core | Required | TB_PREF constant |
| ksf_ModulesDAO | Optional | Persistence patterns |

## 5. User Roles

| Role | Permissions |
|------|-------------|
| Developer | Generate module code |
| Module Architect | Design module structures |

## 6. Acceptance Criteria

- [ ] ModuleDefinition creates valid module structure
- [ ] FieldDefinition supports all field types
- [ ] Generated SQL is valid MySQL syntax
- [ ] Module can be serialized/deserialized
- [ ] All optional features generate correctly