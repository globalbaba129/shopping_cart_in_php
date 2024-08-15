<?php

$conn = new mysqli('localhost','root','','shopping_system');
if($conn->connect_error){
    die('connection failed : ' . $conn->connect_error);
}

?>