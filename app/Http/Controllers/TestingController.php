<?php

namespace App\Http\Controllers;

use App\Services\PMBService;
use Illuminate\Http\Request;

class TestingController extends Controller
{
    public function index(){
        $foto = PMBService::foto("3514140207070005", "S1");
        dd($foto);
    }
}
