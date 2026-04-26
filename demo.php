<?php

spl_autoload_register(function ($class) {
    $prefix = 'Ksfraser\\ModuleBuilder\\';
    $base_dir = __DIR__ . '/src/Ksfraser/ModuleBuilder/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

use Ksfraser\ModuleBuilder\ModuleDefinition;
use Ksfraser\ModuleBuilder\FieldDefinition;
use Ksfraser\ModuleBuilder\CodeGenerator;

$module = ModuleDefinition::create('Asset', 'Assets')
    ->description('Track company assets')
    ->hasAttachments()
    ->hasWorkflow()
    ->addField(FieldDefinition::make('name', 'varchar')->label('Asset Name')->required()->indexed())
    ->addField(FieldDefinition::make('serial_number', 'varchar')->label('Serial Number')->unique())
    ->addField(FieldDefinition::make('category', 'enum')->label('Category')->options(['IT', 'Furniture', 'Vehicle', 'Equipment']))
    ->addField(FieldDefinition::make('location', 'varchar')->label('Location')->indexed())
    ->addField(FieldDefinition::make('purchase_date', 'date')->label('Purchase Date'))
    ->addField(FieldDefinition::make('purchase_price', 'decimal')->label('Purchase Price'))
    ->addField(FieldDefinition::make('status', 'enum')->label('Status')->options(['Active', 'In Repair', 'Disposed'])->default('Active'))
    ->addField(FieldDefinition::make('assigned_to', 'varchar')->label('Assigned To'))
    ->addRelation([
        'table' => 'fa_asset_maintenance',
        'columns' => [
            ['name' => 'id', 'type' => 'INT AUTO_INCREMENT PRIMARY KEY'],
            ['name' => 'asset_id', 'type' => 'INT'],
            ['name' => 'maintenance_date', 'type' => 'DATE'],
            ['name' => 'description', 'type' => 'TEXT'],
            ['name' => 'cost', 'type' => 'DECIMAL(10,2)'],
        ],
        'foreign_key' => 'asset_id',
        'delete_cascade' => true,
    ])
    ->addPermission(['role' => 1, 'view' => 1, 'edit' => 1, 'delete' => 1])
    ->addWorkflowTrigger(['event' => 'after_insert', 'action' => 'notify'])
    ->addHook(['name' => 'activate_TB', 'action' => 'activate']);

$generator = CodeGenerator::forModule($module);

echo "=== Installation SQL ===\n";
echo $generator->generateInstallationSql() . "\n\n";

echo "=== Extension Class ===\n";
echo $generator->generateFAExtensionClass() . "\n\n";

echo "=== Hook File ===\n";
echo $generator->generateHookFile() . "\n\n";

echo "=== JSON Config ===\n";
echo $generator->generateJsonConfig() . "\n\n";