<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DropAllTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:drop-all-tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all tables in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 删除所有视图
        $this->components->info('Dropping all views...');
        $views = DB::select('SHOW FULL TABLES WHERE Table_Type = "VIEW"');

        if (empty($views)) {
            $this->line('No views found.');
        } else {
            foreach ($views as $view) {
                $viewName = array_values((array)$view)[0];
                DB::statement('DROP VIEW IF EXISTS ' . $viewName);
                $this->line(str_pad($viewName, 60, '.') . ' DONE');
            }
        }
        $this->components->success('All views dropped successfully!');

        // 关闭外键检查
        $this->components->info('Dropping all tables...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 获取并删除所有表
        $tables = DB::select('SHOW TABLES');

        if (empty($tables)) {
            $this->line('No tables found.');
        } else {
            foreach ($tables as $table) {
                $tableName = array_values((array)$table)[0];
                DB::statement('DROP TABLE IF EXISTS ' . $tableName);
                $this->line(str_pad($tableName, 60, '.') . ' DONE');
            }
        }

        // 重新开启外键检查
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->components->success('All tables dropped successfully!');

        return 0;
    }
}
