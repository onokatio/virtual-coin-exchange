<?php
function mymysql($db,$pass){
  try{
    $database = new PDO("mysql:dbname=".$db.";host=localhost", $db, $pass);
  } catch (PDOException $e) {
    print('Connection failed:'.$e->getMessage());
    exit();
  }
  return $database;
}

function alert($type,$str){
    echo "<div class='alert alert-".$type." text-center' role='alert' style='margin: 10px 10px; '>".$str."</div>";
}
function alert_wide($type,$str){
    echo "<div class='alert alert-".$type." text-center' role='alert' style='margin: 10px 10px;  padding-top: 30px; padding-bottom: 30px;'>".$str."</div>";
}
function passcrypt($str){
  $str = MD5($str);
  $str = "etalebil".$str."liblib";
  $str = hash('sha256',$str);
  return $str;
}
?>
