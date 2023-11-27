<?php
require 'load.php';
include 'login.php';

	if(login::isLoggedIn()){
		$userid = login::isLoggedIn();
		header('location: index.php');
	}else{
		// header('location: index.php');
	}

if(isset($_POST['firstName']) && !empty($_POST['firstName'])){
	$upFirst = $_POST['firstName'];
    $upLast = $_POST['lastName'];
    $upEmailMobile = $_POST['email'];
    $upPassword = $_POST['pass'];
    $birthDay = $_POST['birth-day'];
    $birthMonth = $_POST['birth-month'];
    $birthYear = $_POST['birth-year'];
    if(!empty($_POST['gender'])){
    $upgen = $_POST['gender'];
    }
    $birth = ''.$birthYear.'-'.$birthMonth.'-'.$birthDay.'';

    if(empty($upFirst) or empty($upLast) or empty($upEmailMobile) or empty($upgen)){
        $error = 'All feilds are required';
    }else{
	$first_name = $loadFromUser->checkInput($upFirst);
	$last_name = $loadFromUser->checkInput($upLast);
	$email_mobile = $loadFromUser->checkInput($upEmailMobile);
	$password = $loadFromUser->checkInput($upPassword);
	$screenName = ''.$first_name.'_'.$last_name.'';
			if(DB::query('SELECT screenName FROM users WHERE screenName = :screenName', array(':screenName' => $screenName ))){
	$screenRand = rand();
				$userLink = ''.$screenName.''.$screenRand.'';
			}else{
				$userLink = $screenName;
			}
	if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^",$email_mobile)){
	if(!preg_match("^[0-9]{11}^", $email_mobile)){
		$error = 'Email id or Mobile number is not correct. Please try again.';
	}else{
		$mob = strlen((string)$email_mobile);

		if($mob > 11 || $mob < 11){
			$error = 'Mobile number is not valid';
		}else if(strlen($password) <5 || strlen($password) >= 60){
			$error = 'Password is not correct';
		}else{
			if(DB::query('SELECT mobile FROM users WHERE mobile=:mobile', array(':mobile'=>$email_mobile))){
				$error = 'Mobile number is already in use.';
			}else{
				$user_id = $loadFromUser->create('users', array('spec_no'=>$screenRand,'first_name'=>$first_name,'last_name'=>$last_name, 'mobile' => $email_mobile, 'password'=>password_hash($password, PASSWORD_BCRYPT),'screenName'=>$screenName,'userLink'=>$userLink, 'birthday'=>$birth, 'gender'=>$upgen));

					$loadFromUser->create('profile', array('userId'=>$user_id, 'birthday'=> $birth, 'firstName' => $first_name, 'lastName'=>$last_name, 'profilePic'=>'assets/img/banner.png', 'gender'=>$upgen, 'about'=>'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam               erat volutpat. Morbi imperdiet, mauris ac auctor dictum, nisl               ligula egestas nulla.'));

				$tstrong = true;
				$token = bin2hex(openssl_random_pseudo_bytes(64, $tstrong));
			$loadFromUser->create('token', array('token'=>sha1($token), 'user_id'=>$user_id));

			setcookie('NID', $token, time()+60*60*24*7, '/', NULL, NULL, true);

			header('Location: index.php');


			}
		}
   	}
}else{
	if(!filter_var($email_mobile)){
		$error = "Invalid Email Format";
	}else if(strlen($first_name) > 20){
		$error = "Name must be between 2-20 character";
	}else if(strlen($password) <5 && strlen($password) >= 60){
		$error = "The password is either too shor or too long";
	}else{
		if((filter_var($email_mobile,FILTER_VALIDATE_EMAIL)) && $loadFromUser->checkEmail($email_mobile) === true){
			$error = "Email is already in use";
		}else{
				$screenRand = rand();

			$user_id = $loadFromUser->create('users', array('spec_no'=>$screenRand,'first_name'=>$first_name,'last_name'=>$last_name, 'email' => $email_mobile, 'password'=>password_hash($password, PASSWORD_BCRYPT),'screenName'=>$screenName,'userLink'=>$userLink, 'birthday'=>$birth, 'gender'=>$upgen));

			$loadFromUser->create('profile', array('userId'=>$user_id, 'birthday'=>$birth, 'firstName' => $first_name, 'lastName'=>$last_name, 'profilePic'=>'assets/img/banner.png', 'gender'=>$upgen, 'about'=>'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam               erat volutpat. Morbi imperdiet, mauris ac auctor dictum, nisl               ligula egestas nulla.'));


	$tstrong = true;
	$token = bin2hex(openssl_random_pseudo_bytes(64, $tstrong));
			$loadFromUser->create('token', array('token'=>sha1($token), 'user_id'=>$user_id));

			setcookie('NID', $token, time()+60*60*24*7, '/', NULL, NULL, true);

			header('Location: index.php');

		}
	}
	}



    }
}

