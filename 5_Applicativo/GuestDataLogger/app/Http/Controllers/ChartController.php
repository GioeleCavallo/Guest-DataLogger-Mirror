<?php
 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
 
class ChartController extends Controller
{

    /**
     * This function returns the chart view.
     * 
     * @return View 
     */
    public function index()
    {
        return view('chart');
    }

}