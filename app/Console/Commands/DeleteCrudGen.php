<?php

namespace App\Console\Commands;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class DeleteCrudGen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:remove {name : Class (singular)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete A Crud';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function getStub($type)
    {
        return file_get_contents(resource_path("stubs/$type.stub"));
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

    protected function remove($name){
        // Str::plural(strtolower($name))
        unlink(app_path("Models/{$name}.php"));
        unlink(app_path("DataTables/".Str::plural(strtolower($name))."DataTable.php"));
        unlink(app_path("Http/Controllers/{$name}Controller.php"));
        unlink(app_path("Http/Requests/{$name}Request.php"));
        $this->rrmdir(base_path("resources/views/{$name}"));


    }

    protected function rrmdir($dir) {
        if (is_dir($dir)) {
          $objects = scandir($dir);
          foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
              if (filetype($dir."/".$object) == "dir") 
                 $this->rrmdir($dir."/".$object); 
              else unlink   ($dir."/".$object);
            }
          }
          reset($objects);
          rmdir($dir);
        }
       }

    public function handle()
    {
        $name = $this->argument('name');
        $this->remove($name);
    }
}
