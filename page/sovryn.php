<?php 

ULogin(1);

Head("| Sovryn stats - TVL");

$current_wallet = $_SESSION['USER_MAIN_WALLET'];


?>

<body>

	<main>
		<div class="container">
			<?php HeaderBlock();?>

			<div id="content">

				<aside class="sidebar">
					<?php Menu(6); ?>
				</aside>

				<aside class="content">
					<h1>Sovryn stats - TVL</h1>

					<div style=" display: flex; align-items: flex-start;     flex-wrap: wrap; ">
						<div class="block" style="   width: 48%; margin-bottom: 14px; margin-top: 14px; margin-right: 30px; ">
							<h3>Total TVL:</h3>
							<div id="total_tvl" >
								<center>
									<div class="lds-dual-ring" style=" height: 30px; margin: 0px auto; text-align: center; width: 48px; "></div>
								<center>
							</div>
							
						</div>
					</div>


					<div style=" display: flex; align-items: flex-start;     flex-wrap: wrap; justify-content: space-between; ">
						<div class="block" style="     width: 48%; margin-bottom: 14px; margin-top: 14px; margin-right: 30px; ">
							<h3>Protocol Contract:</h3>

							<div id="protocol">
								<center>
									<div class="lds-dual-ring" style=" height: 30px; margin: 0px auto; text-align: center; width: 48px; "></div>
								<center>
							</div>
						</div>

						<div class="block" style="     width: 48%; margin-bottom: 14px; margin-top: 14px; margin-top: 13px;">
							<h3>Lending Contract:</h3>

							<div id="lending">
								<center>
									<div class="lds-dual-ring" style=" height: 30px; margin: 0px auto; text-align: center; width: 48px; "></div>
								<center>
							</div>
						</div>

						<div class="block" style="    width: 48%; margin-bottom: 14px; margin-top: 14px;   margin-top: 13px;">
							<h3>Amm Contract:</h3>

							<div id="amm">
								<center>
									<div class="lds-dual-ring" style=" height: 30px; margin: 0px auto; text-align: center; width: 48px; "></div>
								<center>
							</div>
						</div>

						<div class="block" style="     width: 48%; margin-bottom: 14px; margin-top: 14px;  margin-top: 13px;">
							<h3>Bitocracy Staking Contract</h3>

							<div id="staking">
								<center>
									<div class="lds-dual-ring" style=" height: 30px; margin: 0px auto; text-align: center; width: 48px; "></div>
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


	<script>
		const total_tvl = document.getElementById("total_tvl");
		const protocol = document.getElementById("protocol");
		const lending = document.getElementById("lending");
		const amm = document.getElementById("amm");
		const staking = document.getElementById("staking");


		let total = 0;
		let count_total = 0;

		// TVL stats
		;(async () => {
			;(async () => {
				var staking_TVL = await tvlStaking(<?=chainMainnet?>, "<?=APIKEY;?>");
				staking.innerHTML = ` 
				<span style="font-weight: 600;font-size: 27px;">
					<span>$${staking_TVL.balance_usd.toFixed(3)}</span>
				</span>`;
				total += staking_TVL.balance_usd;
				count_total++;
				if(count_total == 4){
					total_tvl.innerHTML = ` 
					<span style="font-weight: 600;font-size: 27px;">
						<span>$${total.toFixed(3)}</span>
					</span>`;
				}
			})();

			;(async () => {
				var protocol_TVL = await tvlProtocol(<?=chainMainnet?>, "<?=APIKEY;?>");
				protocol.innerHTML = ` 
				<span style="font-weight: 600;font-size: 27px;">
					<span>$${protocol_TVL.balance_usd.toFixed(3)}</span>
				</span>`;
				total += protocol_TVL.balance_usd;
				count_total++;
				if(count_total == 4){
					total_tvl.innerHTML = ` 
					<span style="font-weight: 600;font-size: 27px;">
						<span>$${total.toFixed(3)}</span>
					</span>`;
				}
			})();

			;(async () => {
				var lending_TVL = await tvlLending(<?=chainMainnet?>, "<?=APIKEY;?>");
				lending.innerHTML = ` 
				<span style="font-weight: 600;font-size: 27px;">
					<span>$${lending_TVL.balance_usd.toFixed(3)}</span>
				</span>`;
				total += lending_TVL.balance_usd;		
				count_total++;
				if(count_total == 4){
					total_tvl.innerHTML = ` 
					<span style="font-weight: 600;font-size: 27px;">
						<span>$${total.toFixed(3)}</span>
					</span>`;
				}
			})();

			;(async () => {
				var amm_TVL = await tvlAmm(<?=chainMainnet?>, "<?=APIKEY;?>");
				amm.innerHTML = ` 
				<span style="font-weight: 600;font-size: 27px;">
					<span>$${amm_TVL.balance_usd.toFixed(3)}</span>
				</span>`;
				total += amm_TVL.balance_usd;
				count_total++;
				if(count_total == 4){
					total_tvl.innerHTML = ` 
					<span style="font-weight: 600;font-size: 27px;">
						<span>$${total.toFixed(3)}</span>
					</span>`;
				}
			})();

		})();

	</script>

		
</body>
	
</html>