<?php
    require_once 'utilityFunctions.php';
    session_start();
    if(!isset($_SESSION['name'])){
        die("You can not view this site!");
        exit(0);
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        require_once 'pdo.php';
        $stmt = $con->prepare('DELETE FROM Profile WHERE profile_id = :pfi');
        $stmt->execute(array(':pfi' => $_GET['profile_id']));

        $stmt = $con->prepare('DELETE FROM position WHERE profile_id = :pif');
        $stmt ->execute(array(
            'pif'=>$_GET['profile_id']
        ));

        header('Location:index.php');
        exit(0);
    }
?>
<?php
    require_once 'pdo.php';
    $stmt = $con->prepare('SELECT * FROM Profile WHERE profile_id = :pfi');
    $stmt->execute(array(':pfi'=>$_GET['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
</head>
<body>
    <h1 class = "container" style = "margin-top: 50px;">Delete Entry</h1>
    <form class = "container" action= <?php echo htmlspecialchars('delete.php?profile_id='.$row['profile_id'])?> method = "POST">
    <?php
        echo 'First Name: '. htmlspecialchars($row['first_name']).'<br>';
        echo 'Last Name: '. htmlentities($row['last_name']).'<br>';
    ?>
        <input type="submit" onclick = "return confirmation();"class = "btn btn-danger" value="Delete Entry" style = "margin-top:30px;"> <a href="index.php"><input type="button" style = "margin-top:30px;" class = "btn btn-primary" value="Cancel"></a><br>
    </form>
    <script>
        const confirmation = () =>{
            if(confirm("Your entry is going to get deleted.\nThis can not be reversed.\nWould you really like to continue?")){
                return true;
            }
            return false;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous"></script>
</body>
</html>
