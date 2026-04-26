<?php

namespace Ksfraser\ModuleBuilder;

class CodeGenerator
{
    private ModuleDefinition $module;

    public function __construct(ModuleDefinition $module)
    {
        $this->module = $module;
    }

    public static function forModule(ModuleDefinition $module): self
    {
        return new self($module);
    }

    public function generateInstallationSql(): string
    {
        $sql = $this->module->getSqlCreate();
        $sql .= ";\n\n";

        foreach ($this->module->relations as $relation) {
            $sql .= $this->generateRelationSql($relation) . ";\n\n";
        }

        $sql .= $this->generateAccessSql();

        return $sql;
    }

    public function generateUninstallationSql(): string
    {
        $sql = "";

        foreach ($this->module->relations as $relation) {
            if (isset($relation['delete_cascade']) && $relation['delete_cascade']) {
                $sql .= "DROP TABLE IF EXISTS " . TB_PREF . $relation['table'] . ";\n";
            }
        }

        $sql .= "DROP TABLE IF EXISTS " . TB_PREF . $this->module->table . ";";

        return $sql;
    }

    public function generateAccessSql(): string
    {
        $access_table = $this->module->table . "_access";
        $sql = "CREATE TABLE IF NOT EXISTS " . TB_PREF . $access_table . " (
            id INT AUTO_INCREMENT PRIMARY KEY,
            role_id INT,
            user_id INT,
            can_view INT DEFAULT 0,
            can_edit INT DEFAULT 0,
            can_delete INT DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n";

        if (!empty($this->module->permissions)) {
            $sql .= "\nINSERT INTO " . TB_PREF . $access_table . " (role_id, can_view, can_edit, can_delete) VALUES
";
            $perm_rows = [];
            foreach ($this->module->permissions as $perm) {
                $role_id = $perm['role'] ?? 1;
                $perm_rows[] = "($role_id, 1, 1, 1)";
            }
            $sql .= implode(",\n", $perm_rows) . ";";
        }

        return $sql;
    }

    public function generateFAExtensionClass(): string
    {
        $name = $this->module->name;
        $className = "FA{$name}";
        $table = $this->module->table;
        $label = $this->module->label;

        return <<<PHP
<?php

class {$className}
{
    public static \$table = '{$table}';
    public static \$prefix = '{$this->module->prefix}';
    public static \$module_name = '{$name}';

    public static function install()
    {
        \$sql = file_get_contents(__DIR__ . '/sql/install.sql');
        foreach (explode(';', \$sql) as \$statement) {
            if (trim(\$statement)) {
                db_query(\$statement);
            }
        }
    }

    public static function uninstall()
    {
        \$sql = "DROP TABLE IF EXISTS " . TB_PREF . static::\$table;
        db_query(\$sql);
    }

    public static function get_access()
    {
        return db_fetch_assoc(db_query(
            "SELECT * FROM " . TB_PREF . static::\$table . "_access 
            WHERE user_id = " . get_current_user()
        ));
    }

    public static function can_view()
    {
        \$access = static::get_access();
        return \$access && \$access['can_view'];
    }

    public static function can_edit()
    {
        \$access = static::get_access();
        return \$access && \$access['can_edit'];
    }

    public static function can_delete()
    {
        \$access = static::get_access();
        return \$access && \$access['can_delete'];
    }

    public static function add(\$data)
    {
        \$fields = [];
        \$values = [];

        foreach (\$data as \$key => \$value) {
            \$fields[] = \$key;
            \$values[] = db_escape(\$value);
        }

        \$fields[] = 'created_by';
        \$values[] = db_escape(get_current_user());

        \$sql = "INSERT INTO " . TB_PREF . static::\$table . "
            (" . implode(', ', \$fields) . ")
            VALUES (" . implode(', ', \$values) . ")";

        db_query(\$sql);
        return db_insert_id();
    }

    public static function update(\$id, \$data)
    {
        \$updates = [];
        foreach (\$data as \$key => \$value) {
            \$updates[] = \$key . " = " . db_escape(\$value);
        }

        \$updates[] = 'updated_by = ' . db_escape(get_current_user());

        \$sql = "UPDATE " . TB_PREF . static::\$table . "
            SET " . implode(', ', \$updates) . "
            WHERE id = " . db_escape(\$id);

        db_query(\$sql);
    }

    public static function delete(\$id)
    {
        \$sql = "UPDATE " . TB_PREF . static::\$table . "
            SET active = 0
            WHERE id = " . db_escape(\$id);

        db_query(\$sql);
    }

    public static function get(\$id)
    {
        \$sql = "SELECT * FROM " . TB_PREF . static::\$table . "
            WHERE id = " . db_escape(\$id) . " AND active = 1";

        return db_fetch_assoc(db_query(\$sql));
    }

    public static function get_all(\$filter = [])
    {
        \$sql = "SELECT * FROM " . TB_PREF . static::\$table . " WHERE active = 1";

        if (!empty(\$filter)) {
            \$conditions = [];
            foreach (\$filter as \$key => \$value) {
                \$conditions[] = \$key . " = " . db_escape(\$value);
            }
            \$sql .= " AND " . implode(' AND ', \$conditions);
        }

        \$sql .= " ORDER BY created_at DESC";

        \$result = db_query(\$sql);
        \$items = [];
        while (\$row = db_fetch_assoc(\$result)) {
            \$items[] = \$row;
        }

        return \$items;
    }

    public static function add_attachment(\$id, \$file_path, \$file_name)
    {
        \$sql = "UPDATE " . TB_PREF . static::\$table . "
            SET file_path = " . db_escape(\$file_path) . ",
                file_name = " . db_escape(\$file_name) . "
            WHERE id = " . db_escape(\$id);

        db_query(\$sql);
    }

    public static function get_attachments(\$id)
    {
        \$sql = "SELECT * FROM " . TB_PREF . static::\$table . "_attachments
            WHERE parent_id = " . db_escape(\$id) . " AND active = 1";

        \$result = db_query(\$sql);
        \$attachments = [];
        while (\$row = db_fetch_assoc(\$result)) {
            \$attachments[] = \$row;
        }

        return \$attachments;
    }

    public static function list_view(\$params)
    {
        \$table = static::\$table;
        \$name = static::\$module_name;

        return [
            'table' => \$table,
            'title' => '{$label}',
            'fields' => [
                'id' => ['type' => 'hidden'],
                'created_at' => ['type' => 'date', 'label' => 'Date'],
            ],
            'operations' => [
                'view' => true,
                'edit' => true,
                'delete' => false,
            ],
        ];
    }
}
PHP;
    }

