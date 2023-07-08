<?php

class SignupController extends Signup
{

    private $name;
    private $email;
    private $pass;
    private $repass;

    //construct
    public function __construct($name, $email, $pass, $repass)
    {
        $this->name = $name;
        $this->email = $email;
        $this->pass = $pass;
        $this->repass = $repass;
    }

    //throw error

    public function signupUser()
    {

        //empty input
        if ($this->emptyInput() == false) {
            $this->setError("empty input");
        }

        //empty nam
        if($this->nameIsEmpty() == true) {
            $this->setError("name is required");
        }

        //user validity
        if ($this->nameValidation() == false) {
            $this->setError("invalid username");
        }

        if($this->emailIsEmpty() == true) {
            $this->setError("email is required");
        }

        //email validation
        if ($this->emailValidation() == false) {
            $this->setError("invalid Email");
        }

        //pass is empty
        if ($this->passIsEmpty()== true) {
            $this->setError("password is required");
        }

        //repass empty
        if($this->rePassIsEmtpy() == true) {
            $this->setError("confirm password");
        }


        //repass match
        if ($this->repassValidation() == false) {
            $this->setError("passwords do not match");
        }

        //userName or pass exists
        if ($this->userIsTaken()) {
            $this->setError("user already exists");
        }

    
        $this->setUser($this->name, $this->email, $this->pass);
    }

    private function setError(string $error)
    {
        header("location:../../api/signup-form.php?error=$error");
        exit();
    }

    //validate for empty inputs
    private function emptyInput()
    {

        if (
            empty($this->name) &&
            empty($this->email) &&
            empty($this->pass) &&
            empty($this->repass)
        ) {
            $validation = false;
        } else {
            $validation = true;
        }

        return $validation;
    }

    private function nameValidation()
    {
        //some regExp
        if (!preg_match("/^[a-zA-Z0-9]*$/", $this->name)) {

            $nameIsValid = false;
        } else {
           
            $nameIsValid = true;
        }

        return $nameIsValid;

    }

    private function nameIsEmpty()
    {
        if (empty($this->name)) {
            $nameIsEmpty = true;
        } else {
            $nameIsEmpty = false;
        }
        return $nameIsEmpty;
    }

    private function emailValidation()
    {

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {

            $emailIsValid = false;

        } else {
            // $this->email = filter_input($_POST["email"], FILTER_SANITIZE_EMAIL);
            $emailIsValid = true;
        }

        return $emailIsValid;

    }

    private function emailIsEmpty()
    {

        if(empty($this->email)) {
            $emailIsEmpty = true;
        } else {
            $emailIsEmpty = false;
        }

        return $emailIsEmpty;
    }

    private function passIsEmpty()
    {

        if(empty($this->pass)) {
            $passIsEmpty = true;
        } else {
            $passIsEmpty = false;
        }
        return $passIsEmpty;
    }

    private function rePassIsEmtpy()
    {
        if(empty($this->repass)) {
            $repassIsEmpty = true;
        } else {
            $repassIsEmpty = false;
        }
        return $repassIsEmpty;
    }

    private function repassValidation()
    {

        if ($this->pass !== $this->repass) {
            $repassMatched = false;
        } else {
            $repassMatched = true;
        }

        return $repassMatched;

    }

    private function userIsTaken()
    {

        if (!$this->checkUser($this->name, $this->email)) {
            $userExists = false;
        } else {
            $userExists = true;
        }

        return $userExists;

    }

}
