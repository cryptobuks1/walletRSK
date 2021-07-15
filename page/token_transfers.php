<?php 

ULogin(1);

Head("| Token transfers");

$current_wallet = $_SESSION['USER_MAIN_WALLET'];

if(!$Module){
	MessageSend('error','Invalid contract address of token.', '/tokens');
}

if (!preg_match('/^0x([A-Fa-f0-9]{40})$/', $Module)){
	MessageSend('error','Invalid contract address of token.', '/tokens');
}


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
					<div id="titleToken" style=" align-items: center; display: flex; "></div>

					<div style=" display: flex; align-items: flex-start; ">
						<div id="tokenInfo" class="block" style="min-height: 124px; width: 100%; margin-top: 14px; ">
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
		const tokenInfo = document.getElementById("tokenInfo");
		
		const titleToken = document.getElementById("titleToken");

		;(async () => {
			const tokenMetadata = await getTokenMetadata(<?=chainMainnet?>, "<?php echo $current_wallet; ?>", "<?php echo $Module; ?>", "<?=APIKEY;?>");
			console.log(tokenMetadata);

			if(tokenMetadata.error){
				titleToken.innerHTML = ``;

			  	tokenInfo.innerHTML = ` 
			  		<span style="font-weight: 600;font-size: 27px;">
						<h1>Error</h1>
					</span>
			  	`
			}
			else{
				let info = tokenMetadata.data[0];
				titleToken.innerHTML = ` 
					<img src="/${info.logo_url}" style=" margin-right: 20px; width: 35px; ">
					<h1>${info.contract_ticker_symbol} token transfers</h1> `;

				;(async () => {

					const tokenTransfers = await getTokenTransfers(<?=chainMainnet?>, "<?php echo $current_wallet; ?>", "<?php echo $Module; ?>", "<?=APIKEY;?>");
					console.log(tokenTransfers);

					if(tokenTransfers.error){
						tokenList.innerHTML = `
							<span style="font-weight: 600;font-size: 27px;">
								<h1>Error</h1>
							</span>
					  	`;
					} 
					else{
						var balance = info.balance / 10 ** (info.contract_decimals);
						if(balance > 0.9){
							balance = (Math.round(((balance) + Number.EPSILON) * 100000000) / 100000000).toString().toLocaleString()
						} else {
							balance = balance.toFixed(18);
						}

						tokenInfo.innerHTML = ` 
						<div class="info">
							<h2>Your current balance: ${balance} ${info.contract_ticker_symbol} </h2><br>
						</div>
						<div id="tokenList"></div>
						`;

						const tokenList = document.getElementById("tokenList");

						if(tokenTransfers.data.items.length){
							tokenList.innerHTML += ` 
								<div class="token_row token_row_header transfers">
									<div class="tx">Tx</div>
									<div class="date">Date</div>
									<div class="from">From</div>
									<div class="to">To</div>
									<div class="balance">Value</div>
								</div>
							 `
						
							let tx_hashes = [];

							for(var i = 0; i < tokenTransfers.data.items.length; i++){
								let transfer = tokenTransfers.data.items[i];

								if(!tx_hashes.includes(transfer.tx_hash)){
									tx_hashes.push(transfer.tx_hash);

									for(var j = 0; j < transfer.transfers.length; j++){
										let from = `<a target="_blank" href="https://explorer.rsk.co/address/${transfer.transfers[j].from_address}">
										${transfer.transfers[j].from_address.substr(0, 5)}...${transfer.transfers[j].from_address.substr(-4)}</a>`;
										let to = `<a target="_blank" href="https://explorer.rsk.co/address/${transfer.transfers[j].to_address}">
											${transfer.transfers[j].to_address.substr(0, 5)}...${transfer.transfers[j].to_address.substr(-4)}</a>`;

										let type;
										if(transfer.transfers[j].transfer_type == "OUT"){
											type = `<div class="out_transfer transfer_type">OUT</div>`;
										} else if(transfer.transfers[j].transfer_type == "IN"){
											type = `<div class="in_transfer transfer_type">IN</div>`;
										}


										if(transfer.transfers[j].from_address.toLowerCase() == "<?php echo $current_wallet; ?>".toLowerCase()){
											from = "Your address"
										} 

										if(transfer.transfers[j].to_address.toLowerCase() == "<?php echo $current_wallet; ?>".toLowerCase()){
											to = "Your address"
										} 

										var value = transfer.transfers[j].delta / 10 ** (transfer.transfers[j].contract_decimals);
										if(value > 0.9){
											value = (Math.round(((value) + Number.EPSILON) * 100000000) / 100000000).toString().toLocaleString()
										}  else{
											value = (Math.round(((value) + Number.EPSILON) * 100000000) / 100000000).toString().toLocaleString()
										}

										tokenList.innerHTML += ` 
											<div class="token_row transfers">
												<div class="tx">
													<a target="_blank" href="https://explorer.rsk.co/tx/${transfer.tx_hash}">
														<i class="fas fa-external-link-alt"></i>
													</a>
												</div>
												<div class="date">${transfer.block_signed_at.substr(0, 10) + " " + transfer.block_signed_at.substr(11, 8)}<br>${type}</div>
												<div class="from">${from}</div>
												<div class="to">${to}</div>
												<div class="balance">${value} ${transfer.transfers[j].contract_ticker_symbol}</div>
											</div>
										 `
									}

								}
								
								
							}
						}
						else{
							tokenList.innerHTML = `
							<center><br><h2>No token transfers</h2></center>
							`;
						}

					}
				})();
			}

		})();


	</script>

		
</body>
	
</html>