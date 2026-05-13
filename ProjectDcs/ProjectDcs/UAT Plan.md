# ksf_ModuleBuilder - UAT Plan

## Document Information

| Field | Value |
|-------|-------|
| **Document ID** | UAT-MODULE-001 |
| **Module** | ksf_ModuleBuilder |
| **Project** | Module Builder/Generator |
| **Version** | 1.0.0 |
| **Author** | KS Fraser Development Team |
| **Created** | 2024-01-15 |

---

## 1. UAT Scenarios

### 1.1 Scenario MBU-01: Create Customer Module

**Scenario ID**: MBU-01  
**Priority**: Critical

#### Scenario
Developer creates a complete Customer module with fields.

#### Test Steps
1. Create module: `ModuleDefinition::create('Customer', 'Customer Management')`
2. Add fields:
   - name (varchar, required)
   - email (email, unique)
   - phone (phone)
   - status (enum: active, inactive)
   - notes (text)
3. Enable attachments and comments
4. Generate SQL installation script
5. Generate PHP extension class
6. Verify all outputs are valid

#### Pass Criteria
- [ ] Module definition complete
- [ ] SQL valid and executable
- [ ] PHP class syntactically correct

---

### 1.2 Scenario MBU-02: Generate All Code

**Scenario ID**: MBU-02  
**Priority**: High

#### Scenario
Generate complete module structure.

#### Test Steps
1. Define complete module with 10+ fields
2. Generate:
   - SQL install script
   - SQL uninstall script
   - Extension class
   - Hook file
   - Form view
   - JSON config
3. Verify each output
4. Write files to test directory
5. Run PHP syntax check on generated files

#### Pass Criteria
- [ ] All outputs generated
- [ ] No syntax errors
- [ ] Code follows conventions

---

### 1.3 Scenario MBU-03: Round-Trip Configuration

**Scenario ID**: MBU-03  
**Priority**: Medium

#### Scenario
Export module configuration and reimport.

#### Test Steps
1. Create complex module
2. Export to JSON via `toArray()`
3. Store JSON
4. Clear module
5. Reimport from JSON via `fromArray()`
6. Verify imported module matches original

#### Pass Criteria
- [ ] JSON export valid
- [ ] JSON import works
- [ ] Data preserved exactly

---

## 2. Success Criteria

| Criterion | Target | Weight |
|-----------|--------|--------|
| Critical scenarios pass | 100% | 50% |
| High priority scenarios pass | 100% | 30% |
| No critical defects | 0 | 20% |

**Pass Threshold**: 95%

---

## 3. Sign-Off

| Role | Name | Signature | Date |
|------|------|-----------|------|
| Developer Lead | | | |
| QA Lead | | | |

---

**Document Owner**: KS Fraser Development Team  
**Status**: Ready for UAT