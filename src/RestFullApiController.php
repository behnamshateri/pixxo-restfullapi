<?php

namespace Pixxo\RestFullApi;

use App\Http\Controllers\Controller;

class RestFullApiController extends Controller
{
    public function index(){
        return view("RestFullApi::index");
    }
}
