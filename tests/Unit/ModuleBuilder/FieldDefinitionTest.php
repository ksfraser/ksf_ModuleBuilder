<?php

declare(strict_types=1);

namespace Ksfraser\Tests\Unit\ModuleBuilder;

use Ksfraser\ModuleBuilder\FieldDefinition;
use PHPUnit\Framework\TestCase;

class FieldDefinitionTest extends TestCase
{
    public function testConstructorWithNameAndType(): void
    {
        $field = new FieldDefinition('email', 'varchar');

        $this->assertEquals('email', $field->name);
        $this->assertEquals('varchar', $field->type);
    }

    public function testConstructorWithDefaults(): void
    {
        $field = new FieldDefinition('title');

        $this->assertEquals('title', $field->name);
        $this->assertEquals('varchar', $field->type);
        $this->assertNull($field->label);
        $this->assertFalse($field->required);
        $this->assertNull($field->default);
        $this->assertNull($field->length);
        $this->assertEquals([], $field->options);
        $this->assertFalse($field->unique);
        $this->assertFalse($field->indexed);
        $this->assertNull($field->validation);
    }

    public function testMakeFactoryMethod(): void
    {
        $field = FieldDefinition::make('username', 'varchar');

        $this->assertInstanceOf(FieldDefinition::class, $field);
        $this->assertEquals('username', $field->name);
        $this->assertEquals('varchar', $field->type);
    }

    public function testLabelMethodSetsLabelAndReturnsSelf(): void
    {
        $field = new FieldDefinition('email');
        $result = $field->label('Email Address');

        $this->assertEquals('Email Address', $field->label);
        $this->assertSame($field, $result);
    }

    public function testRequiredMethodSetsRequiredAndReturnsSelf(): void
    {
        $field = new FieldDefinition('name');
        $result = $field->required(true);

        $this->assertTrue($field->required);
        $this->assertSame($field, $result);
    }

    public function testRequiredMethodDefaultsToTrue(): void
    {
        $field = new FieldDefinition('name');
        $field->required();

        $this->assertTrue($field->required);
    }

    public function testDefaultMethodSetsDefaultAndReturnsSelf(): void
    {
        $field = new FieldDefinition('status');
        $result = $field->default('pending');

        $this->assertEquals('pending', $field->default);
        $this->assertSame($field, $result);
    }

    public function testDefaultMethodAcceptsNumericValues(): void
    {
        $field = new FieldDefinition('quantity');
        $field->default(0);

        $this->assertEquals(0, $field->default);
    }

    public function testDefaultMethodAcceptsBooleanValues(): void
    {
        $field = new FieldDefinition('active');
        $field->default(true);

        $this->assertEquals('1', (string)$field->default);
    }

    public function testLengthMethodSetsLengthAndReturnsSelf(): void
    {
        $field = new FieldDefinition('email');
        $result = $field->length(255);

        $this->assertEquals(255, $field->length);
        $this->assertSame($field, $result);
    }

    public function testOptionsMethodSetsOptionsAndReturnsSelf(): void
    {
        $field = new FieldDefinition('status');
        $options = ['active', 'inactive', 'pending'];
        $result = $field->options($options);

        $this->assertEquals($options, $field->options);
        $this->assertSame($field, $result);
    }

    public function testUniqueMethodSetsUniqueAndReturnsSelf(): void
    {
        $field = new FieldDefinition('email');
        $result = $field->unique(true);

        $this->assertTrue($field->unique);
        $this->assertSame($field, $result);
    }

    public function testUniqueMethodDefaultsToTrue(): void
    {
        $field = new FieldDefinition('email');
        $field->unique();

        $this->assertTrue($field->unique);
    }

    public function testIndexedMethodSetsIndexedAndReturnsSelf(): void
    {
        $field = new FieldDefinition('category');
        $result = $field->indexed(true);

        $this->assertTrue($field->indexed);
        $this->assertSame($field, $result);
    }

    public function testIndexedMethodDefaultsToTrue(): void
    {
        $field = new FieldDefinition('category');
        $field->indexed();

        $this->assertTrue($field->indexed);
    }

    public function testValidationMethodSetsValidationAndReturnsSelf(): void
    {
        $field = new FieldDefinition('email');
        $result = $field->validation('email');

        $this->assertEquals('email', $field->validation);
        $this->assertSame($field, $result);
    }