    public function generateHookFile(): string
    {
        $name = $this->module->name;
        $className = "FA{$name}";

        $hooks_code = "";
        foreach ($this->module->hooks as $hook) {
            $hook_name = $hook['name'];
            $hooks_code .= <<<PHP

add_hook('{$hook_name}', function(\$params) use (\$className) {
    return {$className}::hook_{\$params['action']}(\$params);
});

PHP;
        }

        return <<<PHP
<?php

add_menu_entry('{$name}', '{$this->module->label}', '{$name}');

function ksf_fa_{$name}_install()
{
    {$className}::install();
}

function ksf_fa_{$name}_uninstall()
{
    {$className}::uninstall();
}

{$hooks_code}
PHP;
    }

    public function generateFormView(): string
    {
        $fields_html = "";
        foreach ($this->module->fields as $field) {
            $name = $field->name;
            $label = $field->label ?? ucfirst($name);
            $type = $field->type;

            if ($type === 'enum') {
                $options = json_encode($field->options);
                $fields_html .= <<<HTML
<div class="form-group">
    <label>{$label}</label>
    <select name="{$name}" class="form-control">
        <option value="">Select...</option>
        <option value=""></option>
    </select>
</div>

HTML;
            } elseif ($type === 'text') {
                $fields_html .= <<<HTML
<div class="form-group">
    <label>{$label}</label>
    <textarea name="{$name}" class="form-control" rows="4"></textarea>
</div>

HTML;
            } else {
                $input_type = in_array($type, ['date', 'datetime']) ? $type : 'text';
                $fields_html .= <<<HTML
<div class="form-group">
    <label>{$label}</label>
    <input type="{$input_type}" name="{$name}" class="form-control">
</div>

HTML;
            }
        }

        return <<<PHP
<div class="panel panel-default">
    <div class="panel-heading">
        <h3>{$this->module->label}</h3>
    </div>
    <div class="panel-body">
        <form method="post" action="" id="{$name}_form">
            <input type="hidden" name="added_by" value="<?php echo get_current_user(); ?>">
            <input type="hidden" name="proc" value="{$name}_form">
            <input type="hidden" name="form_id" value="{$name}_form">

            {$fields_html}

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-default">Reset</button>
            </div>
        </form>
    </div>
</div>
PHP;
    }

    public function generateJsonConfig(): string
    {
        return json_encode($this->module->toArray(), JSON_PRETTY_PRINT);
    }

    private function generateRelationSql(array $relation): string
    {
        $table = $relation['table'];
        $columns = [];

        foreach ($relation['columns'] as $col) {
            $columns[] = "{$col['name']} {$col['type']}";
        }

        return "CREATE TABLE IF NOT EXISTS " . TB_PREF . $table . " (
            " . implode(",\n", $columns) . "
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    }
}