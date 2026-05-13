<?php

declare(strict_types=1);

namespace Ksfraser\Tests\Unit\ModuleBuilder;

use Ksfraser\ModuleBuilder\CodeGenerator;
use Ksfraser\ModuleBuilder\FieldDefinition;
use Ksfraser\ModuleBuilder\ModuleDefinition;
use PHPUnit\Framework\TestCase;

class CodeGeneratorTest extends TestCase
{
    private CodeGenerator $generator;
    private ModuleDefinition $module;

    protected function setUp(): void
    {
        $this->module = new ModuleDefinition('Customer', 'Customer Management');
        $this->generator = new CodeGenerator($this->module);
    }

    public function testConstructorAcceptsModuleDefinition(): void
    {
        $generator = new CodeGenerator($this->module);

        $this->assertInstanceOf(CodeGenerator::class, $generator);
    }

    public function testForModuleFactoryMethod(): void
    {
        $generator = CodeGenerator::forModule($this->module);

        $this->assertInstanceOf(CodeGenerator::class, $generator);
    }

    public function testGenerateInstallationSqlReturnsString(): void
    {
        $sql = $this->generator->generateInstallationSql();

        $this->assertIsString($sql);
        $this->assertStringContainsString('CREATE TABLE', $sql);
    }

    public function testGenerateInstallationSqlContainsModuleTable(): void
    {
        $sql = $this->generator->generateInstallationSql();

        $this->assertStringContainsString('fa_customer', $sql);
    }

    public function testGenerateInstallationSqlIncludesAccessTable(): void
    {
        $sql = $this->generator->generateInstallationSql();

        $this->assertStringContainsString('_access', $sql);
    }

    public function testGenerateUninstallationSqlReturnsString(): void
    {
        $sql = $this->generator->generateUninstallationSql();

        $this->assertIsString($sql);
        $this->assertStringContainsString('DROP TABLE', $sql);
    }

    public function testGenerateUninstallationSqlDropsModuleTable(): void
    {
        $sql = $this->generator->generateUninstallationSql();

        $this->assertStringContainsString('fa_customer', $sql);
    }

    public function testGenerateAccessSqlReturnsString(): void
    {
        $sql = $this->generator->generateAccessSql();

        $this->assertIsString($sql);
        $this->assertStringContainsString('CREATE TABLE', $sql);
        $this->assertStringContainsString('_access', $sql);
    }

    public function testGenerateAccessSqlContainsPermissionFields(): void
    {
        $sql = $this->generator->generateAccessSql();

        $this->assertStringContainsString('can_view', $sql);
        $this->assertStringContainsString('can_edit', $sql);
        $this->assertStringContainsString('can_delete', $sql);
    }

    public function testGenerateAccessSqlWithPermissionsInsertsDefaults(): void
    {
        $this->module->addPermission(['role' => 1]);
        $this->module->addPermission(['role' => 2]);

        $sql = $this->generator->generateAccessSql();

        $this->assertStringContainsString('INSERT INTO', $sql);
        $this->assertStringContainsString('(1, 1, 1, 1)', $sql);
    }

    public function testGenerateFAExtensionClassReturnsString(): void
    {
        $code = $this->generator->generateFAExtensionClass();

        $this->assertIsString($code);
        $this->assertStringContainsString('<?php', $code);
        $this->assertStringContainsString('class FACustomer', $code);
    }

    public function testGenerateFAExtensionClassContainsTableConstant(): void
    {
        $code = $this->generator->generateFAExtensionClass();

        $this->assertStringContainsString("public static \$table = 'fa_customer'", $code);
    }

    public function testGenerateFAExtensionClassContainsModuleName(): void
    {
        $code = $this->generator->generateFAExtensionClass();

        $this->assertStringContainsString("public static \$module_name = 'Customer'", $code);
    }

    public function testGenerateFAExtensionClassContainsInstallMethod(): void
    {
        $code = $this->generator->generateFAExtensionClass();

        $this->assertStringContainsString('public static function install()', $code);
        $this->assertStringContainsString('db_query', $code);
    }

    public function testGenerateFAExtensionClassContainsUninstallMethod(): void
    {
        $code = $this->generator->generateFAExtensionClass();

        $this->assertStringContainsString('public static function uninstall()', $code);
    }

    public function testGenerateFAExtensionClassContainsCRUDMethods(): void
    {
        $code = $this->generator->generateFAExtensionClass();

        $this->assertStringContainsString('public static function add(', $code);
        $this->assertStringContainsString('public static function update(', $code);
        $this->assertStringContainsString('public static function delete(', $code);
        $this->assertStringContainsString('public static function get(', $code);
        $this->assertStringContainsString('public static function get_all(', $code);
    }

    public function testGenerateFAExtensionClassContainsAccessMethods(): void
    {
        $code = $this->generator->generateFAExtensionClass();

        $this->assertStringContainsString('public static function can_view()', $code);
        $this->assertStringContainsString('public static function can_edit()', $code);
        $this->assertStringContainsString('public static function can_delete()', $code);
    }

    public function testGenerateFAExtensionClassContainsAttachmentMethods(): void
    {
        $code = $this->generator->generateFAExtensionClass();

        $this->assertStringContainsString('public static function add_attachment(', $code);
        $this->assertStringContainsString('public static function get_attachments(', $code);
    }

    public function testGenerateFAExtensionClassContainsListView(): void
    {
        $code = $this->generator->generateFAExtensionClass();

        $this->assertStringContainsString('public static function list_view(', $code);
        $this->assertStringContainsString("'title' => 'Customer Management'", $code);
    }

