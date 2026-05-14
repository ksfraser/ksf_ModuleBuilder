# ModuleBuilder - Use Cases

**Document ID:** UC-MODULEB-001  
**Module:** ksf_ModuleBuilder  
**Version:** 1.0.0  

---

## 1. Use Case Overview

### UC-001: Define New Module

**Description:** Developer creates definition for new FrontAccounting module.

**Primary Flow:**
1. Developer creates ModuleDefinition with name/label
2. Developer adds fields using FieldDefinition
3. Developer configures optional features
4. System generates module definition
5. System generates SQL CREATE statement

### UC-002: Generate Module SQL

**Description:** System generates database schema for module.

**Primary Flow:**
1. Developer defines all fields
2. Developer calls getSqlCreate()
3. System maps field types to database columns
4. System adds audit fields
5. System generates indexes
6. System returns complete CREATE TABLE statement

### UC-003: Serialize Module Definition

**Description:** Developer saves module definition for reuse.

**Primary Flow:**
1. Developer creates ModuleDefinition
2. Developer calls toArray()
3. System serializes all properties
4. Developer saves to file/database
5. Later, Developer calls fromArray() to restore

### UC-004: Configure Advanced Features

**Description:** Developer adds optional module features.

**Primary Flow:**
1. Developer enables attachments
2. Developer enables workflow
3. Developer adds custom hooks
4. System includes feature fields in SQL

## 2. Actors

| Actor | Role |
|-------|------|
| Developer | Creates module definitions |
| ModuleBuilder | Generates code |
| Database | Stores schema |