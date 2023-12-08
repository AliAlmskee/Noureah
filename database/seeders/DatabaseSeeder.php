<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Truncate all tables to reset the database
        $this->truncateTables();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Call the seeders
        $this->call(BookSeeder::class);
        $this->call(VersionSeeder::class);
        $this->call(FolderSeeder::class);
        $this->call(BranchSeeder::class);
        $this->call(TeacherSeeder::class);
        $this->call(AdminSeeder::class);
    }

    /**
     * Truncate all tables in the database.
     *
     * @return void
     */
    protected function truncateTables(): void
    {
        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {
            $tableName = $table->{'Tables_in_' . env('DB_DATABASE')};
            DB::table($tableName)->truncate();
        }
    }
}
