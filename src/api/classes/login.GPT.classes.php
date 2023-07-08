<?php

use App\Foundation;

class Login
{
    protected function getUser($userName, $pass)
    {
        

        try {

            $user = Foundation::db()->fetchOne("SELECT * FROM users WHERE username = ? OR email = ?", [$userName, $userName]);
            
            if ($user && password_verify($pass, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['loginSuccessful'] = true;

                header("Location: ../user.php");
                exit();
            } else {
                $this->error = "Invalid username or password.";
                $_SESSION['loginSuccessful'] = false;
                header("Location: ../index.php?error=invalidlogin");
                exit();
            }
        } catch (PDOException $e) {
            $this->error = "Statement failed: " . $e->getMessage();
            $_SESSION['loginSuccessful'] = false;
            header("Location: ../index.php?error=statementfailed");
            exit();
        }
    }
}
