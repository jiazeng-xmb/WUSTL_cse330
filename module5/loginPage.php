<?php
    ini_set("session.cookie_httponly", 1);
    session_start(); 
    if(isset($_SESSION['user']))
    {
        header('Location: calendar.html');
    }
?>

<!doctype html>
<html lang='en'>
    <head>
        <title>	CSE503 Calendar </title>    <!-- 与zj page统一名字 -->
        <meta charset="utf-8"/>
    </head>
    <body>
        <div class="text" style="text-align:center;">
        <h1> Calendar </h1> <!-- 与zj page统一名字 -->
        <!-- error message -->
        <?php
            if(isset($_GET['error']))
            {
                $error = trim($_GET['error']);
                if ($error == '1') 
                {
                    echo '<span style="color: red;"> Username does exit!</span>';
                }
                else if ($error == '3') 
                {
                    echo '<span style="color: red;"> Sign up successfully! Please log in. </span>';
                }
                else if ($error == '4') 
                {
                    echo '<span style="color: red;"> Wrong password! </span>';
                }
            }
        ?>
        <!-- username input -->
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
        <form action="signup.php" method="POST">
            <p>Username: <input type="text" name="user" /></p>
            <input type="submit" value="Submit" />
        </form>
        </div>
    </body>
</html>