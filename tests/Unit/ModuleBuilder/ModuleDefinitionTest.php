<?php

declare(strict_types=1);

namespace Ksfraser\Tests\Unit\ModuleBuilder;

use Ksfraser\ModuleBuilder\FieldDefinition;
use Ksfraser\ModuleBuilder\ModuleDefinition;
use PHPUnit\Framework\TestCase;

class ModuleDefinitionTest extends TestCase
{
    public function testConstructorSetsNameAndLabel(): void
    {
        $module = new ModuleDefinition('Customer', 'Customer Management');

        $this->assertEquals('Customer', $module->name);
        $this->assertEquals('Customer Management', $module->label);
    }

    public function testConstructorSetsDefaultTableName(): void
    {
        $module = new ModuleDefinition('Customer', 'Customer Management');

        $this->assertEquals('fa_customer', $module->table);
    }

    public function testConstructorSetsDefaultValues(): void
    {
        $module = new ModuleDefinition('Test', 'Test Module');

        $this->assertEquals('fa_', $module->prefix);
        $this->assertEquals('', $module->description);
        $this->assertEquals('1.0.0', $module->version);
        $this->assertFalse($module->has_attachments);
        $this->assertFalse($module->has_comments);
        $this->assertFalse($module->has_workflow);
        $this->assertFalse($module->has_revision);
        $this->assertEquals([], $module->fields);
        $this->assertEquals([], $module->relations);
        $this->assertEquals([], $module->permissions);
        $this->assertEquals([], $module->workflow_triggers);
        $this->assertEquals([], $module->hooks);
        $this->assertNull($module->parent_module);
    }

    public function testCreateFactoryMethod(): void
    {
        $module = ModuleDefinition::create('Product', 'Product Catalog');

        $this->assertInstanceOf(ModuleDefinition::class, $module);
        $this->assertEquals('Product', $module->name);
        $this->assertEquals('Product Catalog', $module->label);
    }

    public function testTableMethodSetsTableAndReturnsSelf(): void
    {
        $module = new ModuleDefinition('Order', 'Orders');
        $result = $module->table('custom_orders');

        $this->assertEquals('custom_orders', $module->table);
        $this->assertSame($module, $result);
    }

    public function testPrefixMethodSetsPrefixAndReturnsSelf(): void
    {
        $module = new ModuleDefinition('Item', 'Items');
        $result = $module->prefix('ksf_');

        $this->assertEquals('ksf_', $module->prefix);
        $this->assertSame($module, $result);
    }

    public function testPrefixAffectsTableName(): void
    {
        $module = new ModuleDefinition('Invoice', 'Invoices');
        $module->prefix('ksf_inv_');
        $module->table('ksf_inv_invoice');

        $this->assertEquals('ksf_inv_invoice', $module->table);
    }

    public function testDescriptionMethodSetsDescriptionAndReturnsSelf(): void
    {
        $module = new ModuleDefinition('Customer', 'Customers');
        $result = $module->description('Manages customer data');

        $this->assertEquals('Manages customer data', $module->description);
        $this->assertSame($module, $result);
    }

    public function testVersionMethodSetsVersionAndReturnsSelf(): void
    {
        $module = new ModuleDefinition('Test', 'Test');
        $result = $module->version('2.0.0');

        $this->assertEquals('2.0.0', $module->version);
        $this->assertSame($module, $result);
    }

    public function testHasAttachmentsMethodSetsFlagAndReturnsSelf(): void
    {
        $module = new ModuleDefinition('Document', 'Documents');
        $result = $module->hasAttachments();

        $this->assertTrue($module->has_attachments);
        $this->assertSame($module, $result);
    }

    public function testHasCommentsMethodSetsFlagAndReturnsSelf(): void
    {
        $module = new ModuleDefinition('Post', 'Posts');
        $result = $module->hasComments();

        $this->assertTrue($module->has_comments);
        $this->assertSame($module, $result);
    }

    public function testHasWorkflowMethodSetsFlagAndReturnsSelf(): void
    {
        $module = new ModuleDefinition('Task', 'Tasks');
        $result = $module->hasWorkflow();

        $this->assertTrue($module->has_workflow);
        $this->assertSame($module, $result);
    }

    public function testHasRevisionMethodSetsFlagAndReturnsSelf(): void
    {
        $module = new ModuleDefinition('Article', 'Articles');
        $result = $module->hasRevision();

        $this->assertTrue($module->has_revision);
        $this->assertSame($module, $result);
    }

