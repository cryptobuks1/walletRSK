<?php 

ULogin(1);

Head("| Tokens balance");

$current_wallet = $_SESSION['USER_MAIN_WALLET'];


?>

<body>

	<main>
		<div class="container">
			<?php HeaderBlock();?>

			<div id="content">

				<aside class="sidebar">
					<?php Menu(2); ?>
				</aside>

				<aside class="content">
					<h1>Tokens balance:</h1>

					<div style=" display: flex; align-items: flex-start; ">
						<div id="tokenList" class="block" style="min-height: 124px; width: 100%; margin-top: 14px; ">
							<center>
								<div class="lds-dual-ring" style=" height: 30px; margin: 0px auto; text-align: center; width: 48px; margin-top: 13px;"></div>
							<center>
						</div>
					</div>

				</aside>

			</div>


			<div class="notif-block" id="notif-block">
				<?php MessageShow(); ?>
			</div>

		</div>
	</main>

	<?php Footer();?>




	<script>
		const tokenList = document.getElementById("tokenList");

		;(async () => {
			const tokensData = await getTokenBalances(<?=chainMainnet?>, "<?php echo $current_wallet; ?>", "<?=APIKEY;?>");

			if(tokensData.error){
				tokenList.innerHTML = `
					<span style="font-weight: 600;font-size: 27px;">
						<h3>Error</h3>
					</span>
			  	`;
			} 
			else{
				tokenList.innerHTML = "";

				if(tokensData.data.length){
					tokenList.innerHTML += ` 
						<div class="token_row token_row_header">
							<div class="logo_title">Token</div>
							<div class="contract">Contract address</div>
							<div class="balance">Balance</div>
						</div>
					 `
				
					for(var i = 0; i < tokensData.data.length; i++){
						var balance = tokensData.data[i].balance / 10 ** (tokensData.data[i].contract_decimals);
						if(balance > 0.9){
							balance = (Math.round(((balance) + Number.EPSILON) * 100000000) / 100000000).toString().toLocaleString()
						} else{
							balance = (Math.round(((balance) + Number.EPSILON) * 100000000) / 100000000).toString().toLocaleString()
						}

						tokenList.innerHTML += ` 
							<div class="token_row">
								<div class="logo_title">
									<a href="/token_transfers/${tokensData.data[i].contract_address}">
										<img src="${tokensData.data[i].logo_url}" alt="">
										<h3>${tokensData.data[i].contract_name} (${tokensData.data[i].contract_ticker_symbol})</h3>
										&nbsp;&nbsp;<i class="fas fa-external-link-alt"></i>
										</a>
								</div>
								<div class="contract">
									<a target="_blank" href="https://explorer.rsk.co/address/${tokensData.data[i].contract_address}">${tokensData.data[i].contract_address.substr(0, 5)}...${tokensData.data[i].contract_address.substr(-4)}
									<i class="fas fa-external-link-alt"></i>
									</a>
								</div>
								<div class="balance">${balance}</div>

							</div>
						 `
					}
				}
				else{
					tokenList.innerHTML = `
					<center><br><h2>You don't have tokens</h2></center>
					`;
				}

			}

		})();


	</script>

		
</body>
	
</html>