    public function testFluentInterface(): void
    {
        $field = FieldDefinition::make('test', 'varchar')
            ->label('Test Field')
            ->required()
            ->default('default_value')
            ->length(100)
            ->options(['opt1', 'opt2'])
            ->unique()
            ->indexed()
            ->validation('required');

        $this->assertEquals('test', $field->name);
        $this->assertEquals('varchar', $field->type);
        $this->assertEquals('Test Field', $field->label);
        $this->assertTrue($field->required);
        $this->assertEquals('default_value', $field->default);
        $this->assertEquals(100, $field->length);
        $this->assertEquals(['opt1', 'opt2'], $field->options);
        $this->assertTrue($field->unique);
        $this->assertTrue($field->indexed);
        $this->assertEquals('required', $field->validation);
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $field = FieldDefinition::make('username', 'varchar')
            ->label('Username')
            ->required()
            ->length(50)
            ->unique();

        $array = $field->toArray();

        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertArrayHasKey('required', $array);
        $this->assertArrayHasKey('default', $array);
        $this->assertArrayHasKey('length', $array);
        $this->assertArrayHasKey('options', $array);
        $this->assertArrayHasKey('unique', $array);
        $this->assertArrayHasKey('indexed', $array);
        $this->assertArrayHasKey('validation', $array);

        $this->assertEquals('username', $array['name']);
        $this->assertEquals('varchar', $array['type']);
        $this->assertEquals('Username', $array['label']);
        $this->assertTrue($array['required']);
        $this->assertEquals(50, $array['length']);
        $this->assertTrue($array['unique']);
    }

    public function testFromArrayCreatesFieldWithAllProperties(): void
    {
        $data = [
            'name' => 'email',
            'type' => 'varchar',
            'label' => 'Email Address',
            'required' => true,
            'default' => 'test@example.com',
            'length' => 255,
            'options' => [],
            'unique' => true,
            'indexed' => true,
            'validation' => 'email',
        ];

        $field = FieldDefinition::fromArray($data);

        $this->assertEquals('email', $field->name);
        $this->assertEquals('varchar', $field->type);
        $this->assertEquals('Email Address', $field->label);
        $this->assertTrue($field->required);
        $this->assertEquals('test@example.com', $field->default);
        $this->assertEquals(255, $field->length);
        $this->assertTrue($field->unique);
        $this->assertTrue($field->indexed);
        $this->assertEquals('email', $field->validation);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'name' => 'simple_field',
        ];

        $field = FieldDefinition::fromArray($data);

        $this->assertEquals('simple_field', $field->name);
        $this->assertEquals('varchar', $field->type);
        $this->assertNull($field->label);
        $this->assertFalse($field->required);
        $this->assertNull($field->default);
        $this->assertEquals([], $field->options);
        $this->assertFalse($field->unique);
        $this->assertFalse($field->indexed);
        $this->assertNull($field->validation);
    }

    public function testToArrayAndFromArrayAreReversible(): void
    {
        $original = FieldDefinition::make('test_field', 'int')
            ->label('Test Integer')
            ->required()
            ->default(0)
            ->indexed();

        $array = $original->toArray();
        $restored = FieldDefinition::fromArray($array);

        $this->assertEquals($original->name, $restored->name);
        $this->assertEquals($original->type, $restored->type);
        $this->assertEquals($original->label, $restored->label);
        $this->assertEquals($original->required, $restored->required);
        $this->assertEquals($original->default, $restored->default);
        $this->assertEquals($original->indexed, $restored->indexed);
    }

    public function testVariousFieldTypes(): void
    {
        $types = ['varchar', 'text', 'int', 'bigint', 'decimal', 'float', 'boolean', 'date', 'datetime', 'json'];

        foreach ($types as $type) {
            $field = new FieldDefinition('test_' . $type, $type);
            $this->assertEquals($type, $field->type);
        }
    }

    public function testOptionsWithKeyValuePairs(): void
    {
        $field = new FieldDefinition('country', 'enum');
        $options = [
            'US' => 'United States',
            'CA' => 'Canada',
            'UK' => 'United Kingdom',
        ];
        $field->options($options);

        $this->assertCount(3, $field->options);
        $this->assertEquals('United States', $field->options['US']);
    }
}