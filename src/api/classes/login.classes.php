<?php

require "../../../startup.php";

use App\Foundation;
use App\Models\PintzyUserInfo;

class Login
{

    protected $loginData;
    private $errorMsg = '';
    
    protected function getUser($userName, $password)
    {
        $user = PintzyUserInfo::fetchByUserNameAndEmail($userName, $userName);
        if (!isset($user)) {
            $this->errorHandler("User Not Found");
        }

        if (!$user->verifyPassword($password)) {
            $this->errorHandler( "Wrong Password.");
        }
        
        session_start();

        $_SESSION["id"] = $user->primaryKey();
        $_SESSION["userName"] = $user->user_name;
        
        Foundation::db()->closeConnection();
    }

    protected function errorHandler(string $msg)
    {
        $this->errorMsg = "$msg";
        header("location:../../../index.php?loginError=$this->errorMsg");
        exit();
        

    }
   

}
