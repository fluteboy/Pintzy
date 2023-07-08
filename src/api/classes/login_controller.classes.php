<?php



class LoginController extends login
{
    
    public $userName;
    public $password;
    public $nameValidationMessage;
    public $passwordValidationMessage;

    //construct
    public function __construct($userName, $password)
    {
        //sanitize input
        $this->userName = htmlspecialchars($userName);
        // $this->userName = $userName;
        $this->password = $password;
        

    }

    //throw error

public function loginUser()
{
    
    if ($this->nameIsEmpty()) {
        $this->nameValidationMessage = "Name is required. ";
    }

    if ($this->loginPassIsEmpty()) {
        $this->passwordValidationMessage = "Password is required. ";
    }
    

    if (!empty($this->nameValidationMessage) || !empty($this->passwordValidationMessage)) {
        header("location:../../../index.php?loginError=&nameValidationMessage=". urlencode($this->nameValidationMessage). "&passwordValidationMessage=". urlencode($this->passwordValidationMessage));
        exit();
    }

    //get user data and run user authentication
    $this->getUser($this->userName, $this->password);
}


    
  

    protected function nameIsEmpty()
    {
        
        return  empty($this->userName)  ;

   
    }

    protected function loginPassIsEmpty()
    {
     
        return empty($this->password) ;
   
    }


}
