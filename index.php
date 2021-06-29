<?php
session_start();
function printProfiles(){
    require_once'pdo.php';
    $stmt = $con->prepare("SELECT profile_id,first_name,last_name,headline FROM Profile");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row != false){
        echo '<table class = "table table-striped">';
        echo '<tr>';
            echo '<th>Name</th>';
            echo '<th>Headline</th>';
            echo '<th>Action</th>';
        echo '</tr>';
        while($row != false){
            echo '<tr>';
                echo "<td><a href = 'view.php?profile_id=".htmlentities($row['profile_id'])."'>".htmlentities($row['first_name'])." ".htmlentities($row['last_name'])."</a></td>";
                echo '<td>'.htmlentities($row['headline']).'</td>';
                if (isset($_SESSION['name']) && isset($_SESSION['user_id'])){
                    echo "<td><a href = 'edit.php?profile_id=".htmlentities($row['profile_id'])."'>edit</a> <a href = 'delete.php?profile_id=".htmlentities($row['profile_id'])."'>delete</a></td>";
                }
                else{
                    echo "<td><a href = 'view.php?profile_id=".htmlentities($row['profile_id'])."'>view</a></td>";
                }     
            echo '<tr>';
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        echo '</table>';
    }
    else{
        echo '<p style = "margin-top:20px;">No Profiles in the database.</p><br>';
        return;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinap</title>
    <link rel="stylesheet" href="./cssFiles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h1 style = "text-align:center;margin-top: 50px;">Sinap's Resume Registry</h1>
        <?php
            $flag = 0;
            if (isset($_SESSION['success'])){
                echo '<p style = "color:green;">Profile added succesfully!</p><br>';
                unset($_SESSION['success']);
            }
            if (isset($_SESSION['name']) && isset($_SESSION['user_id'])){
                echo '<a href="logout.php">Logout</a><br>';
                $flag = 1;
            }
            else{
                echo '<a href="login.php">Please log in</a><br>';
            }
            echo '<div style = "margin-top:20px;">';
            printProfiles();
            echo '</div>';
            if ($flag == 1){
                echo '<a href = "add.php";">Add New Entry</a><br>';
            }
        ?>
        <p style = "margin-top:30px;"><b>Note:</b>Your implementation should retain data across multiple logout/login sessions. This sample implementation clears all its data periodically - which you should not do in your implementation.</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous"></script>
</body>
</html>
