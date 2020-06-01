<?php 
//  Session check
//  https://www.cnblogs.com/evai/p/6057219.html
session_start();
if (!isset($_SESSION['user'])){
    header('Location: loginPage.php');
    echo "Please login";
// check if the user input the file name
}
$f1 = fopen('/srv/uploads/users.txt','r');
$f2 = fopen('/srv/uploads/'.$_SESSION['user'].'group.txt','r');

$friend = $_POST['friend'];
if ($friend == '') {
    header('Location: mainPage.php?error=10');
    exit;
}
$isExist = false;
while(!feof($f1)){
    if(trim(fgets($f1)) == $friend) {
        $isExist = true;
        break;
    }
}
fclose($f1);
if(!$isExist){
    header('Location: mainPage.php?error=1');
    exit;
}

$isExist = false;
if(filesize('/srv/uploads/'.$_SESSION['user'].'group.txt') != 0)
{
    while(!feof($f2)){
        if(trim(fgets($f2)) == $friend) {
            $isExist = true;
            break;
        }
    }
}

fclose($f2);
if($isExist){
    header('Location: mainPage.php?error=2');
    exit;
}
$f2 = fopen('/srv/uploads/'.$_SESSION['user'].'group.txt','a');
fwrite($f2,$friend."\n");
fclose($f2);
header('Location: mainPage.php?error=3');
?>