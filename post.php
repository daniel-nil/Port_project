<?php
// User count((array)$variable)   for remove warning from php file due to its version php 7.2 or more.
class Post extends User {
function __construct($pdo){
    $this->pdo = $pdo;
}
    public function start(){
        
    }
}

?>
