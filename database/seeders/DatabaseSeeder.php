<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogCategoryMap;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminSeeder::class);
        $this->call(RolePermissionSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(MenuSeeder::class);
        $this->call(MenuItemSeeder::class);
        $this->call(SectionSeeder::class);
        $this->call(SliderSeeder::class);
        Artisan::call("optimize:clear");
    }
}
