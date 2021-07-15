<?php 

ULogin(1);

Head("| tRBTC faucet");

$current_wallet = $_SESSION['USER_MAIN_WALLET'];


?>

<body>

	<main>
		<div class="container">
			<?php HeaderBlock();?>

			<div id="content">

				<aside class="sidebar">
					<?php Menu(4); ?>
				</aside>

				<aside class="content">
					<h1>tRBTC faucet</h1>

					<div style=" display: flex; align-items: flex-start;     flex-wrap: wrap; ">
						<div id="balance_rbtc" class="block" style="display: flex; min-width: 209px; margin-bottom: 14px; margin-top: 14px; margin-right: 30px; min-height: 100px;">
							<div>
								<h3>tRBTC Balance:</h3>
								<div >
									<center>
										<div class="lds-dual-ring" style=" height: 30px; margin: 0px auto; text-align: center; width: 48px; margin-top: 13px;"></div>
									<center>
								</div>
							</div>
							
						</div>
					</div><br>


					<h2>Last claims:</h2>

					<div style=" display: flex; align-items: flex-start; ">
						<div id="lastCLaims" class="block" style="min-height: 124px; width: 100%; margin-top: 14px; ">
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

		const balance_rbtc = document.getElementById("balance_rbtc");
		const lastCLaims = document.getElementById("lastCLaims");


		;(async () => {
			// test Balance
			;(async () => {
				var result = await getBalanceRBTC(<?=chainTest?>, "<?php echo $current_wallet; ?>", "<?=APIKEY;?>");

				if(result.error){
					balance_rbtc.innerHTML = `
						<span style="font-weight: 600;font-size: 27px;">
							<h3>error</h3>
						</span>
				  	`;
				} 
				else{
					var RBTC_balance = result.data.items[0].balance / 10 ** (result.data.items[0].contract_decimals);
					balance_rbtc.innerHTML = `

						<div>
							<h3>tRBTC Balance:</h3>
							<div >
								<span style="font-weight: 600;font-size: 27px;">
									<span>${RBTC_balance.toFixed(8)}</span>
								</span>
							</div>
						</div>
						
						<a href="https://faucet.rsk.co/" target="_blank" style=" margin-left: 36px; align-items: center; display: flex; justify-content: space-between; ">
							<div >
								<div class="btn" id="send_btn" style=" padding: 5px 22px; ">Claim tRBTC</div>
							</div>
						</a>
				  	`;

				}
			})();


			// Last claims of tRBTC
			// test Balance
			;(async () => {
				const faucetClaims = await getFaucetClaims(<?=chainTest?>, "<?php echo $current_wallet; ?>", "<?=APIKEY;?>");

				if(faucetClaims.error){
					lastCLaims.innerHTML = `
						<span style="font-weight: 600;font-size: 27px;">
							<h3>Error</h3>
						</span>
				  	`;
				} 
				else{
					lastCLaims.innerHTML = "";

					if(faucetClaims.data.items.length){
						lastCLaims.innerHTML += ` 
							<div class="token_row token_row_header claims">
								<div class="tx">Tx hash</div>
								<div class="date">Date</div>
								<div class="balance">Value</div>
							</div>
						 `
					
						for(var i = 0; i < faucetClaims.data.items.length; i++){
							let claim = faucetClaims.data.items[i];
							let tx_hash = `
								<a target="_blank" href="https://explorer.testnet.rsk.co/tx/${claim.tx_hash}">
									${claim.tx_hash.substr(0, 5)}...${claim.tx_hash.substr(-4)}
									<i class="fas fa-external-link-alt"></i>
								</a>
							`;

							var value = claim.value / 10 ** 18;
							if(value > 0.9){
								value = (Math.round(((value) + Number.EPSILON) * 100000000) / 100000000).toString().toLocaleString()
							}  else{
								value = (Math.round(((value) + Number.EPSILON) * 100000000) / 100000000).toString().toLocaleString()
							}

							lastCLaims.innerHTML += ` 
								<div class="token_row claims">
									<div class="tx">
										${tx_hash}
									</div>
									<div class="date">${claim.block_signed_at.substr(0, 10) + " " + claim.block_signed_at.substr(11, 8)}</div>
									<div class="balance">${value} tRBTC</div>
								</div>
							 `
						}
					}
					else{
						lastCLaims.innerHTML = `
							<center><br><h3>No faucet claims</h3></center>
						`;
					}

				}
			})();


		})();

	</script>

		
</body>
	
</html>