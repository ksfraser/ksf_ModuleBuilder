<?php

namespace Ksfraser\ModuleBuilder;

use InvalidArgumentException;

class FieldDefinition
{
    public string $name;
    public string $type;
    public ?string $label = null;
    public bool $required = false;
    public ?string $default = null;
    public ?int $length = null;
    public array $options = [];
    public bool $unique = false;
    public bool $indexed = false;
    public ?string $validation = null;

    public function __construct(string $name, string $type = 'varchar')
    {
        $this->name = $name;
        $this->type = $type;
    }

    public static function make(string $name, string $type = 'varchar'): self
    {
        return new self($name, $type);
    }

    public function label(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function required(bool $required = true): self
    {
        $this->required = $required;
        return $this;
    }

    public function default($default): self
    {
        $this->default = $default;
        return $this;
    }

    public function length(int $length): self
    {
        $this->length = $length;
        return $this;
    }

    public function options(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    public function unique(bool $unique = true): self
    {
        $this->unique = $unique;
        return $this;
    }

    public function indexed(bool $indexed = true): self
    {
        $this->indexed = $indexed;
        return $this;
    }

    public function validation(string $validation): self
    {
        $this->validation = $validation;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'label' => $this->label,
            'required' => $this->required,
            'default' => $this->default,
            'length' => $this->length,
            'options' => $this->options,
            'unique' => $this->unique,
            'indexed' => $this->indexed,
            'validation' => $this->validation,
        ];
    }

    public static function fromArray(array $data): self
    {
        $field = new self($data['name'], $data['type'] ?? 'varchar');
        $field->label = $data['label'] ?? null;
        $field->required = $data['required'] ?? false;
        $field->default = $data['default'] ?? null;
        $field->length = $data['length'] ?? null;
        $field->options = $data['options'] ?? [];
        $field->unique = $data['unique'] ?? false;
        $field->indexed = $data['indexed'] ?? false;
        $field->validation = $data['validation'] ?? null;
        return $field;
    }
}