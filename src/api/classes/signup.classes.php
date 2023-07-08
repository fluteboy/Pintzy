<?php

require "../../../startup.php";

use App\Foundation;
use App\Models\PintzyUserInfo;

class Signup
{

    protected function setUser($name, $email, $pass)
    {

        $user = new PintzyUserInfo();
        $user->user_name = $name;
        $user->user_email = $email;
        $user->user_password = PintzyUserInfo::hashPassword($pass);
        $user->save();
        
        session_start();

        //on success
        $_SESSION['signupSuccessful'] = true;
        $_SESSION['userName'] = $name;
    }

    protected function checkUser($name, $email)
    {
        $user = PintzyUserInfo::fetchByUserNameAndEmail($name, $email);

        return $user !== null;
    }

}
