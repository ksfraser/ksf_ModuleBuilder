# ModuleBuilder - Architecture

**Document ID:** ARCH-MODULEB-001  
**Module:** ksf_ModuleBuilder  
**Version:** 1.0.0  

---

## 1. Module Overview

ModuleBuilder implements a fluent builder pattern for defining FrontAccounting module structures with code generation capabilities.

## 2. Class Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                  ModuleDefinition                          │
├─────────────────────────────────────────────────────────────┤
│ - name: string                                              │
│ - table: string                                             │
│ - prefix: string                                            │
│ - label: string                                             │
│ - description: string                                       │
│ - version: string                                           │
│ - fields: FieldDefinition[]                                 │
│ - relations: array[]                                        │
│ - permissions: array[]                                     │
│ - workflow_triggers: array[]                               │
│ - hooks: array[]                                            │
├─────────────────────────────────────────────────────────────┤
│ + create(name, label): ModuleDefinition                     │
│ + addField(field): self                                     │
│ + addRelation(config): self                                │
│ + hasAttachments(bool): self                                │
│ + hasWorkflow(bool): self                                  │
│ + getSqlCreate(): string                                    │
│ + toArray(): array                                         │
│ + static fromArray(data): ModuleDefinition                 │
└─────────────────────────────────────────────────────────────┘
                              │
                              │ contains
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                  FieldDefinition                           │
├─────────────────────────────────────────────────────────────┤
│ - name: string                                              │
│ - type: string                                              │
│ - label: string                                             │
│ - required: bool                                            │
│ - unique: bool                                              │
│ - default: mixed                                            │
│ - length: int                                               │
│ - indexed: bool                                             │
├─────────────────────────────────────────────────────────────┤
│ + static create(name, type): FieldDefinition                │
│ + label(str): self                                         │
│ + required(bool): self                                      │
│ + unique(bool): self                                        │
│ + default(val): self                                       │
│ + toArray(): array                                          │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                  CodeGenerator                              │
├─────────────────────────────────────────────────────────────┤
│ - module: ModuleDefinition                                  │
├─────────────────────────────────────────────────────────────┤
│ + generateEntity(): string                                  │
│ + generateRepository(): string                             │
│ + generateSql(): string                                     │
└─────────────────────────────────────────────────────────────┘
```

## 3. Directory Structure

```
ksf_ModuleBuilder/
├── src/Ksfraser/ModuleBuilder/
│   ├── ModuleDefinition.php
│   ├── FieldDefinition.php
│   └── CodeGenerator.php
├── tests/
│   └── Unit/
│       ├── ModuleDefinitionTest.php
│       ├── FieldDefinitionTest.php
│       └── CodeGeneratorTest.php
└── doc/ProjectDcs/
```

## 4. Supported Field Types

| Type | Database Mapping |
|------|-----------------|
| varchar | VARCHAR(length) |
| text | TEXT |
| int | INT |
| bigint | BIGINT |
| decimal | DECIMAL(10,2) |
| float | FLOAT |
| boolean | TINYINT(1) |
| date | DATE |
| datetime | DATETIME |
| timestamp | TIMESTAMP |
| email | VARCHAR(255) |
| phone | VARCHAR(50) |
| url | VARCHAR(500) |
| json | JSON |
| file | VARCHAR(500) |
| enum | ENUM |

## 5. Technology Stack

| Component | Technology |
|-----------|------------|
| Language | PHP 7.3+ |
| Pattern | Builder, Factory |
| Testing | PHPUnit |