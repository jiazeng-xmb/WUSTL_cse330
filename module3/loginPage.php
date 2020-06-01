<!doctype html>
<html lang='en'>
    <head>
        <title>	CSE503 News Web Site </title>
        <meta charset="utf-8"/>
    </head>
    <body>
        <div class="text" style="text-align:center;">
        <!-- title display -->
        <h1> CSE503 New Web Site </h1>
        <!-- error message -->
        <?php
            session_start(); 
            if(isset($_SESSION['user']))
            {
                header('Location: mainPage.php');
            }

            if(isset($_GET['error']))
            {
                $error = trim($_GET['error']);
                if ($error == '1') 
                {
                    echo '<span style="color: red;"> Username does not exit! </span>';
                }
                else if ($error == '3') 
                {
                    echo '<span style="color: red;"> Sign up successfully! Please log in. </span>';
                }
                else if ($error == '4') 
                {
                    echo '<span style="color: red;"> Password not match! </span>';
                }
            }
        ?><br /><br />
        <!-- login -->
        <form action="login.php" method="POST">
            Username: <input type="text" name="username" /><br /><br />
            Password: <input type="password" name="pw" /><br /><br />
            <input type="submit" name="guest" value="Sign in as Guest" />
            <input type="submit" name="user" value="Log in" />
        </form>

        <br/><br/><br/>
        <h3> No account? Sign up below! </h3>
        <!-- error message -->
        <?php
            if (isset($_GET['error'])) 
            {
                $error = trim($_GET['error']);
                if($error  == '2')
                {
                    echo '<span style="color: red;"> User already exist! Please log in directly. </span>';
                }
                else if($error  == '5')
                {
                    echo '<span style="color: red;"> Password do not match. </span>';
                }    
            }
        ?><br />
        <!-- sign up -->
        <form action="signup.php" method="POST">
            <p>Username: <input type="text" name="user" /></p>
            <p>Password: <input type="password" name="pw" /></p>
            <p>Retype Password: <input type="password" name="rpw" /></p>
            <input type="submit" value="Submit" />
        </form>
        </div>
    </body>
</html>