    public function testGenerateHookFileReturnsString(): void
    {
        $code = $this->generator->generateHookFile();

        $this->assertIsString($code);
        $this->assertStringContainsString('<?php', $code);
    }

    public function testGenerateHookFileContainsInstallFunction(): void
    {
        $code = $this->generator->generateHookFile();

        $this->assertStringContainsString('function ksf_fa_customer_install()', $code);
        $this->assertStringContainsString('FACustomer::install()', $code);
    }

    public function testGenerateHookFileContainsUninstallFunction(): void
    {
        $code = $this->generator->generateHookFile();

        $this->assertStringContainsString('function ksf_fa_customer_uninstall()', $code);
        $this->assertStringContainsString('FACustomer::uninstall()', $code);
    }

    public function testGenerateHookFileContainsMenuEntry(): void
    {
        $code = $this->generator->generateHookFile();

        $this->assertStringContainsString("add_menu_entry('Customer'", $code);
    }

    public function testGenerateHookFileWithCustomHooks(): void
    {
        $this->module->addHook(['name' => 'customer_created']);
        $this->module->addHook(['name' => 'customer_updated']);

        $code = $this->generator->generateHookFile();

        $this->assertStringContainsString("add_hook('customer_created'", $code);
        $this->assertStringContainsString("add_hook('customer_updated'", $code);
    }

    public function testGenerateFormViewReturnsString(): void
    {
        $html = $this->generator->generateFormView();

        $this->assertIsString($html);
        $this->assertStringContainsString('<form', $html);
        $this->assertStringContainsString('</form>', $html);
    }

    public function testGenerateFormViewContainsModuleLabel(): void
    {
        $html = $this->generator->generateFormView();

        $this->assertStringContainsString('Customer Management', $html);
    }

    public function testGenerateFormViewContainsFormId(): void
    {
        $html = $this->generator->generateFormView();

        $this->assertStringContainsString('customer_form', $html);
    }

    public function testGenerateFormViewContainsCSRFToken(): void
    {
        $html = $this->generator->generateFormView();

        $this->assertStringContainsString('added_by', $html);
    }

    public function testGenerateFormViewWithFields(): void
    {
        $this->module->addField(FieldDefinition::make('name', 'varchar')->label('Name'));
        $this->module->addField(FieldDefinition::make('email', 'varchar')->label('Email'));

        $html = $this->generator->generateFormView();

        $this->assertStringContainsString('name', $html);
        $this->assertStringContainsString('email', $html);
        $this->assertStringContainsString('Name', $html);
        $this->assertStringContainsString('Email', $html);
    }

    public function testGenerateFormViewWithSelectField(): void
    {
        $this->module->addField(
            FieldDefinition::make('status', 'enum')
                ->label('Status')
                ->options(['active', 'inactive'])
        );

        $html = $this->generator->generateFormView();

        $this->assertStringContainsString('<select', $html);
        $this->assertStringContainsString('status', $html);
    }

    public function testGenerateFormViewWithTextAreaField(): void
    {
        $this->module->addField(FieldDefinition::make('notes', 'text')->label('Notes'));

        $html = $this->generator->generateFormView();

        $this->assertStringContainsString('<textarea', $html);
        $this->assertStringContainsString('notes', $html);
    }

    public function testGenerateFormViewWithDateField(): void
    {
        $this->module->addField(FieldDefinition::make('start_date', 'date')->label('Start Date'));

        $html = $this->generator->generateFormView();

        $this->assertStringContainsString('type="date"', $html);
        $this->assertStringContainsString('start_date', $html);
    }

    public function testGenerateJsonConfigReturnsValidJson(): void
    {
        $json = $this->generator->generateJsonConfig();

        $this->assertJson($json);
        $data = json_decode($json, true);
        $this->assertEquals('Customer', $data['name']);
        $this->assertEquals('Customer Management', $data['label']);
    }

    public function testGenerateJsonConfigContainsFields(): void
    {
        $this->module->addField(FieldDefinition::make('name', 'varchar'));

        $json = $this->generator->generateJsonConfig();
        $data = json_decode($json, true);

        $this->assertArrayHasKey('fields', $data);
        $this->assertCount(1, $data['fields']);
    }

    public function testGenerateUninstallationSqlWithCascadeRelations(): void
    {
        $this->module->addRelation(['table' => 'customer_addresses', 'delete_cascade' => true]);

        $sql = $this->generator->generateUninstallationSql();

        $this->assertStringContainsString('customer_addresses', $sql);
    }

    public function testGenerateInstallationSqlWithRelations(): void
    {
        $this->module->addRelation([
            'table' => 'customer_addresses',
            'columns' => [
                ['name' => 'id', 'type' => 'INT'],
                ['name' => 'address', 'type' => 'VARCHAR(255)'],
            ],
        ]);

        $sql = $this->generator->generateInstallationSql();

        $this->assertStringContainsString('customer_addresses', $sql);
        $this->assertStringContainsString('id', $sql);
        $this->assertStringContainsString('address', $sql);
    }

    public function testGenerateInstallationSqlWithFieldDefaults(): void
    {
        $this->module->addField(
            FieldDefinition::make('status', 'varchar')
                ->default('active')
                ->required()
        );

        $sql = $this->generator->generateInstallationSql();

        $this->assertStringContainsString("DEFAULT 'active'", $sql);
    }

    public function testGenerateInstallationSqlWithUniqueField(): void
    {
        $this->module->addField(
            FieldDefinition::make('email', 'varchar')
                ->unique()
        );

        $sql = $this->generator->generateInstallationSql();

        $this->assertStringContainsString('UNIQUE', $sql);
    }
}