    public function testAddFieldReturnsSelf(): void
    {
        $module = new ModuleDefinition('Customer', 'Customers');
        $field = new FieldDefinition('name', 'varchar');
        $result = $module->addField($field);

        $this->assertCount(1, $module->fields);
        $this->assertSame($module, $result);
    }

    public function testAddRelationReturnsSelf(): void
    {
        $module = new ModuleDefinition('Order', 'Orders');
        $result = $module->addRelation(['table' => 'order_items', 'type' => 'one_to_many']);

        $this->assertCount(1, $module->relations);
        $this->assertSame($module, $result);
    }

    public function testAddPermissionReturnsSelf(): void
    {
        $module = new ModuleDefinition('Report', 'Reports');
        $result = $module->addPermission(['role' => 'admin', 'access' => 'full']);

        $this->assertCount(1, $module->permissions);
        $this->assertSame($module, $result);
    }

    public function testAddWorkflowTriggerReturnsSelf(): void
    {
        $module = new ModuleDefinition('Task', 'Tasks');
        $result = $module->addWorkflowTrigger(['event' => 'on_complete', 'action' => 'notify']);

        $this->assertCount(1, $module->workflow_triggers);
        $this->assertSame($module, $result);
    }

    public function testAddHookReturnsSelf(): void
    {
        $module = new ModuleDefinition('Payment', 'Payments');
        $result = $module->addHook(['name' => 'payment_completed', 'callback' => 'handlePayment']);

        $this->assertCount(1, $module->hooks);
        $this->assertSame($module, $result);
    }

    public function testParentModuleReturnsSelf(): void
    {
        $module = new ModuleDefinition('OrderItem', 'Order Items');
        $result = $module->parentModule('Order');

        $this->assertEquals('Order', $module->parent_module);
        $this->assertSame($module, $result);
    }

    public function testGetSqlCreateReturnsString(): void
    {
        $module = new ModuleDefinition('Customer', 'Customers');
        $module->addField(FieldDefinition::make('name', 'varchar')->label('Name')->required());
        $module->addField(FieldDefinition::make('email', 'varchar')->label('Email')->length(255));

        $sql = $module->getSqlCreate();

        $this->assertIsString($sql);
        $this->assertStringContainsString('CREATE TABLE', $sql);
        $this->assertStringContainsString('fa_customer', $sql);
    }

    public function testGetSqlCreateIncludesFields(): void
    {
        $module = new ModuleDefinition('Product', 'Products');
        $module->addField(FieldDefinition::make('name', 'varchar')->length(100));

        $sql = $module->getSqlCreate();

        $this->assertStringContainsString('name', $sql);
        $this->assertStringContainsString('VARCHAR(100)', $sql);
    }

    public function testGetSqlCreateIncludesTimestampFields(): void
    {
        $module = new ModuleDefinition('Event', 'Events');

        $sql = $module->getSqlCreate();

        $this->assertStringContainsString('created_at', $sql);
        $this->assertStringContainsString('updated_at', $sql);
    }

    public function testGetSqlCreateWithAttachments(): void
    {
        $module = new ModuleDefinition('Document', 'Documents');
        $module->hasAttachments();

        $sql = $module->getSqlCreate();

        $this->assertStringContainsString('file_path', $sql);
        $this->assertStringContainsString('file_name', $sql);
    }

    public function testGetSqlCreateWithComments(): void
    {
        $module = new ModuleDefinition('Post', 'Posts');
        $module->hasComments();

        $sql = $module->getSqlCreate();

        $this->assertStringContainsString('comment_count', $sql);
    }

    public function testGetSqlCreateWithRevision(): void
    {
        $module = new ModuleDefinition('Article', 'Articles');
        $module->hasRevision();

        $sql = $module->getSqlCreate();

        $this->assertStringContainsString('revision', $sql);
    }

