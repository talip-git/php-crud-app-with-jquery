<?php
    session_start();
    unset($_SESSION["name"]);
    unset($_SESSION["user_id"]);
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        require_once 'pdo.php';
        $salt = 'XyZzy12*_';
        $check = hash('md5', $salt.$_POST['pass']);

        $stmt = $con->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
       
        $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
       
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row != false){
            $_SESSION['name']=$row['name'];
            $_SESSION['user_id'] = $row['user_id'];
            header("Location:index.php");
            exit(0);
        }
        else{
            $_SESSION['error'] = 'Incorrect email or password';
            header("Location:login.php");
            exit(0);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talip Sina Postacı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
</head>
<body>
    <h1 class = "container" style = "margin-top: 50px;margin-bottom: 50px;">Please Login</h1>
    <?php
        if(isset($_SESSION['error'])){
            echo '<p class = "container" style = "color:red;">'.htmlentities($_SESSION['error']).'</p>';
            unset($_SESSION['error']);
        }
        ?> 
    <form class = "container" method = "POST"action="login.php">    
        <b>Email:</b><input class = "form-control" type="text" name = "email" id="email_field"><br>
        <b>Password:</b><input class = "form-control" type="password" name = "pass" id="password_field"><br>
        <input type="submit" onclick="return validate();" value="Log In"> <a href="index.php"><input type="button" value="Cancel"></a><br>
    </form>
    <script>
    function validate(){
        try{
            var email = document.getElementById("email_field").value;
            var password = document.getElementById("password_field").value;

            if(email == null || email == "" || password == null || password == ""){
                alert("Both fields must be filled!");
                return false;
            }
            if(email.indexOf("@") == -1){
                alert("Invalid Email Adress format!");
                return false;
            }
            return true;
        }catch(err){
            return false;
        }
        return false;
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous"></script>
</body>
</html>