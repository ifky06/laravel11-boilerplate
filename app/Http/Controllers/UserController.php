<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Contracts\DataTable;

class UserController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
//            new Middleware('permission:user-index', only:['index', 'data']),
            new Middleware('permission:user-add', only:['store']),
            new Middleware('permission:user-edit', only:['edit', 'update']),
            new Middleware('permission:user-delete', only:['destroy']),
        ];
    }

    public function index()
    {
        return view('user');
    }

    public function data(){
        $data = User::with('roles')->chunkMap(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->first()->name,
            ];
        });
        return datatables()->of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function store(Request $request)
    {
        $rules=[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', $validator->errors()->first());
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];
        User::create($data);
        return redirect('users')
            ->with('success', 'Data Added Successfully');
    }

    public function edit($id)
    {
        $data = User::find($id);
        $roles = Role::all();

        $data->role = $data->roles->first()->name;

        return response()->json(
            [
                'data' => $data,
                'roles' => $roles,
            ]
        );
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);
        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if($request->password){
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return redirect('users')
            ->with('success', 'Data Updated Successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect('users')
            ->with('success', 'Data Deleted Successfully');
    }

}
