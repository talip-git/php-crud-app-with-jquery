<?php
function printPositions(){
    $username = "root";
    $password = "";
    try{
        $con = new PDO("mysql:host=localhost;dbname=misc",$username,$password);
    }catch(Exception $e){
        print "Could not connect to the db!";
    }
    $stmt = $con->prepare('SELECT * FROM Position WHERE profile_id = :pid');
    $stmt->execute(array('pid'=>$_GET['profile_id']));
    $row = $stmt ->fetch(PDO::FETCH_ASSOC);
    $count_pos = 1;
    while($row !=false){
        echo '<div id = "position'.$count_pos.'">
        <label class = "form-label">Year: </label><br> 
        <input class = "txt-field" type = "text" name = "year'.$count_pos.'" value = "'.htmlentities($row['year']).'">
        <input type = "button" class = "btn btn-secondary" onclick = "$(\'#position'.($count_pos).'\').remove();return false;" value = "-"><br>
        <textarea class = "form-control" name = "desc'.$count_pos.'" style = "height: 150px">'.htmlentities($row['description']).'</textarea><br>
        </div>';
        $count_pos++;
        $row = $stmt ->fetch(PDO::FETCH_ASSOC);
    }
    return $count_pos;
}
function displayPositions(){
    $username = "root";
    $password = "";
    try{
        $con = new PDO("mysql:host=localhost;dbname=misc",$username,$password);
    }catch(Exception $e){
        print "Could not connect to the db!";
    }
    $stmt = $con->prepare('SELECT year,description FROM Position WHERE profile_id = :pid ORDER BY profile_id');
    $stmt->execute(array('pid'=>$_GET['profile_id']));
    $row = $stmt ->fetch(PDO::FETCH_ASSOC);
    if($row!=false){
        echo 'Positions: <br>';
        while($row !=false){
            echo '-'.htmlentities($row['year']).': '.htmlentities($row['description']).'<br>';
            $row = $stmt ->fetch(PDO::FETCH_ASSOC);
        }
    }
}
?>