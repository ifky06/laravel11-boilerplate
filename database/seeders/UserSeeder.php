<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);

        $modelNames = ['user','role'];
        $actions = ['index', 'add', 'edit', 'delete'];

        foreach ($modelNames as $modelName) {
            foreach ($actions as $action) {
                Permission::create(['name' => $modelName . '-' . $action]);
                Role::findByName('admin')->givePermissionTo($modelName . '-' . $action);
            }
        }

        User::create([
            'name' => 'Test User',
            'email' => 'test@example',
            'password' => Hash::make('password'),
        ])->assignRole('admin');
    }
}
