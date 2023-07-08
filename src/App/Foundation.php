<?php
namespace App;

use App\Core\Database;

class Foundation {

    private static ?Database $db = null;

    public static function db(): Database
    {

        if (self::$db === null)
        {
            self::$db = new Database();
        }

        return self::$db
;
    }

}