    public function testMapTypeConvertsVariousTypes(): void
    {
        $module = new ModuleDefinition('Test', 'Test');

        $reflection = new \ReflectionClass($module);
        $method = $reflection->getMethod('mapType');
        $method->setAccessible(true);

        $this->assertEquals('VARCHAR', $method->invoke($module, 'varchar'));
        $this->assertEquals('TEXT', $method->invoke($module, 'text'));
        $this->assertEquals('INT', $method->invoke($module, 'int'));
        $this->assertEquals('INT', $method->invoke($module, 'integer'));
        $this->assertEquals('BIGINT', $method->invoke($module, 'bigint'));
        $this->assertEquals('DECIMAL(10,2)', $method->invoke($module, 'decimal'));
        $this->assertEquals('FLOAT', $method->invoke($module, 'float'));
        $this->assertEquals('TINYINT(1)', $method->invoke($module, 'boolean'));
        $this->assertEquals('DATE', $method->invoke($module, 'date'));
        $this->assertEquals('DATETIME', $method->invoke($module, 'datetime'));
        $this->assertEquals('JSON', $method->invoke($module, 'json'));
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $module = new ModuleDefinition('Customer', 'Customers');
        $module->addField(FieldDefinition::make('name', 'varchar'));

        $array = $module->toArray();

        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('table', $array);
        $this->assertArrayHasKey('prefix', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('version', $array);
        $this->assertArrayHasKey('has_attachments', $array);
        $this->assertArrayHasKey('has_comments', $array);
        $this->assertArrayHasKey('has_workflow', $array);
        $this->assertArrayHasKey('has_revision', $array);
        $this->assertArrayHasKey('fields', $array);
        $this->assertArrayHasKey('relations', $array);
        $this->assertArrayHasKey('permissions', $array);
        $this->assertArrayHasKey('workflow_triggers', $array);
        $this->assertArrayHasKey('hooks', $array);
        $this->assertArrayHasKey('parent_module', $array);

        $this->assertEquals('Customer', $array['name']);
        $this->assertCount(1, $array['fields']);
    }

    public function testFromArrayCreatesModuleWithAllProperties(): void
    {
        $data = [
            'name' => 'Order',
            'label' => 'Order Management',
            'table' => 'fa_orders',
            'prefix' => 'fa_',
            'description' => 'Manages orders',
            'version' => '1.5.0',
            'has_attachments' => true,
            'has_comments' => false,
            'has_workflow' => true,
            'has_revision' => false,
            'fields' => [
                ['name' => 'order_date', 'type' => 'date', 'label' => 'Order Date'],
            ],
            'relations' => [['table' => 'order_items']],
            'permissions' => [['role' => 'admin']],
            'workflow_triggers' => [['event' => 'on_create']],
            'hooks' => [['name' => 'order_created']],
            'parent_module' => 'Sales',
        ];

        $module = ModuleDefinition::fromArray($data);

        $this->assertEquals('Order', $module->name);
        $this->assertEquals('Order Management', $module->label);
        $this->assertEquals('fa_orders', $module->table);
        $this->assertEquals('1.5.0', $module->version);
        $this->assertTrue($module->has_attachments);
        $this->assertFalse($module->has_comments);
        $this->assertTrue($module->has_workflow);
        $this->assertFalse($module->has_revision);
        $this->assertCount(1, $module->fields);
        $this->assertEquals('order_date', $module->fields[0]->name);
        $this->assertCount(1, $module->relations);
        $this->assertCount(1, $module->permissions);
        $this->assertCount(1, $module->workflow_triggers);
        $this->assertCount(1, $module->hooks);
        $this->assertEquals('Sales', $module->parent_module);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'name' => 'Simple',
            'label' => 'Simple Module',
        ];

        $module = ModuleDefinition::fromArray($data);

        $this->assertEquals('Simple', $module->name);
        $this->assertEquals('Simple Module', $module->label);
        $this->assertEquals('fa_simple', $module->table);
        $this->assertEquals('1.0.0', $module->version);
        $this->assertEquals([], $module->fields);
    }

    public function testCompleteFluentInterface(): void
    {
        $module = ModuleDefinition::create('Invoice', 'Invoice Management')
            ->table('custom_invoices')
            ->prefix('fin_')
            ->description('Financial invoice tracking')
            ->version('3.0.0')
            ->hasAttachments()
            ->hasComments()
            ->hasWorkflow()
            ->hasRevision()
            ->addField(FieldDefinition::make('amount', 'decimal'))
            ->addRelation(['table' => 'invoice_items'])
            ->addPermission(['role' => 'accountant'])
            ->addWorkflowTrigger(['event' => 'on_approve'])
            ->addHook(['name' => 'invoice_paid'])
            ->parentModule('Billing');

        $this->assertEquals('Invoice', $module->name);
        $this->assertEquals('custom_invoices', $module->table);
        $this->assertEquals('fin_', $module->prefix);
        $this->assertEquals('3.0.0', $module->version);
        $this->assertTrue($module->has_attachments);
        $this->assertTrue($module->has_comments);
        $this->assertTrue($module->has_workflow);
        $this->assertTrue($module->has_revision);
        $this->assertCount(1, $module->fields);
        $this->assertCount(1, $module->relations);
        $this->assertCount(1, $module->permissions);
        $this->assertCount(1, $module->workflow_triggers);
        $this->assertCount(1, $module->hooks);
        $this->assertEquals('Billing', $module->parent_module);
    }
}