if(isset($_POST['emailNum']) && !empty($_POST['emailNum'])){
    $email_mobile = $_POST['emailNum'];
    $in_pass = $_POST['in-pass'];

    if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email_mobile)){
        if(!preg_match("^[0-9]{11}^", $email_mobile)){
            $error = 'Email or Phone is not correct. Please try again';
        }else{

        if(DB::query("SELECT mobile FROM users WHERE mobile = :mobile", array(':mobile'=>$email_mobile))){
            if(password_verify($in_pass, DB::query('SELECT password FROM users WHERE mobile=:mobile', array(':mobile'=>$email_mobile))[0]['password'])){

                $user_id = DB::query('SELECT user_id FROM users WHERE mobile=:mobile', array(':mobile'=>$email_mobile))[0]['user_id'];
               $tstrong = true;
			$token = bin2hex(openssl_random_pseudo_bytes(64, $tstrong));
          $loadFromUser->create('token', array('token'=>sha1($token), 'user_id'=>$user_id));

		  if(DB::query("SELECT * FROM users WHERE a_status = :a_status AND user_id = :userid", array(':a_status'=>'1', ':userid'=>$user_id))){
			setcookie('NAID', $token, time()+60*60*24*7, '/', NULL, NULL, true);
			header('Location: profiles.php');
		  }else{
			setcookie('NID', $token, time()+60*60*24*7, '/', NULL, NULL, true);

			header('Location: index.php');
		  }
            }else{
                $error="Password is not correct";
            }

        }else{
            $error="User wasn't found.";
        }

        }
    }else{
        if(DB::query("SELECT email FROM users WHERE email = :email", array(':email'=>$email_mobile))){
            if(password_verify($in_pass, DB::query('SELECT password FROM users WHERE email=:email', array(':email'=>$email_mobile))[0]['password'])){

                $user_id = DB::query('SELECT user_id FROM users WHERE email=:email', array(':email'=>$email_mobile))[0]['user_id'];
               $tstrong = true;
$token = bin2hex(openssl_random_pseudo_bytes(64, $tstrong));
          $loadFromUser->create('token', array('token'=>sha1($token), 'user_id'=>$user_id));
		  if(DB::query("SELECT * FROM users WHERE a_status = :a_status AND user_id = :userid", array(':a_status'=>'1', ':userid'=>$user_id))){
			setcookie('NAID', $token, time()+60*60*24*7, '/', NULL, NULL, true);
			header('Location: profiles.php');
		  }else{
			setcookie('NID', $token, time()+60*60*24*7, '/', NULL, NULL, true);

			header('Location: index.php');
		  }
            }else{
                $error="Password is not correct";
            }

        }else{
            $error="User wasn't found.";
        }
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
	<title>INTOPPL | Sign</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100 unmes">
				<div class="error">
				</div>
				<div class="sreer">
					<form class="login100-form" action="sign.php" method="post">
						<span class="login100-form-title">
							Login
						</span>

						<div class="wrap-input100 validate-input" data-validate = "Valid email or number is required: ex@abc.xyz">
							<input class="input100" type="text" name="emailNum" id="emailNum" placeholder="Email / Number">
							<span class="focus-input100"></span>
							<span class="symbol-input100">
								<i class="fa fa-envelope" aria-hidden="true"></i>
							</span>
						</div>

						<div class="wrap-input100 validate-input" data-validate = "Password is required">
							<input class="input100" type="password" name="in-pass" id="in-pass" placeholder="Password">
							<span class="focus-input100"></span>
							<span class="symbol-input100">
								<i class="fa fa-lock" aria-hidden="true"></i>
							</span>
						</div>
						
						<div class="container-login100-form-btn">
							<input type="submit" name="submit" class="login100-form-btn acct" value="Login">
						</div>

						<div class="text-center p-t-12">
							<span class="txt1">
								Forgot
							</span>
							<a class="txt2" href="#">
								Username / Password?
							</a>
						</div>

						<div class="text-center p-t-136 unmess">
							<a class="txt2" href="#">
								Create your Account
								<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
							</a>
						</div>
					</form>
				</div>
				<div class="serrr" style="display: none;">
					<form class="login100-form" action="sign.php" method="post">
						<span class="login100-form-title">
							<strong style="color:red;">E-</strong>Registration
						</span>

						<div class="wrap-input100 validate-input" data-validate = "First Name is required">
							<input class="input100" type="text" name="firstName" placeholder="First Name">
							<span class="focus-input100"></span>
							<span class="symbol-input100">
								<i class="fa fa-user" aria-hidden="true"></i>
							</span>
						</div>

						<div class="wrap-input100 validate-input" data-validate = "Last Name is required">
							<input class="input100" type="text" name="lastName" placeholder="Last Name">
							<span class="focus-input100"></span>
							<span class="symbol-input100">
								<i class="fa fa-user" aria-hidden="true"></i>
							</span>
						</div>

						<div class="wrap-input100" data-validate = "Valid email / number is required: ex@abc.xyz">
							<input class="input100" type="text" name="email" placeholder="Email">
							<span class="focus-input100"></span>
							<span class="symbol-input100">
								<i class="fa fa-envelope" aria-hidden="true"></i>
							</span>
						</div>

						<div class="wrap-input100 validate-input" data-validate = "Gender is required">
							<select name="gender" id="gender" class="input100">
								<option value="Gender">Gender</option>
								<option value="Male">Male</option>
								<option value="Female">Female</option>
							</select>
							<span class="focus-input100"></span>
							<span class="symbol-input100">
								<i class="fa fa-user" aria-hidden="true"></i>
							</span>
						</div>

						<div class="wrap-input100 validate-input" data-validate = "Password is required">
							<input class="input100" type="password" name="pass" placeholder="Password">
							<span class="focus-input100"></span>
							<span class="symbol-input100">
								<i class="fa fa-lock" aria-hidden="true"></i>
							</span>
						</div>
						<div class="wrap-input100 validate-input" data-validate = "birthday is required">
							<div class="sign-up-birthday">
								<div class="form-birthday">
									<div class="dayss">Day<select name="birth-day" id="days" class="select-body input100"></select></div>
									<div class="dayss">Month<select name="birth-month" id="months" class="select-body input100"></select></div>
									<div class="dayss">Year<select name="birth-year" id="years" class="select-body input100"></select></div>
								</div>
							</div>
						<span class="focus-input100"></span>
						</div>
						
						<div class="container-login100-form-btn">
							<input type="submit" class="login100-form-btn" value="Register" name="submit">
						</div>

						<div class="text-center p-t-12">
							<span class="txt1">
								Forgot
							</span>
							<a class="txt2" href="#">
								Username / Password?
							</a>
						</div>

						<div class="text-center p-t-136 messed">
							<a class="txt2" href="#">
								already have an account ?Login
								<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
							</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<script src="jquery.js"></script>
	<script>
		$(document).on('click', '.unmess', function(){
			$('.sreer').empty();
			$('.serrr').show();
		})
		$(document).on('click', '.messed', function(){
			location.reload();
		})
	</script>
	<script>
        for (i = new Date().getFullYear(); i > 1900; i--) {
            //    2019,2018, 2017,2016.....1901
            $("#years").append($('<option/>').val(i).html(i));

        }
        for (i = 1; i < 13; i++) {
            $('#months').append($('<option/>').val(i).html(i));
        }
        updateNumberOfDays();

        function updateNumberOfDays() {
            $('#days').html('');
            month = $('#months').val();
            year = $('#years').val();
            days = daysInMonth(month, year);
            for (i = 1; i < days + 1; i++) {
                $('#days').append($('<option/>').val(i).html(i));
            }

        }
        $('#years, #months').on('change', function() {
            updateNumberOfDays();
        })

        function daysInMonth(month, year) {
            return new Date(year, month, 0).getDate();

        }

    </script>

</body>
</html>