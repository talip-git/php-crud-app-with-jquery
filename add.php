<?php
    session_start();
    require_once 'utilityFunctions.php';
    if(!isset($_SESSION["name"])){
        die('Error!\nYou can not view this site!');
        exit(0);
    }
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])){
            if(strlen($_POST['first_name'])<1 || strlen($_POST['last_name'])<1 || 
               strlen($_POST['email'])<1 || strlen($_POST['headline'])<1 ||strlen($_POST['summary'])<1){
                $_SESSION['error'] = 'All fields are required';
                header("Location:add.php");
                return;
            }
            if(strpos($_POST['email'],'@') == false){
                $_SESSION['error'] = 'Wrong email format!';
                header("Location:add.php");
                return;
            }
        }
        for ($i=0;$i<=9;$i++){
            if(!isset($_POST['year'.$i]) || !isset($_POST['desc'.$i])) continue;
            else{
                if(strlen($_POST['desc'.$i])<1 || strlen($_POST['year'.$i])<1){
                    $_SESSION['error'] = 'Description can not be empty!';
                    header("Location:add.php");
                    exit(0);
                }
                if(!is_numeric($_POST['year'.$i])){
                    $_SESSION['error'] = 'Year has to be a numeric value!';
                    header("Location:add.php");
                    exit(0);
                }
            }
        }
        require_once 'pdo.php';
        $stmt = $con->prepare('INSERT INTO Profile
        (user_id, first_name, last_name, email, headline, summary)
        VALUES ( :uid, :fn, :ln, :em, :he, :su)');
      
        $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
        );
        $id = $con->lastInsertId();
        
        $rank = 1;
        for ($i=0; $i<=9; $i++) { 
            if(!isset($_POST['year'.$i]) || !isset($_POST['desc'.$i])){
                continue;
            }else{
                $stmt = $con->prepare('INSERT INTO Position (profile_id,rank,year,description) VALUES(:pif, :rn, :yr, :des )');
                $stmt->execute(array(
                    ':pif'=>$id,
                    ':rn' =>$rank,
                    ':yr' =>$_POST['year'.$i],
                    ':des' =>$_POST['desc'.$i]
                ));
                $rank++;
            }
        }
        $_SESSION['success'] = "Profile Added";
        header("Location:index.php");
        exit(0);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinap</title>
    <script
    src="https://code.jquery.com/jquery-3.6.0.js"
    integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
    crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./cssFiles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
</head>
<body>
    <div class = "container">
        <h1 style = "margin-top: 50px;">Add A New Entry</h1>
        <?php
            if(isset($_SESSION['error'])){
                echo '<p style = "color:red;" id = "error">'.htmlentities($_SESSION['error']).'</p><br>';
                unset($_SESSION['error']);
            }
        ?>
    </div>
    <form class = "container" action="add.php" method = "POST">
        <label class = "form-label" for="first_name">First Name: </label><br>
        <input class = "form-control" type="text" name="first_name" id="first_name">

        <label class = "form-label" for="last_name">Last Name: </label><br>
        <input class = "form-control" type="text" name="last_name" id="last_name">

        <label class = "form-label" for="email">Email: </label><br>
        <input class = "form-control" type="text" name="email" id="email">

        <label class = "form-label" for="headline">Headline: </label><br>
        <input class = "form-control" type="text" name="headline" id="headline">

        <label class = "form-label" for="summary">Summary: </label><br>
        <textarea class="form-control" name="summary" id="summary" style="height: 150px"></textarea>

        <label class = "form-label" for="pos">Position: </label><br>
        <input type="button" class = "btn btn-secondary"value="+" id = "addPos" name ="pos"><br>
        <div id = "position_fields"></div>
        <input type="submit" class = "btn btn-primary" value="Add The Entry" style = "margin-top:30px;"> <a href="index.php"><input type="button" style = "margin-top:30px;" class = "btn btn-primary" value="Cancel"></a><br>
    </form>
    
    <script>
        var count_pos = 0;
        $(document).ready(()=>{
            $("#addPos").click(()=>{
                console.log("Clicked to the button: +");
                if(count_pos >=9){
                    alert("Can not add more than 9 positions!");
                    return;
                }
                count_pos++;
                console.log("Count Position: "+count_pos);
                $("#position_fields").append('<div id = "position'+count_pos+'">\
                <label class = "form-label">Year: </label><br> \
                <input class = "txt-field" type = "text" name = "year'+count_pos+'">\
                <input type = "button" class = "btn btn-secondary" onclick = "$(\'#position'+(count_pos)+'\').remove();return false;" value = "-"><br>\
                <textarea class = "form-control" name = "desc'+count_pos+'" style = "height: 150px"></textarea><br>\
                </div>');
            });
        });
        const deletediv = ()=>{
            $("#position"+count_pos).remove();
            return false;
        }
    </script>
    <script>
        function validate(){
            try {
                var first_name = document.getElementById("first_name").value;
                var last_name = document.getElementById("last_name").value;
                var email = document.getElementById("email").value;
                var headline = document.getElementById("headline").value;
                var summary = document.getElementById("summary").value;

                if(first_name == "" || first_name == null || last_name == "" || last_name == null ||
                email == "" || email == null || headline == "" || headline == null|| summary == "" || summary == null){
                    document.getElementById("error").innerHTML = "All fields are required";
                    return false;
                }
                if(email.indexOf("@") == -1){
                    document.getElementById("error").innerHTML = "Please enter a valid email!";
                    return false;
                }
                return true;
            }
            catch(err){
                return false;
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous"></script>
</body>
</html>
