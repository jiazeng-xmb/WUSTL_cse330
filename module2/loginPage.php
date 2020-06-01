<?php  
    //check session state
    session_start(); 
    if(isset($_SESSION['user']))
    {
        header('Location: mainPage.php');
    }
?>

<!doctype html>
<html lang='en'>
    <head>
        <title>	CSE503 File Sharing </title>
        <meta charset="utf-8"/>
    </head>
    <body>
        <div class="text" style="text-align:center;">
        <!-- title display -->
        <h1> File Sharing Site </h1>
        <!-- error message -->
        <?php
            if(isset($_GET['error']))
            {
                $error = trim($_GET['error']);
                if ($error == '1') 
                {
                    echo '<span style="color: red;"> Username does not exit!</span>';
                }
                else if ($error == '3') 
                {
                    echo '<span style="color: red;"> Sign up successfully! Please log in. </span>';
                }
            }
        ?>
        <!-- username input and login-->
        <form action="login.php" method="POST">
            <p>Username: <input type="text" name="user" /></p>
            <input type="submit" value="Submit" />
        </form>
        <br/><br/><br/>
        <h3> No account? Sign up below! </h3>
        <!-- error message -->
        <?php
            if (isset($_GET['error']) && $_GET['error'] == '2') 
            {
                echo '<span style="color: red;"> User already exist! Please log in directly. </span>';
            }
        ?>
        <!--sing up new users-->
        <form action="signup.php" method="POST">
            <p>Username: <input type="text" name="user" /></p>
            <input type="submit" value="Submit" />
        </form>
        </div>
    </body>
</html>