<?php
function Connection(){
    $server = "localhost";
    $name = "root";
    $pass = "";
    $db = "testservice";

    $mysqli = new mysqli($server,$name,$pass,$db);

    if($mysqli->connect_error){
        echo json_encode(["error" => "An error has been encountered in the database connection."]);
    }else{
        return $mysqli;
    }
}