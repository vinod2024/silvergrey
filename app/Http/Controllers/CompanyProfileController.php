<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Company;
use DB;
use Redirect;


class CompanyProfileController extends Controller
{
    public function index(Request $request){

        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];
        $request->attributes->add(['subdomain' => $subdomain]);
        
        
        if(($subdomain != 'localhost') && ($subdomain != '127')){
            $auth = Auth::user();
            
            $user = User::find($auth->id);
            $company = Company::where('user_id', $auth->id)->first();

            $subdomain = $company->comp_subdomain;

            // Set a test session variable
            session(['user_data_sess' => $auth]);

            $url = "http://$subdomain/home";
            // return redirect()->to($url);

            return Redirect::to($url)->with([
                'auth' => $auth
            ]);

        }

        $auth = Auth::user();
        $user = User::find($auth->id);

        // return redirect()->route('dashboard');
        /* if($auth->role == 'admin'){
            return redirect()->to('http://'.$subdomain.'.localhost:8000/home');
            // return redirect()->to('http://localhost:8000/home');
        }    */   


        // return view('dashboard');

        /* if($auth->role == 'admin'){
            config([
                'database.connections.mysql.host'     => '127.0.0.1',
                'database.connections.mysql.database' => 'newdb',
                'database.connections.mysql.username' => 'root',
                'database.connections.mysql.password' => '',
            ]);
            DB::reconnect();
        } */

        // return $user;

        $userData = User::where('email',$user->email)->first();
        $company = Company::where('user_id', $userData->id)->first();

        return view('dashboard', ['user' => $userData, 'company'=>$company]);

    }


    public function index_old(Request $request){

        $auth = Auth::user();
        $user = User::find($auth->id);
        $company = Company::where('user_id', $auth->id)->first();

        $subdomain = $company->comp_subdomain;
        // return redirect()->route('dashboard');
        if($auth->role == 'admin'){
            return redirect()->to('http://'.$subdomain.'.localhost:8000/home');
            // return redirect()->to('http://localhost:8000/home');
        }      


        // return view('dashboard');

    }

    public function home(Request $request){
        // return $auth = Auth::user();
        $user = User::find($auth->id);
        $company = Company::where('user_id', $auth->id)->first();
        // $user = Auth::user();
        // return view('profile');
        // return view('dashboard');
        // return view('dashboard', ['user' => $user, 'company'=>$company]);

    }

    
}
