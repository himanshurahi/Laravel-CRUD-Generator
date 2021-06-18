<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use File;

class crudgen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:generator {name : Class (singular)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
   
    protected function getStub($type)
{
    return file_get_contents(resource_path("stubs/$type.stub"));
}

protected function model($name)
{
    $modelTemplate = str_replace(
        ['{{modelName}}'],
        [$name],
        $this->getStub('Model')
    );

    file_put_contents(app_path("/Models/{$name}.php"), $modelTemplate);
}

protected function controller($name)
{
    $controllerTemplate = str_replace(
        [
            '{{modelName}}',
            '{{modelNamePluralLowerCase}}',
            '{{modelNameSingularLowerCase}}'
        ],
        [
            $name,
            strtolower(Str::plural($name)),
            strtolower($name)
        ],
        $this->getStub('Controller')
    );

    file_put_contents(app_path("/Http/Controllers/{$name}Controller.php"), $controllerTemplate);
}
protected function request($name)
{
    $requestTemplate = str_replace(
        ['{{modelName}}'],
        [$name],
        $this->getStub('Request')
    );

    if(!file_exists($path = app_path('/Http/Requests')))
        mkdir($path, 0777, true);

    file_put_contents(app_path("/Http/Requests/{$name}Request.php"), $requestTemplate);
}

protected function migration($name){
    // date('Y_m_d_His');
    $file_name = date('Y_m_d_His').'_Create_'.Str::plural(strtolower($name)).'_Table'.'.php';
    $migration_file = file_get_contents(base_path('resources/stubs/migrations/migration.stub'));
    $updated_migration_file = str_replace('{{name}}', Str::plural(strtolower($name)), $migration_file);
    file_put_contents(base_path("database/migrations/$file_name"), $updated_migration_file);
    // file_put_contents(base_path('/database/migrations/test.php'), 'data');


}

protected function dataTables($name){
    $DataTable_file = file_get_contents(base_path('resources/stubs/DataTables/DataTable.stub'));
    $updated_DataTable_file = str_replace(['{{name}}', '{{s_name}}'], [Str::plural(strtolower($name)), strtolower($name)], $DataTable_file);
    file_put_contents(app_path("DataTables/".Str::plural(strtolower($name))."DataTable.php"), $updated_DataTable_file);
}

protected function views($name){
    if(!file_exists($path = base_path("resources/views/{$name}")))
        mkdir($path, 0777, true);
   if(!file_exists($path = base_path("resources/views/{$name}/layout")))
       mkdir($path, 0777, true);
    
    
    //layout
    $layout_file = file_get_contents(base_path('resources/stubs/views/layout/layout.blade.stub'));
    $updated_layout_file = str_replace('{{s_name}}', $name, $layout_file);
    file_put_contents(base_path("resources/views/{$name}/layout/layout.blade.php"), $updated_layout_file);
    
    //index
    $index_file = file_get_contents(base_path('resources/stubs/views/index.blade.stub'));
    $updated_index_file = str_replace(['{{name}}', '{{s_name}}'], [Str::plural(strtolower($name)), strtolower($name)], $index_file);
    file_put_contents(base_path("resources/views/{$name}/index.blade.php"), $updated_index_file);

    //create
    $create_file = file_get_contents(base_path('resources/stubs/views/create.blade.stub'));
    $updated_create_file = str_replace(['{{name}}', '{{s_name}}'], [Str::plural(strtolower($name)), strtolower($name)], $create_file);
    file_put_contents(base_path("resources/views/{$name}/create.blade.php"), $updated_create_file);

    //edit
    $edit_file = file_get_contents(base_path('resources/stubs/views/edit.blade.stub'));
    $updated_edit_file = str_replace(['{{name}}', '{{s_name}}'], [Str::plural(strtolower($name)), strtolower($name)], $edit_file);
    file_put_contents(base_path("resources/views/{$name}/edit.blade.php"), $updated_edit_file);

}

public function handle()
{
    $name = $this->argument('name');
    $this->dataTables($name);
    $this->views($name);
    // $this->migration($name);

    $this->controller($name);
    $this->model($name);
    $this->request($name);

// $file_data = $name.Controller::class;
// $file_data .= file_get_contents(base_path('routes/web.php'));
// file_put_contents(base_path('routes/web.php'), $file_data);
// $route = file_get_contents(base_path('routes/web.php'));
// $updated_layout_file = str_replace('<?php', "'<?php \n use App\Http\Controllers\'.$name.'Controller;'", $route);
// file_put_contents(base_path("routes/web.php"), $updated_layout_file);



File::append(base_path('routes/web.php'), 'Route::resource(\'' . Str::plural(strtolower($name)) . "', {$name}Controller::class);\n");
}

}
