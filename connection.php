<?php

$hostDetails = 'mysql:host=localhost; dbname=port_project;';
$userAdmin = 'root';
$pass = '';

try{
    $pdo = new PDO($hostDetails,$userAdmin,$pass);
} catch(PDOExecption $e){
    echo 'Connection error!' . $e->getMessage();
}

?>
