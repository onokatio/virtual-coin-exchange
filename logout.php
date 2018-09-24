<?php
session_start();
unset($_SESSION["account"]["id"]);
header("location: index.php");
exit();
?>
