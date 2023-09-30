<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Support\Facades\Config;

use App\Http\Requests\LoginRequest;
use App\Http\Helpers\Helper;
use Auth;
use App\Http\Resources\UserResource;
use App\Models\Company;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{ 
    public function store(Request $request)
    {

        $user_admin = auth()->user();
        $subDomainDB = Company::where('user_id', $user_admin->id)->first();
        $db = $subDomainDB->comp_database;

        $this->connectDynamicDatabase($db);

        // dd($dbName);
        // Validate the request data
        /* $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'required|string',
        ]); */
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Hash::make($request->password),
        ]);

        $token = $user->createToken('Token')->accessToken;
        
        // assign role.
        $user_role = Role::where(['name'=>'user'])->first();
        if(empty($user_role)){
            $user_role = Role::create(['name'=>'user']);            
        }        
        $user->assignRole($user_role);
       
        return response()->json(['message' => 'User created successfully', 'token'=>$token, 'user'=>$user], 200);
        
    }

    public function connectDynamicDatabase($dbName)
    {   
        
        // Generate a new database configuration dynamically
        $config = [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $dbName,
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ];

        // Set the new database configuration
        Config::set('database.connections.dynamic', $config);
        Config::set('database.default', 'dynamic');

    }

    public function login(Request $request)
    {
        $dbName = config('database.connections.mysql.database');
        $this->connectDynamicDatabase($dbName);
        // php artisan passport:install --database=$tenantConnectionName

        // return User::all();

        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if(!Auth::attempt($data)){
            Helper::sendError('Email or Password is wrong');
        }
        return new UserResource(auth()->user());

        /* if( !Auth::attempt($data) ){            
            $token =  auth()->user()->createToken('Token')->accessToken;
            return response()->json(['token'=>$token], 200);
        }else{
            return response()->json(['error'=>'unauthrized'], 401);
            // Helper::sendError('Email or Password is wrong');
        } */
        
    }

    public function userInfo(){
        // $this->connectDynamicDatabase($dbName='tenant15');

        $user = auth()->user();
        // return response()->json(['user'=>$user], 200);
        return new UserResource($user);
    }



}
