<?php 
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

    if($isExist)
    {
        header("Location: loginPage.php?error=2");
    }
    else    //create a new user
    {
        $data = $username."\n";
        $f = fopen('/srv/uploads/users.txt', 'a');
        fwrite($f, $data);
        fclose($f);
        mkdir('/srv/uploads/'.$username, 0777);
        $f = fopen('/srv/uploads/'.$username.'group.txt','w');
        fclose($f);
        header("Location: loginPage.php?error=3");
    }
?>