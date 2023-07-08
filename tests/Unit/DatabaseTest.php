<?php
use App\Foundation;
use App\Core\Database;

require_once("startup.php");

$db = Foundation::db();

test('Basic Database Test',  fn() => expect($db)->toBeInstanceOf(Database::class));

test('Select count test', fn() => expect($db->fetchOne("select count(1) from pintzy_user_info"))->tobeNumeric());