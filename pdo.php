<?php
    $dbName = "misc";
    $servername = "localhost";
    $username = "root";
    $password = "";
    try{
        $con = new PDO("mysql:host=localhost;dbname=misc",$username,$password);
    }catch(Exception $e){
        print "Could not connect to the db!";
    }
    if(isset($_POST['email']) && isset($_POST['password'])){
        if(strlen($_POST['email'])<1 && strlen($_POST['password'])<1){
            $_SESSION['error'] = 'Fields must be filled!';
            exit(0);
            return;
        }
    }
?>