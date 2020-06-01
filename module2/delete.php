<?php
//  Session check
session_start();
if (!isset($_SESSION['user'])){
    header('Location: mainPage.php?error=9');
// check if the user input the file name
}else if(!$_POST["filename"]){ 
    header('Location: mainPage.php?error=5');
}else if(!$_POST["name"]){
    header('Location: mainPage.php?error=5');
}
else{
    //check if the user is a friend
    $f = fopen('/srv/uploads/'.$_SESSION['user'].'group.txt','r');
    $isExist = false;
    $username = $_POST["name"];
    while(!feof($f)){
        if(trim(fgets($f)) == $username) {
            $isExist = true;
            break;
        }
    }
    fclose($f);
    //if the input user name is neither the user nor the username
    //go back the mainPage
    if (!$isExist && $_SESSION['user'] != $username){
        header('Location: mainPage.php?error=4');
        exit;
    }
    $username = $_POST["name"];
    $filename = $_POST["filename"];
    $target_path = "/srv/uploads/".$username."/".$filename;
    // if file exists, then delete and go back to main page
    if (file_exists($target_path)){
        unlink($target_path);
        header('Refresh:3; url=mainPage.php');
        echo "delete successfully!<br/>";
        echo "You will go back your mainPage in several seconds";
    }else{
        header('Location: mainPage.php?error=8');
    }
}
?>