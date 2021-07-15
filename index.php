<?php
	include_once 'settings.php';
	ob_start(); 
	
        error_reporting(E_ERROR | E_PARSE);


	session_start();
	$CONNECT=mysqli_connect(HOST, USER, PASS, DB);

	$_COOKIE['user'] = FormChars($_COOKIE['user'], 1);

	if($_SESSION['USER_LOGIN_IN'] !=1 and $_COOKIE['user']) {
		$Rov=mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `users` WHERE `password` = '$_COOKIE[user]'"));

		// Защита от Cookie инъекции
		if (!$Rov) { 
			setcookie('user', '', strtotime('-7 days'), '/');
			unset($_COOKIE['user']);
			MessageSend('red','Login error...');
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
	}

	// Link initial /
	if ($_SERVER['REQUEST_URI']=='/') {
		$Page='home';
		// $Module='main';
	} else {
		$URL_Path=parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$URL_Path_clear = trim($URL_Path, '/');
		$URL_Parts=explode('/', $URL_Path_clear);
		$Page=array_shift($URL_Parts);
		$Module=array_shift($URL_Parts);	
		if (!empty($Module)) {
			$Param=array();
			for($i=0;$i<count($URL_Parts);$i++){
				$Param[$URL_Parts[$i]]=$URL_Parts[++$i];
			}
		} else $Module = 'main';
	}

//----------------------------Rooting: page, module, ---------------------
	if(in_array($Page, array('home', 'tokens', 'token_transfers', 'nft', 'faucet', 'market', 'sovryn', 'login', 'register', 'user_functions'))) {
		include("page/$Page.php");
	}

	else if($Page == 'oferte' and in_array($Module, array('main', 'view', 'ajax', 'rezervare'))) {
		include("module/oferte/$Module.php");
	}

	else if($Page == 'profile' and in_array($Module, array('main', 'my-transactions', 'my-orders', 'control_rez'))) {
		include("module/profile/$Module.php");
	}

	else {NotFound();}
//-------end---------------------page, module, ---------------------

	function MessageSend($p1, $p2, $p3 = '', $p4 = 1){
		switch ($p1) {
			case 'error':
				$title = "Error";
				break;
			case 'info':
				$title = "Information";
				break;
		}
		$_SESSION['message']='
			<div class="notif active-notif notif-'.$p1.'">
				<b>'.$title.'</b><br>
				'.$p2.'
			</div>
		';
		if($p4){
			if($p3){$_SERVER['HTTP_REFERER'] = $p3;}
			exit(header('Location: '.$_SERVER['HTTP_REFERER']));
		}	
	}

	function MessageShow(){
		if($_SESSION['message']) $Message=$_SESSION['message'];
		echo $Message;
		$_SESSION['message']= array();
	}

	function NotFound(){
		header('HTTP/1.0 404 Not Found');
		exit(include("page/404.php"));
		// MessageSend('error','Only logged users are allowed to access this page.', '/login');
	}

	function ULogin($p1){
		if($p1 == 0 and $_SESSION['USER_LOGIN_IN'] != $p1) {
			MessageSend('error','Logged users cannot access the given page.', '/');
		} else if($_SESSION['USER_LOGIN_IN']!=$p1) {
			MessageSend('error','Only logged users are allowed to access this page.', '/login');
		}
	}


	function Head($p1) {
		?>
		<!DOCTYPE html>
			<html lang="en">
			<head>
			   <meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<link rel="stylesheet" href="/resource/styles/nullStyles.css">
				<link rel="stylesheet" href="/resource/styles/vars.css">	
				<link rel="stylesheet" href="/resource/styles/styles.css">
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">	

				<!-- Favicons -->
				<link rel="apple-touch-icon" sizes="180x180" href="/resource/images/favicons/apple-touch-icon.png">
			    <link rel="icon" type="image/png" sizes="32x32" href="/resource/images/favicons/favicon-32x32.png">
			    <link rel="icon" type="image/png" sizes="16x16" href="/resource/images/favicons/favicon-16x16.png">
			    <link rel="mask-icon" href="/resource/images/favicons/safari-pinned-tab.svg" color="#000000">
			    <link rel="shortcut icon" href="/resource/images/favicons/favicon.ico">

			    <!-- Font Awesome -->
			    <script src="https://kit.fontawesome.com/ed57e229be.js" crossorigin="anonymous"></script>

				<title>RSK Wallet <?=$p1?></title>
			</head>
		<?
	}

	function PageSelector($p1, $current, $p3, $p4 = 6){
		/*
		$p1 - URL (Ex: /oferte/main/page)
		$current - Pagina curenta
		$p3 - Nr. de inregistrari
		$p4 - Nr. de elemente pe pagina
		*/

		$Pages = ceil($p3[0] / $p4);
		if ($Pages > 1) {
			echo '<div class="pagination"> <div class="pages">';

			$prev = $current-1;
			if($current > 1){
				echo '<div class="page"> <a href="'.$p1.$prev.'" class="page btn_page"> <span>	&#8592;</span> </a> </div>';
			}

			$prev_offset = $current-2;
			$next_offset = $current+2;

			if( (($current-2) > 1)){
				echo '<div class="page"> <a href="'.$p1.'1" class="page btn_page"> <span>1</span> </a> </div>';
			}

			if((($current-3) > 1) ){
				echo '<div class="page"> <a class="page btn_page"> <span>...</span> </a> </div>';
			}

			for($i = 1; $i < ($Pages+1); $i++){
				if($i<=$next_offset and $i>=$prev_offset  ){

					if($current == $i) {$curent_page='current_page';} 
						else {$curent_page='';}

					echo '<div class="page"> <a href="'.$p1.$i.'" class="page btn_page '.$curent_page.'"> <span>'.$i.'</span> </a> </div>';
				}				
			}

			if((($current+3) < $Pages) ){
				echo '
				<div class="page"> <a class="page btn_page"> <span>...</span> </a> </div>';
			}

			if( (($current+2) < $Pages)){
				echo '
				<div class="page"> <a href="'.$p1.$Pages.'" class="page btn_page"> <span>'.$Pages.'</span> </a> </div>';
			}

			$next = $current+1;
			if($current < $Pages){
				echo '<div class="page"> <a href="'.$p1.$next.'" class="page btn_page"> <span>	&#8594;</span> </a> </div>';
			}

			echo '</div></div>';
		}
	}


	function Menu($nr) {
		$arrayItems = array();
		for ($i=0; $i < 6; $i++) { 
			array_push($arrayItems, "");
		}
		$arrayItems[$nr-1] = 'class="active"';
	?>
		<div class="menu">
			<ul>
				<a href="/">
					<li <?=$arrayItems[0];?>>
					<div class="icon"><i class="fas fa-th-large"></i></div>
					<div class="title">
						<p>Dashboard</p>
					</div>
				</li>
				</a>

				<a href="/tokens">
					<li <?=$arrayItems[1];?>>
						<div class="icon"><i class="fas fa-chart-pie"></i></div>
						<div class="title">
							<p>Tokens balance</p>
						</div>
					</li>
				</a>

				<a href="/nft">
					<li <?=$arrayItems[2];?>>
						<div class="icon"><i class="fas fa-clone"></i></div>
						<div class="title">
							<p>NFTs</p>
						</div>
					</li>
				</a>

				<a href="/faucet">
					<li <?=$arrayItems[3];?>>
						<div class="icon"><i class="fas fa-faucet"></i></div>
						<div class="title">
							<p>tRBTC faucet</p>
						</div>
					</li>
				</a>

				<a href="/market">
					<li <?=$arrayItems[4];?>>
						<div class="icon"><i class="fas fa-chart-line"></i></div>
						<div class="title">
							<p>Market</p>
						</div>
					</li>
				</a>

				<a href="/sovryn">
					<li <?=$arrayItems[5];?>>
						<div class="icon"><i class="fas fa-cog"></i></div>
						<div class="title">
							<p>Sovryn stats</p>
						</div>
					</li>
				</a>

			</ul>
		</div>
		<?
	}

	
	function HeaderBlock(){
		global $CONNECT;
		?>
		<div class="modal hidden-element">
			<div class="modal__content"></div>
		</div>
		<header>
			<nav>
				<div class="logo">
					<h1>RSK Wallet</h1>
				</div>

				<div class="gasPrice">
					<div class="icon"></div>
					<div class="text"><i class="fas fa-gas-pump"></i> &nbsp;RSK Gas price: <span id="gasPrice">-</span> Gwei</div>
				</div>

				<div class="options">
					<div class="wallet_block" id="walletBlock">
						<div class="row" id="walletRow">
							<h3><span>Wallet 1 </span> (<?=substr($_SESSION['USER_MAIN_WALLET'], 0, 5);?>...<?=substr($_SESSION['USER_MAIN_WALLET'], -4);?>)</h3>
							<i id="extendWalletList" class="fa fa-angle-down" style="font-size:20px"></i>
						</div>

						<div class="list-outer">
							<div class="list" id="walletList">
								<ul>
									<?
										for($i=0; $i < count($_SESSION['USER_OTHERS_WALLETS']); $i++){
											echo "
												<li>
													<a class='wallet' href='/user_functions/selectWallet/id/".$_SESSION['USER_OTHERS_WALLETS'][$i]."'>
														<div>
															<span class='wallet'>Wallet ".($i+2)."</span> (".substr($_SESSION['USER_OTHERS_WALLETS'][$i], 0, 5)."...".substr($_SESSION['USER_OTHERS_WALLETS'][$i], -4).")
														</div>
													</a>

													<a href='/user_functions/deleteWallet/id/".$_SESSION['USER_OTHERS_WALLETS'][$i]."'>
														<span class='delete'><i class='far fa-trash-alt'></i></span>
													</a>
												</li>
												";
										}
									?>
									<!-- <li><span>Wallet 2</span> (0x77...ed)</li>
									<li><span>Wallet 3</span> (0x77...ed)</li>
									<li><span>Wallet 4</span> (0x77...ed)</li> -->
								</ul>

								<div style="width: 100%; text-align: center;">
									<div class="btn" id="addWallet" style=" margin-top: 8px; ">
										<div style="display: flex;align-items: center;justify-content: center;">
											<span>Add new wallet</span>&nbsp;&nbsp;
											<i class="fas fa-plus"></i>
										</div>
									</div>
								</div>
								
							</div>
						</div>

					</div>

					<a href="/user_functions/logout">
						<div class="btn" style="width: 100px;">
							<div style="display: flex;align-items: center;justify-content: center;">
								<span>Logout</span>&nbsp;&nbsp;
								<i class="fas fa-sign-out-alt"></i>
							</div>
						</div>
					</a>

				</div>


			</nav>
		</header>
		<?
	}


	function Footer($header = 1) {
		?>
		<script src="/resource/scripts/header.js"></script>
		<script src="/resource/scripts/hidemsgbox.js"></script>
		<script src="/resource/scripts/api.js"></script>
		<script src="/resource/scripts/qrcode.js"></script>


		<script>
			// GasPrice
			getGasPrice();
		</script>

		<script>
			const walletBlock = document.getElementById("walletBlock");
			const walletRow = document.getElementById("walletRow");
			const extendWalletList = document.getElementById("extendWalletList");
			const walletList = document.getElementById("walletList");

			walletRow.addEventListener("click", ()=>{
				walletBlock.classList.toggle("active");
				walletList.classList.toggle("active");
				walletList.style.width = `${walletBlock.offsetWidth}px`;
			})
		</script>

		<!-- <script>
			const openModal = document.getElementById("openModal");
			const modal = document.getElementsByClassName("modal")[0];
			const modal__content = document.getElementsByClassName("modal__content")[0];

			openModal.addEventListener("click", ()=>{
				modal.classList.remove("hidden-element");
				document.body.style = "overflow: hidden";
			})

			modal.addEventListener("click", (event)=>{
				modal.classList.add("hidden-element");
				document.body.style = "";
			})

			modal__content.addEventListener("click", (event)=>{
				event.stopPropagation();
			})
		</script> -->

		<!-- <script>
			let notifCount = [];
			const notifBlock = document.getElementById("notif-block");

			const addNotif = document.getElementById("addNotif");
			let count = 1;
			addNotif.addEventListener("click", ()=>{

				notifCount = document.querySelectorAll(".active-notif");

				notifs = document.querySelectorAll(".notif")
				notifs.forEach((item)=>{
					if(item.classList.contains("hide-notif") ){
						item.classList.add("hidden-element");
					}
				})


				if(notifCount.length >=4){
					var lastElem = notifCount[notifCount.length-1];
					lastElem.classList.add("move-notif");
					lastElem.classList.remove("active-notif");

					let min = 999999;
					notifCount.forEach((element)=>{
						if(parseInt(element.getAttribute("data-id")) < min ){
							min = parseInt(element.getAttribute("data-id"));
						}
					})
					console.log(min);

					notifCount = document.querySelectorAll(".active-notif");

					setTimeout(function(){
						document.querySelectorAll(`[data-id='${min}']`)[0].remove("move-notif");
					}, 300);
				}


				notifBlock.innerHTML = `
				<div data-id="${count}" class="notif active-notif notif-red" style="animation: showNotif 0.3s;">
						<b>${count++}</b><br>
						You are nor loged!
					</div>
			    ` + notifBlock.innerHTML;
				notifCount = document.querySelectorAll(".active-notif");

				setTimeout(()=>{
					notifs = document.querySelectorAll(".notif")
					notifs.forEach((item)=>{
						item.addEventListener("click", ()=>{
							// item.classList.add("move-notif");
							if(!item.classList.contains("hide-notif") ){
								item.classList.add("hide-notif");
								item.classList.remove("active-notif");
								notifCount = document.querySelectorAll(".active-notif");
							}
							setTimeout(()=>{
								item.classList.add("hidden-element");
							}, 300)

							console.log(notifCount);
							console.log("ID:" + item.getAttribute("data-id"))
						})
						item.style="";
						
					})
				}, 150)

				console.log(notifCount);

			})
		</script> -->
		<?
	}
	
	function FormChars($p1, $p2 = 0){
		global $CONNECT;
		if ($p2) return mysqli_real_escape_string($CONNECT, $p1);
		else return nl2br(htmlspecialchars(trim($p1), ENT_QUOTES), false);
	}


	function GenPass($p1,$p2){
		return md5('twetwet'.md5('423'.$p1.'634').md5('643'.$p2.'315'));
	}
?>