<?php
 
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    
    /**
     * This function returns the login view.
     * 
     * @return View 
     */    
    public function index()
    {
        return view('login');
    }

    
    /**
     * @param String $usr it's the username to check.
     * @param String $psq it's the plain text password to check.
     * 
     * @return Boolean 
     */    
    private function rightLogin($usr, $psw){
        $users = DB::table('users')
                ->where('username', $usr)
                ->get();
                 
        foreach ($users as $user) {
            if (Hash::check($psw, $user->password)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Request $request it's the post request sended from the login form. 
     * 
     * @return View 
     */ 
    public function checkLogin(Request $request)
    {
        $input = $request->all();
 
        $request->validate([
            'username' => 'required|max:50',
            'password' => 'required'
        ]);
        if (isset($request->validator) && $request->validator->fails()) {
            return back();
        }
        if($this::rightLogin($request->username, $request->password)){
            return view('admin');
        }
        return back()->with("failed", "login failed");
    }
}