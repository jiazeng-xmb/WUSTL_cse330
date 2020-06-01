<?php 
// Session check
session_start();
if (!isset($_SESSION['user'])){
    header('Location: mainPage.php?error=9');
// check if the user input the file name
}else if(!$_POST["filename"]){
    header('Location: mainPage.php?error=5');
}else if(!$_POST["name"]){
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
    $filename = $_POST["filename"];
    $target_path = "/srv/uploads/".$username."/".$filename;
    $tmp = explode('.', $filename);
    $file_ext=strtolower(end($tmp));
    // if file exists, then view the files
    if (file_exists($target_path)){
        $expensions= array("gif","jpeg","jpg","png");
        $other = array("pdf");
        //check the type of file
        //present the file in a correct format
        if(in_array($file_ext,$expensions)===true){
            $array = getimagesize($target_path);
            if($array){
                $image = file_get_contents($target_path);
                echo "<img src='data:image/jpg;base64,".base64_encode($image)."' alt='Approved Image'>";
            }
            else{
                header('Location: mainPage.php?error=7');
            }}else if(in_array($file_ext,$other)===true){
            header("Content-Type: application/pdf");
            header('Content-Disposition: inline; filename*="utf8\'\''.$target_path.'"');
            ob_start();
            $content = ob_get_contents();
            ob_end_clean();
            echo $content;
        }
        else{
            echo file_get_contents($target_path);
        }
    }else{
        header('Location: mainPage.php?error=8');
    }
}
?>