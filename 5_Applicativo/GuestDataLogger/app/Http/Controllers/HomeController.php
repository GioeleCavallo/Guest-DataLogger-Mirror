<?php
 
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
 
class HomeController extends Controller
{

    /**
     * This function returns the home view.
     * 
     * @return View 
     */    
    public function index()
    {
        return view('home');
    }
    
}