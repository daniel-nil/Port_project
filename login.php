<?php

include 'DB.php';

class login{

    public static function isLoggedIn(){
        if(isset($_COOKIE['NID'])){
            if(DB::query('SELECT user_id FROM token WHERE token = :token', array(':token' => sha1($_COOKIE['NID'])))){

                $user_id = DB::query('SELECT user_id FROM token WHERE token = :token', array(':token' => sha1($_COOKIE['NID'])))[0]['user_id'];

                return $user_id;



            }

        }
    }

}


?>
