<?php
    require_once 'utilityFunctions.php';
    session_start();
    if(!isset($_SESSION['name'])){
        die("You can not view this site!");
        return;
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])){
            if(strlen($_POST['first_name'])<1 || strlen($_POST['last_name'])<1 || 
               strlen($_POST['email'])<1 || strlen($_POST['headline'])<1 ||strlen($_POST['summary'])<1){
                $_SESSION['error'] = 'All fields are required';
                header("Location:edit.php?profile_id=".$_GET['profile_id']);
                return;
            }
            if(strpos($_POST['email'],'@') == false){
                $_SESSION['error'] = 'Wrong email format!';
                header("Location:edit.php?profile_id=".$_GET['profile_id']);
                return;
            }
        }
        for ($i=0;$i<=9;$i++){
            if(!isset($_POST['year'.$i]) || !isset($_POST['desc'.$i])) continue;
            else{
                if(strlen($_POST['desc'.$i])<1 || strlen($_POST['year'.$i])<1){
                    $_SESSION['error'] = 'Description can not be empty!';
                    header("Location:edit.php?profile_id=".$_GET['profile_id']);
                    return;
                }
                if(!is_numeric($_POST['year'.$i])){
                    $_SESSION['error'] = 'Year has to be a numeric value!';
                    header("Location:edit.php?profile_id=".$_GET['profile_id']);
                    return;
                }
            }
        }
        require_once 'pdo.php';
        $stmt = $con->prepare('UPDATE Profile SET first_name = :fn ,last_name = :ln, email =:em, headline = :he, summary= :su WHERE profile_id = :pid');
        $stmt->execute(array(
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'],
        ':pid' => $_GET['profile_id']));
        

        $stmt = $con->prepare('DELETE FROM position WHERE profile_id = :pif');
        $stmt ->execute(array(
            'pif'=>$_GET['profile_id']
        ));
        
        $rank = 1;
        for ($i=0; $i<=9; $i++) { 
            if(!isset($_POST['year'.$i]) || !isset($_POST['desc'.$i])){
                continue;
            }else{
                $stmt = $con->prepare('INSERT INTO Position (profile_id,rank,year,description) VALUES(:pif, :rn, :yr, :des )');
                $stmt->execute(array(
                    ':pif'=>$_GET['profile_id'],
                    ':rn' =>$rank,
                    ':yr' =>$_POST['year'.$i],
                    ':des' =>$_POST['desc'.$i]
                ));
                $rank++;
            }
        }
        header('Location:index.php');
        return;
    }
?>
<?php
require_once 'pdo.php';
require_once 'utilityFunctions.php';
$stmt = $con->prepare('SELECT * FROM Profile WHERE profile_id = :pid');
$stmt->execute(array(':pid'=>$_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <h1 class = "container" style = "margin-top: 50px;">Edit Entry</h1>
    <form class = "container" action= <?php echo htmlspecialchars('edit.php?profile_id='.$row['profile_id'])?> method = "POST">
        <?php
            if(isset($_SESSION['error'])){
                echo '<p style = "color:red;" id = "error">'.htmlentities($_SESSION['error']).'</p><br>';
                unset($_SESSION['error']);
            }
        ?>
        <?php
            echo '<label class = "form-label" for="first_name">First Name: </label><br>';
            echo '<input class = "form-control" type="text" name="first_name" id="first_name" value="'.htmlentities($row['first_name']).'">';

            echo '<label class = "form-label" for="last_name">Last Name: </label><br>';
            echo '<input class = "form-control" type="text" name="last_name" id="last_name" value="'.htmlentities($row['last_name']).'">';

            echo '<label class = "form-label" for="email">Email: </label><br>';
            echo '<input class = "form-control" type="text" name="email" id="email" value="'.htmlentities($row['email']).'">';

            echo '<label class = "form-label" for="headline">Headline: </label><br>';
            echo '<input class = "form-control" type="text" name="headline" id="headline" value="'.htmlentities($row['headline']).'">';

            echo '<label class = "form-label" for="summary">Summary: </label><br>';
            echo '<textarea class="form-control" name="summary" id="summary" style="height: 150px;margin-bottom:20px;">'.htmlentities($row['summary']).'</textarea>';
        ?>
        <label class = "form-label" for="pos">Position: </label>
        <input type="button" class = "btn btn-secondary"value="+" id = "addPos" name ="pos"><br>
        <div id = "position_fields">
            <?php $pos_number = printPositions();?>
        </div>
        <input type="submit" onclick ="return validate();" class= "btn btn-primary" value="Save" style = "margin-top:30px;"> <a href="index.php"><input type="button" style = "margin-top:30px;" class = "btn btn-primary" value="Cancel"></a><br>  
    </form>
    <script>
        var count_pos = Number('<?php echo $pos_number;?>');
        $(document).ready(()=>{
            $("#addPos").click(()=>{
                console.log("Clicked to the button: +");
                if(count_pos >=9){
                    alert("Can not add more than 9 positions!");
                    return;
                }
                console.log("Count Position: "+count_pos);
                $("#position_fields").append('<div id = "position'+count_pos+'">\
                <label class = "form-label">Year: </label><br> \
                <input class = "txt-field" type = "text" name = "year'+count_pos+'">\
                <input type = "button" class = "btn btn-secondary" onclick = "$(\'#position'+(count_pos)+'\').remove();return false;" value = "-"><br>\
                <textarea class = "form-control" name = "desc'+count_pos+'" style = "height: 150px"></textarea><br>\
                </div>');
                count_pos++;
            });
        });
    </script>
    <script>
        const validate = () =>{
            let email = document.getElementById("email").value;
            console.log(email);
            if(email.indexOf("@") == -1){
                alert("Wrong email format!");
                return false;
            }
            return true;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous"></script>
</body>
</html>
