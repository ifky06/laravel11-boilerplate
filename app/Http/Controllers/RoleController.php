<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function getRoles()
    {
        $roles = Role::all();
        return response()->json($roles);
    }
}
