<?php

include '../config/config.php';

reqRecieveLogger();



//get the data
$sqlGetAllPins = "SELECT * FROM pintzy_user_pin LIMIT 20";

require '../db/db-connector.php';

try {

    //prepare
    $stmt = $pdo->prepare($sqlGetAllPins);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //json conversion
    $reponse = json_encode($result);

    //set header
    header('Content-Type: application/json');

    //echo it out
    echo $reponse;

} catch (\Exception $th) {
    //throw $th;
    throw new Exception("Error fetching data". $th->getMessage(), $e->getCode());

}