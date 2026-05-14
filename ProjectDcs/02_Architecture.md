# Module Builder Module - Architecture

## Document Information

| Field | Value |
|-------|-------|
| Document Title | Technical Architecture Specification |
| Module | ksf_ModuleBuilder |
| Version | 1.0.0 |
| Author | KSF Development Team |
| Last Updated | May 2026 |

---

## 1. Architecture Overview

### 1.1 Design Philosophy

ModuleBuilder follows the KSF pattern itself:
- Pure business logic in `src/Ksfraser/ModuleBuilder/`
- Framework-agnostic code generation
- Extensible template system

### 1.2 Module Structure

```
ksf_ModuleBuilder/
├── src/Ksfraser/ModuleBuilder/
│   ├── CodeGenerator.php      # Main generator
│   ├── ModuleDefinition.php   # Module metadata
│   └── FieldDefinition.php     # Field schema
├── templates/                   # Code templates
└── tests/
```

---

## 2. Core Classes

### 2.1 ModuleDefinition

```php
namespace Ksfraser\ModuleBuilder;

class ModuleDefinition {
    
    private string $name;
    private string $namespace;
    private string $type;
    private array $dependencies = [];
    private array $entities = [];
    private array $services = [];
    private array $repositories = [];
    
    public function setName(string $name): self;
    public function setNamespace(string $namespace): self;
    public function setType(string $type): self; // Business Logic, UI, FA, WP
    public function addDependency(string $module): self;
    public function addEntity(string $name, array $fields = []): self;
    public function addService(string $name): self;
    public function addRepository(string $name, string $entity): self;
    public function toArray(): array;
}
```

### 2.2 FieldDefinition

```php
namespace Ksfraser\ModuleBuilder;

class FieldDefinition {
    
    private string $name;
    private string $type;
    private bool $required = false;
    private ?int $maxLength = null;
    private ?array $options = null; // For enum
    private bool $unique = false;
    
    public function __construct(string $name, string $type);
    public function setRequired(bool $required): self;
    public function setMaxLength(int $length): self;
    public function setOptions(array $options): self;
    public function setUnique(bool $unique): self;
}
```

### 2.3 CodeGenerator

```php
namespace Ksfraser\ModuleBuilder;

class CodeGenerator {
    
    public function __construct(string $outputPath);
    
    public function generate(ModuleDefinition $definition): void;
    public function generateComposerJson(ModuleDefinition $definition): string;
    public function generateEntity(string $name, array $fields): string;
    public function generateService(string $name, string $entity): string;
    public function generateTest(string $className): string;
    public function generateDocumentation(ModuleDefinition $definition): void;
    public function generatePhpunitConfig(): string;
}
```

---

## 3. Template System

### 3.1 Template Variables

Templates use `{{variable}}` syntax:

| Variable | Source |
|----------|--------|
| `{{namespace}}` | ModuleDefinition |
| `{{className}}` | Passed to generator |
| `{{fields}}` | Array of FieldDefinition |
| `{{dependencies}}` | ModuleDefinition |

### 3.2 Template Types

| Type | Extension | Purpose |
|------|-----------|---------|
| PHP Entity | `.php.entity` | Entity class template |
| PHP Service | `.php.service` | Service class template |
| PHP Test | `.php.test` | PHPUnit test template |
| Markdown | `.md` | Documentation templates |

---

## 4. Generated Module Structure

### 4.1 Business Logic Module

```
Generated Module/
├── composer.json          # With ksfraser/* dependencies
├── phpunit.xml           # PHPUnit 9/10 config
├── src/
│   └── Ksfraser/
│       └── [ModuleName]/
│           ├── Entity/
│           │   └── [Entity].php
│           ├── Service/
│           │   └── [Service].php
│           └── Events/
│               └── [Event].php
├── tests/
│   ├── Unit/
│   │   └── [Class]Test.php
│   └── bootstrap.php
└── ProjectDcs/
    ├── 01_Business_Requirements.md
    ├── 02_Architecture.md
    ├── 03_Functional_Requirements.md
    ├── 04_Use_Case.md
    ├── 05_Test_Plan.md
    └── 06_UAT_Plan.md
```

---

## 5. Integration

### 5.1 Dependencies

Generated modules include:
- `ksfraser/traits` ^1.0
- `ksfraser/exceptions` ^1.0
- `psr/event-dispatcher` ^1.0

---

## 6. Revision History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0.0 | May 2026 | KSF Development Team | Initial specification |