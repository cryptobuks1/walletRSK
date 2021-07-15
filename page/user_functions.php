<?php  

	if ($Module=='logout' and $_SESSION['USER_LOGIN_IN']==1){
		if($_COOKIE['user']){
			setcookie('user', '', strtotime('-30 days'), '/');
			unset($_COOKIE['user']);
		}
		session_unset();
		MessageSend('info', 'You have successfully logged out.', '/login');
	}



	if ($Module=='addWallet' and $_POST['add_wallet']){
		if (!preg_match('/^0x([A-Fa-f0-9]{40})$/', $_POST[wallet])){
			MessageSend('error','Write a valid EMV-compatible wallet address !');
		}

		// $Rov=mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `wallet_main` FROM `users` WHERE `id`='$_SESSION[USER_ID]' AND `wallet_main`='$_POST[wallet]'"));
		// if($Rov){
		// 	MessageSend('error', 'This wallet is already attached to your account');
		// }

		$Rov2=mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `wallet_address` FROM `wallets` WHERE `user_id`='$_SESSION[USER_ID]'  AND `wallet_address`='$_POST[wallet]'"));
		if($Rov2){
			MessageSend('error', 'This wallet is already attached to your account');
		}


		mysqli_query($CONNECT, "INSERT INTO `wallets` (`user_id`, `wallet_address`) VALUES ('$_SESSION[USER_ID]','$_POST[wallet]');");

		array_push($_SESSION['USER_OTHERS_WALLETS'], $_POST['wallet']);

		MessageSend('info','You have successfully added a new address');
	}









	if ($Module=='selectWallet'){

		$newArray = array();

		for($i=0; $i < count($_SESSION['USER_OTHERS_WALLETS']); $i++){
			if($_SESSION['USER_OTHERS_WALLETS'][$i] != $Param[id]){
				array_push($newArray, $_SESSION['USER_OTHERS_WALLETS'][$i]);
			}
		}
		array_push($newArray, $_SESSION['USER_MAIN_WALLET']);
		$_SESSION['USER_OTHERS_WALLETS'] = $newArray;



		$_SESSION['USER_MAIN_WALLET'] = $Param[id];

		MessageSend('info', 'Wallet was changed !', "/");
	}






	if ($Module=='deleteWallet'){

		$newArray = array();

		for($i=0; $i < count($_SESSION['USER_OTHERS_WALLETS']); $i++){
			if($_SESSION['USER_OTHERS_WALLETS'][$i] != $Param[id]){
				array_push($newArray, $_SESSION['USER_OTHERS_WALLETS'][$i]);
			}
		}
		$_SESSION['USER_OTHERS_WALLETS'] = $newArray;


		if($_SESSION['USER_MAIN_WALLET'] == $Param[id]){
			$_SESSION['USER_MAIN_WALLET'] = $_SESSION['USER_OTHERS_WALLETS'][0];
		}
		
		mysqli_query($CONNECT, "DELETE FROM `wallets` WHERE `user_id`='$_SESSION[USER_ID]' AND `wallet_address`='$Param[id]'");

		MessageSend('info', 'Wallet was removed from address book !');
	}





	Ulogin(0);

	if ($Module=='login' and $_POST['go_login']){

		$_POST['password'] = GenPass($_POST['password'], $_POST['email']);

		$Rov=mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `users` WHERE `email`='$_POST[email]'"));

		if(!$Rov){
			MessageSend('error', 'User with such mail does not exist');
		} else if($Rov['password'] != $_POST['password']) {
			MessageSend('error', 'Wrong login or password.');
		}

		$_SESSION['USER_ID'] = $Rov['id'];
		$_SESSION['USER_LOGIN_IN'] = 1;

		
		$_SESSION['USER_OTHERS_WALLETS'] = array();

		$Rov2=mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `wallets` WHERE `user_id`='$Rov[id]'"));
		if($Rov2){
			$_SESSION['USER_MAIN_WALLET'] = $Rov2['wallet_address'];
			$result=mysqli_query($CONNECT, "SELECT * FROM `wallets` WHERE `user_id`='$Rov[id]'");
			while($wallet_row=mysqli_fetch_assoc($result)){
				if($wallet_row['wallet_address'] != $_SESSION['USER_MAIN_WALLET']){
					array_push($_SESSION['USER_OTHERS_WALLETS'], $wallet_row['wallet_address']);
				}
			}
		}

		setcookie('user', $_POST['password'], strtotime('+7 days'), '/');

		MessageSend('info', 'You are successfully logged in.', '/');
	}



	if ($Module=='register' and $_POST['go_register']){

		$Rov=mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id` FROM `users` WHERE `email`='$_POST[email]'"));
		if($Rov){
			MessageSend('error', 'A user with this email address already exists.');
		}

		if (!preg_match('/^0x([A-Fa-f0-9]{40})$/', $_POST[wallet])){
			MessageSend('error','Write a valid EMV-compatible wallet address !');
		}

		$_POST['password'] = GenPass($_POST['password'], $_POST['email']);
		

		$MaxId=mysqli_fetch_row(mysqli_query($CONNECT, "SELECT max(`id`) FROM `users`"));


		// $lastID =  mysql_result( mysql_query($CONNECT, "SELECT MAX(id) FROM users"), 0);
		$lastID = $MaxId[0] + 1;

		mysqli_query($CONNECT, "INSERT INTO `users` (
			`id`,
			`email`,
			`password`,
			`regdate`) 
			VALUES (
			'$lastID',
			'$_POST[email]',
			'$_POST[password]',
			NOW())");

		mysqli_query($CONNECT, "INSERT INTO `wallets` (`user_id`, `wallet_address`) VALUES ('$lastID','$_POST[wallet]');");

		
		MessageSend('info','The account has been successfully created.', "/login");
	}



	



?>
