<?php 

ULogin(1);

Head("| Dashboard");

$current_wallet = $_SESSION['USER_MAIN_WALLET'];


?>

<body>

	<main>
		<div class="container">
			<?php HeaderBlock();?>

			<div id="content">

				<aside class="sidebar">
					<?php Menu(1); ?>
				</aside>

				<aside class="content">
					<h1>Dashboard</h1>

					<div style=" display: flex; align-items: flex-start;     flex-wrap: wrap; ">
						<div id="balance_rbtc" class="block" style="display: flex; min-width: 209px; margin-bottom: 14px; margin-top: 14px; margin-right: 30px; min-height: 124px;">
							<div>
								<h3>RBTC Balance:</h3>
								<div >
									<center>
										<div class="lds-dual-ring" style=" height: 30px; margin: 0px auto; text-align: center; width: 48px; margin-top: 13px;"></div>
									<center>
								</div>
							</div>
							
						</div>
					</div>


					<div style=" display: flex; align-items: flex-start;     flex-wrap: wrap; justify-content: space-between; ">
						<div class="block" style=" min-width: 209px; margin-bottom: 14px; margin-top: 14px;  min-height: 124px; margin-right: 30px; ">
							<h3>Total RBTC fees:</h3>

							<div id="fee_rbtc">
								<center>
									<div class="lds-dual-ring" style=" height: 30px; margin: 0px auto; text-align: center; width: 48px; margin-top: 13px;"></div>
								<center>
							</div>
						</div>

						<div class="block" style=" min-width: 209px; margin-bottom: 14px; margin-top: 14px;  min-height: 124px; margin-right: 30px; margin-top: 13px;">
							<h3>RBTC Income:</h3>

							<div id="rbtc_income">
								<center>
									<div class="lds-dual-ring" style=" height: 30px; margin: 0px auto; text-align: center; width: 48px; margin-top: 13px;"></div>
								<center>
							</div>
						</div>

						<div class="block" style=" min-width: 209px; margin-bottom: 14px; margin-top: 14px;  min-height: 124px;  margin-top: 13px;">
							<h3>RBTC Expenses:</h3>

							<div id="rbtc_expenses">
								<center>
									<div class="lds-dual-ring" style=" height: 30px; margin: 0px auto; text-align: center; width: 48px; margin-top: 13px;"></div>
								<center>
							</div>
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




	<!-- <script>
		const modal = document.getElementsByClassName("modal")[0];
		const modal__content = document.getElementsByClassName("modal__content")[0];
		const addWallet = document.getElementById("addWallet");

		addWallet.addEventListener("click", ()=>{
			modal__content.innerHTML = `
				<center>
					<h2 style=" font-weight: 600; ">Add new wallet</h2><br>
				</center>

				<form action="/user_functions/addWallet" method="POST">
					<label>RSK wallet address:</label><br>
					<input type="text" name="wallet" title="EMV-compatible wallet address" pattern="0x([A-Fa-f0-9]{40})" required style="width: 100%; margin-top: 10px;"><br><br>
					<input class="btn" type="submit" name="add_wallet" value="Add wallet" style=" width: 100%; ">
				</form>
				
			 `;
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

	</script>
 -->

	<script>

		const balance_rbtc = document.getElementById("balance_rbtc");
		const fee_rbtc = document.getElementById("fee_rbtc");
		const rbtc_income = document.getElementById("rbtc_income");
		const rbtc_expenses = document.getElementById("rbtc_expenses");


		;(async () => {
			const priceBTC = await getCoinPrice("btc", "<?=APIKEY;?>");

			// Balance
			;(async () => {
				var result = await getBalanceRBTC(<?=chainMainnet?>, "<?php echo $current_wallet; ?>", "<?=APIKEY;?>");

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
							<h3>RBTC Balance:</h3>
							<div >
								<span style="font-weight: 600;font-size: 27px;">
									<span>${RBTC_balance.toFixed(8)}</span>
									<h3>≈ $${(priceBTC * RBTC_balance).toFixed(3)}</h3>
								</span>
							</div>
						</div>
						
						<div style=" margin-left: 36px; display: flex; flex-direction: column; justify-content: space-between; ">
							<div class="btn" id="send_btn" style=" padding: 5px 22px; ">Send</div>
							<div class="btn" id="receive_btn" style=" padding: 5px 22px; ">Receive</div>
						</div>
				  	`;


				  	const send_btn = document.getElementById("send_btn");
				  	const receive_btn = document.getElementById("receive_btn");

				  	send_btn.addEventListener("click", ()=>{
				  		modal__content.innerHTML = `
					      <center>
					         <h2 style=" font-weight: 600; ">Send RBTC</h2><br>
					      </center>
					      	<label>Destination address:</label>
							<input id="yourAddress" placeholder="Destination address" type="text" title="EMV-compatible wallet address" pattern="0x([A-Fa-f0-9]{40})" style="width: 100%; margin-top: 10px; "><br><br>

							<label>Amount (RBTC):</label>
							<input id="amount" placeholder="Amount" value="0.0005" type="number" min="0.0005" max="${RBTC_balance.toFixed(8)}" title="Amount of RBTC. Maximum: ${RBTC_balance.toFixed(8)}" pattern="([0-9]+)" style="width: 100%; margin-top: 10px; ">
							<div id="error" style="margin-top: 20px;color:red;text-align: center;"></div>
							<div class="btn" id="sendConfirm" style=" width: 100%; margin-top: 20px; ">Send</div>
					    `;

					    document.getElementById("sendConfirm").addEventListener("click", ()=>{
					    	if(/^0x[A-Fa-f0-9]{40}$/.test(document.getElementById("yourAddress").value) == false){
					    		document.getElementById("error").innerHTML = `<h3>Put a valid address</h3>`;
					    		document.getElementById("yourAddress").focus();
					    	}

					    	else if(parseFloat(document.getElementById("amount").value) > RBTC_balance){
					    		document.getElementById("error").innerHTML = `<h3>You can send maximum ${RBTC_balance.toFixed(8)} RBTC</h3>`;
					    		document.getElementById("amount").focus();
					    	} else{

						    	modal__content.innerHTML = `
						    	<center>
						         <h2 style=" font-weight: 600; ">Sended !</h2>
						      	</center>
						       `
					    	}
					    })

					    modal.classList.remove("hidden-element");
					   	document.body.style = "overflow: hidden";
				  	})

				  	receive_btn.addEventListener("click", ()=>{
				  		modal__content.innerHTML = `
					      <center>
					         <h2 style=" font-weight: 600; ">Receive RBTC</h2><br>
					      </center>
					      	<label>Your address:</label>
							<div style="display:flex; justify-content: space-between; align-items: center">
								<input id="yourAddress" type="text" value="<?=$current_wallet?>" readonly style="width: 100%; margin-top: 10px; color:#888;">
								<i id="copy-btn" class="copy-btn far fa-copy"></i>
							</div>

							<div id="qrcode"></div>
					    `;

					    modal.classList.remove("hidden-element");
					   	document.body.style = "overflow: hidden";

					    new QRCode(document.getElementById("qrcode"), "<?=$current_wallet?>");

					    document.getElementById("copy-btn").addEventListener("click", ()=>{
					    	var copyText = document.querySelector("#yourAddress");
					    	copyText.select();
  							document.execCommand("copy");
					    })

					   	
				  	})


				}
			})();


			// Fee + income + outcome
			;(async () => {
				var result = await getFeeRBTC(<?=chainMainnet?>, "<?php echo $current_wallet; ?>", "<?=APIKEY;?>");

				if(result.error){
					fee_rbtc.innerHTML = `
						<span style="font-weight: 600;font-size: 27px;">
							<h3>error</h3>
						</span>
				  	`;

				  	rbtc_income.innerHTML = `
						<span style="font-weight: 600;font-size: 27px;">
							<h3>error</h3>
						</span>
				  	`;

				  	rbtc_expenses.innerHTML = `
						<span style="font-weight: 600;font-size: 27px;">
							<h3>error</h3>
						</span>
				  	`;
				} 
				else{
					fee_rbtc.innerHTML = `
						<span style="font-weight: 600;font-size: 27px;">
							<span>${result.feeTotal.toFixed(9)}</span>
							<h3>≈ $${(priceBTC * result.feeTotal).toFixed(3)}</h3>
						</span>
				  	`;

				  	rbtc_income.innerHTML = `
						<span style="font-weight: 600;font-size: 27px;">
							<span>${result.incomeTotal.toFixed(9)}</span>
							<h3>≈ $${(priceBTC * result.incomeTotal).toFixed(3)}</h3>
						</span>
				  	`;

				  	rbtc_expenses.innerHTML = `
						<span style="font-weight: 600;font-size: 27px;">
							<span>${result.expensesTotal.toFixed(9)}</span>
							<h3>≈ $${(priceBTC * result.expensesTotal).toFixed(3)}</h3>
						</span>
				  	`;
				}


			})();

		})();

	</script>

		
</body>
	
</html>