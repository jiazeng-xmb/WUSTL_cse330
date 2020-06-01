<?php 
// Session check
session_start();
if (!isset($_SESSION['user'])){
    header('Location: mainPage.php?error=9');
}else if(!$_POST["name"]||!isset($_FILES['filename'])){
    header('Location: mainPage.php?error=5');
}else{
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
    $filename = $_FILES['filename']['name'];
    $target_path = "/srv/uploads/".$username."/".$filename;
    //upload the file
    if (move_uploaded_file($_FILES['filename']['tmp_name'],$target_path)){
        header('Refresh:5; url=mainPage.php');
        echo "<br/>Congradulations! The file is uploaded successfully!<br/><br/>";
        echo "You will go back your mainPage to view your files in several seconds";
    }
    else{
        header('Location: mainPage.php?error=6');
    }
} 


?>