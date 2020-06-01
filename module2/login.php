<?php 
session_start();
$username = $_POST['user'];
$isExist = false;

// read each user in the file to check if input username is one of them
$file = fopen("/srv/uploads/users.txt", "r");

while( !feof($file) )
{
    if(trim(fgets($file)) == $username)
    {
        $isExist = true;
        break;
    }
}
fclose($file);

if($isExist)    // user exist
{
    $_SESSION['user'] = $_POST['user'];
    header("Location: mainPage.php");
}
else    // user not exist
{
    header("Location: loginPage.php?error=1");
}

?>
