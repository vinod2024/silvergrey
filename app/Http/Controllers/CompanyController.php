<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use Schema;
use Artisan;
use Auth;

use App\Http\Resources\UserResource;


use App\Http\Helpers\Helper;

class CompanyController extends Controller
{
    public function login(LoginRequest $request)
    {
        // $this->connectDatabase($dbName='slivergrey');

        $data = [
                    'email' => $request->email,
                    'password' => $request->password
                ];
        
        if(!Auth::attempt($data)){
            Helper::sendError('Email or Password is wrong');
        }
        return new UserResource(auth()->user());

        /* if( auth()->attempt($data) ){
            // $user = auth()->user();
            // $user->roles = auth()->user()->roles->pluck('name') ?? [];
            // $user->token =  auth()->user()->createToken('Token')->accessToken;
            // return response()->json(['user'=>$user], 200);
            return new UserResource($user);
        }else{
            // return response()->json(['error'=>'unauthrized'], 401); 
            Helper::sendError('Email or Password is wrong'); 
        } */
        
    }

    /**
     * Store 
     */
    public function store(RegisterRequest $request)
    {
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
        
        // Assign role
        $admin_role = Role::where(['name'=>'admin'])->first();
        $user->assignRole($admin_role);

        $request->comp_subdomain = $request->comp_subdomain.'.localhost:8000';
        $company = $this->company($request, $user);
        $token = $user->createToken('Token')->accessToken;

        // new db con.
        $this->createDynamicDatabase($dbName=$request->comp_database);
        $this->dataInserInCompanyDB($request, $user);
        
        return response()->json(['message' => 'Company onboarded successfully', 'token'=>$token, 'user'=>$user, 'company'=>$company], 200);
        
    }

    public function company(Request $request, $user)
    {
        $company = Company::create([
            'comp_name' => $request->comp_name,
            'comp_logo' => 'image.jpg',
            'comp_subdomain' => $request->comp_subdomain,
            'comp_database' => $request->comp_database,
            'comp_details' => $request->comp_details,
            'user_id'   => $user->id,
        ]);

        return $company;
    }

    // 
    public function createDynamicDatabase($dbName)
    {
        // Create a new database
        DB::statement("CREATE DATABASE IF NOT EXISTS $dbName");

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
        
        // Run migrations and seeders for the dynamic database
        // Artisan::call('migrate');
        Artisan::call('migrate:fresh');
        
        
        /* Artisan::call('passport:install', [
            '--database' => $dbName,
        ]); */

    }


    public function dataInserInCompanyDB($request, $user){
        
        // Insert data in user.
        $sub_user = User::create([
            'name' => $user->name,
            'email' => $user->email,
            'password' => \Hash::make($user->password),
        ]);

        // assign role.
        $admin_role = Role::where(['name'=>'admin'])->first();
        if(empty($admin_role)){
            $admin_role = Role::create(['name'=>'admin']);            
        }        
        $sub_user->assignRole($admin_role);
        $this->company($request, $sub_user);

    }

    public function connectDatabase($dbName)
    {
        // Create a new database
        DB::statement("CREATE DATABASE IF NOT EXISTS $dbName");

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

        // Run migrations and seeders for the dynamic database
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');

    }

}
