<?php
namespace App\Http\Controller\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Methods\DbactionsController;

class UserController extends Controller
{

    private $dbactions;

    public function __construct()
    {
        $this->dbactions = new DbactionsController();
    }


    public function postlogin(){
        
    }



}

