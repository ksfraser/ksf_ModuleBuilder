<?php

namespace Ksfraser\ModuleBuilder;

use InvalidArgumentException;

if (!defined('TB_PREF')) {
    define('TB_PREF', '');
}

class ModuleDefinition
{
    public string $name;
    public string $table;
    public string $prefix = 'fa_';
    public string $label;
    public string $description = '';
    public string $version = '1.0.0';
    public bool $has_attachments = false;
    public bool $has_comments = false;
    public bool $has_workflow = false;
    public bool $has_revision = false;
    public array $fields = [];
    public array $relations = [];
    public array $permissions = [];
    public array $workflow_triggers = [];
    public array $hooks = [];
    public ?string $parent_module = null;

    public function __construct(string $name, string $label)
    {
        $this->name = $name;
        $this->label = $label;
        $this->table = $this->prefix . strtolower($name);
    }

    public static function create(string $name, string $label): self
    {
        return new self($name, $label);
    }

    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function prefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function version(string $version): self
    {
        $this->version = $version;
        return $this;
    }

    public function hasAttachments(bool $has = true): self
    {
        $this->has_attachments = $has;
        return $this;
    }

    public function hasComments(bool $has = true): self
    {
        $this->has_comments = $has;
        return $this;
    }

    public function hasWorkflow(bool $has = true): self
    {
        $this->has_workflow = $has;
        return $this;
    }

    public function hasRevision(bool $has = true): self
    {
        $this->has_revision = $has;
        return $this;
    }

    public function addField(FieldDefinition $field): self
    {
        $this->fields[] = $field;
        return $this;
    }

    public function addRelation(array $relation): self
    {
        $this->relations[] = $relation;
        return $this;
    }

    public function addPermission(array $permission): self
    {
        $this->permissions[] = $permission;
        return $this;
    }

    public function addWorkflowTrigger(array $trigger): self
    {
        $this->workflow_triggers[] = $trigger;
        return $this;
    }

    public function addHook(array $hook): self
    {
        $this->hooks[] = $hook;
        return $this;
    }

    public function parentModule(string $module): self
    {
        $this->parent_module = $module;
        return $this;
    }

    public function getSqlCreate(): string
    {
        $sql = "CREATE TABLE IF NOT EXISTS " . TB_PREF . $this->table . " (\n";
        $sql .= "    id INT AUTO_INCREMENT PRIMARY KEY,\n";

        foreach ($this->fields as $field) {
            $sql .= "    " . $field->name . " " . $this->mapType($field->type);

            if ($field->length && in_array($field->type, ['varchar', 'char'])) {
                $sql .= "(" . $field->length . ")";
            }

            if ($field->default !== null) {
                $sql .= " DEFAULT " . (is_numeric($field->default) ? $field->default : "'" . $field->default . "'");
            } elseif ($field->required) {
                $sql .= " NOT NULL";
            }

            if ($field->unique) {
                $sql .= " UNIQUE";
            }

            $sql .= ",\n";
        }

        if ($this->has_attachments) {
            $sql .= "    file_path VARCHAR(255),\n";
            $sql .= "    file_name VARCHAR(255),\n";
        }

        if ($this->has_comments) {
            $sql .= "    comment_count INT DEFAULT 0,\n";
        }

        if ($this->has_revision) {
            $sql .= "    revision INT DEFAULT 0,\n";
        }

        $sql .= "    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,\n";
        $sql .= "    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n";
        $sql .= "    created_by VARCHAR(50),\n";
        $sql .= "    updated_by VARCHAR(50),\n";
        $sql .= "    active INT DEFAULT 1\n";
        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        foreach ($this->fields as $field) {
            if ($field->indexed && !$field->unique) {
                $sql .= ";\nCREATE INDEX idx_" . $field->name . " ON " . TB_PREF . $this->table . "(" . $field->name . ")";
            }
        }

        return $sql;
    }

    private function mapType(string $type): string
    {
        if (in_array($type, ['int', 'integer'])) {
            return 'INT';
        }
        if (in_array($type, ['boolean', 'bool'])) {
            return 'TINYINT(1)';
        }

        switch ($type) {
            case 'varchar':
                return 'VARCHAR';
            case 'text':
                return 'TEXT';
            case 'bigint':
                return 'BIGINT';
            case 'decimal':
                return 'DECIMAL(10,2)';
            case 'float':
                return 'FLOAT';
            case 'date':
                return 'DATE';
            case 'datetime':
                return 'DATETIME';
            case 'timestamp':
                return 'TIMESTAMP';
            case 'email':
                return 'VARCHAR(255)';
            case 'phone':
                return 'VARCHAR(50)';
            case 'url':
                return 'VARCHAR(500)';
            case 'json':
                return 'JSON';
            case 'file':
                return 'VARCHAR(500)';
            case 'enum':
                return 'ENUM';
            default:
                return 'VARCHAR(255)';
        }
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'table' => $this->table,
            'prefix' => $this->prefix,
            'label' => $this->label,
            'description' => $this->description,
            'version' => $this->version,
            'has_attachments' => $this->has_attachments,
            'has_comments' => $this->has_comments,
            'has_workflow' => $this->has_workflow,
            'has_revision' => $this->has_revision,
            'fields' => array_map(function ($f) { return $f->toArray(); }, $this->fields),
            'relations' => $this->relations,
            'permissions' => $this->permissions,
            'workflow_triggers' => $this->workflow_triggers,
            'hooks' => $this->hooks,
            'parent_module' => $this->parent_module,
        ];
    }

    public static function fromArray(array $data): self
    {
        $module = new self($data['name'], $data['label']);
        $module->table = $data['table'] ?? $module->table;
        $module->prefix = $data['prefix'] ?? 'fa_';
        $module->description = $data['description'] ?? '';
        $module->version = $data['version'] ?? '1.0.0';
        $module->has_attachments = $data['has_attachments'] ?? false;
        $module->has_comments = $data['has_comments'] ?? false;
        $module->has_workflow = $data['has_workflow'] ?? false;
        $module->has_revision = $data['has_revision'] ?? false;

        if (isset($data['fields'])) {
            foreach ($data['fields'] as $fieldData) {
                $module->fields[] = FieldDefinition::fromArray($fieldData);
            }
        }

        $module->relations = $data['relations'] ?? [];
        $module->permissions = $data['permissions'] ?? [];
        $module->workflow_triggers = $data['workflow_triggers'] ?? [];
        $module->hooks = $data['hooks'] ?? [];
        $module->parent_module = $data['parent_module'] ?? null;

        return $module;
    }
}