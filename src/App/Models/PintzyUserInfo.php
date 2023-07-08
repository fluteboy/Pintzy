<?php

namespace App\Models;
use App\Core\Model;

/**
 * @property string $user_name;
 * @property string $user_email;
 * @property string $user_password;
 * @property string $user_image;
 * 
 */
class PintzyUserInfo extends Model
{
    protected string $table = "pintzy_user_info";


    /**
     * Loads the model based on the username
     * @var string $username
     * @return PintzyUserInfo|null
     */
    public static function findByUsername(string $username): ?self
    {
        return self::find(["user_name" => $username]);
    }

    /**
     * Loads the model by username AND email
     * 
     * @var string $username
     * @var string $user_email
     * @return PintzyUserInfo|null
     */
    public static function fetchByUserNameAndEmail(string $username, string $user_email): ?self
    {
        return self::find([
            "user_name" => $username,
            "user_email" => $user_email
        ]);
    }

    /**
     * Wrapper around the 'verifyPassword' method
     * 
     * @see password_verify
     * @return bool
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->user_password);
    }

    /**
     * Simple wrapper method around password hashing.
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

}