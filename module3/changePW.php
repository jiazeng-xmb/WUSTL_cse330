
 <?php 
    require 'database.php';
    session_start(); 
    if (!hash_equals($_SESSION['token'],$_POST['token'])){
        die("Token Does Not Match!");
    }
    $username = $_SESSION['user'];
    $cpw = $_POST['cpw'];
    $npw = $_POST['npw'];
    $rpw = $_POST['rpw'];

    if($npw != $rpw)
    {
        header("Location: profilePage.php?error=2");
    }
    else
    {
        // password is typed correctly
        $query = $mysqli->prepare("select password from users where username='".$username."'");
        if(!$query)
        {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $query->execute();
        $query->bind_result($pw);
        if($query->fetch())
        {
            $query->close();
            if(password_verify($cpw, trim($pw)))   // if password match
            {
                // change to new password
                $query = $mysqli->prepare("update users set password=? where username=?");
                if(!$query)
                {
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                $query->bind_param('ss',password_hash($_POST['npw'], PASSWORD_DEFAULT),$username);
                $query->execute();
                $query->close();
                header("Location: profilePage.php?error=1");
            }
            else    // current password not match
            {
                header("Location: profilePage.php?error=3");
            }
        }
        else    // user not exist
        {
            header("Location: profilePage.php?error=4");
        }
    }
 ?>