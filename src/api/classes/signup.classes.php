<?php

//This makes sure, we can not manually get to these pages. (e.g - typing in the URL)

class Signup extends Dbh
{
    private $sql_check_user = 'SELECT user_name FROM pintzy_user_info WHERE user_name = ? OR user_email = ?;';
    private $sql_insert_user = 'INSERT INTO pintzy_user_info (user_name, user_email, user_password) VALUES (?, ?, ?);';

    //store error messages
    private $errorMsg = '';
    
    protected function setUser($userName, $email, $password)
    {

       

        // Check if the user already exists
        $stmt_check = $this->connect()->prepare($this->sql_check_user);

        if (!$stmt_check->execute([$userName, $email])) {

            $this->errorHandler($stmt_check, "Failed to check User");
            // $this->error = 'Failed to check user.';
            // $stmt_check = null;
            // $_SESSION['signupSuccessful'] = false;

            // return false;
        }
        if ($stmt_check->rowCount() > 0) {
            $this->errorHandler($stmt_check, "User Already Exists");

            // $this->error = 'User already exists.';
            // $stmt_check = null;
            // $_SESSION['signupSuccessful'] = false;

            // return false;
        }

        // Insert the new user into the database
        $stmt_insert = $this->connect()->prepare($this->sql_insert_user);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        if (!$stmt_insert->execute([$userName, $email, $hashedPassword])) {
            $this->errorHandler($stmt_check, "Failed to Insert User");

            // $this->error = 'Failed to insert user.';
            // $stmt_insert = null;
            // $_SESSION['signupSuccessful'] = false;
            // return false;
        }

        
        //close the connection
        $stmt_check = null;
        
       
        session_start();

        //on success
        $_SESSION['signupSuccessful'] = true;
        $_SESSION['userName'] = $userName;
        // return true;
    }


     protected function checkUser($userName, $email)
     {
         $stmt = $this->connect()->prepare($this->sql_check_user);

         //checks for connection
         if (!$stmt->execute(array($userName, $email))) {
             // if this fails, set error message
             //  $this->error = 'Failed to check user.';
             //  $stmt = null;
             //  return false;
             $this->errorHandler($stmt, "Failed to check User");

         }

         // fetch the number of rows returned by the SELECT statement
         $rowCount = $stmt->fetchColumn();

         // if the row count is greater than 0, a user with the same user name or email exists
         $result = ($rowCount > 0) ? false : true;

         // close the connection
         $stmt = null;

         return $result;
     }

     //expose error
    //  public function getError()
    //  {
    //      return $this->errorMsg;
    //  }

         protected function errorHandler($stmt, $msg)
         {
             $stmt = null;
             $this->errorMsg = "$msg";
             header("location:../../../index.php?error=$this->errorMsg");
             exit();
        

         }

}
