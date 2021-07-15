<?php 

ULogin(1);

Head("| Market");

$current_wallet = $_SESSION['USER_MAIN_WALLET'];


?>

<body>

	<main>
		<div class="container">
			<?php HeaderBlock();?>

			<div id="content">

				<aside class="sidebar">
					<?php Menu(5); ?>
				</aside>

				<aside class="content">
					<h1>Market</h1>

					<div id="prices" >

						<center style=" width: 100%; ">
							<div class="lds-dual-ring" style=" height: 30px; margin: 0px auto; text-align: center; width: 48px; margin-top: 13px;"></div>
						<center>

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
		const prices = document.getElementById("prices");

		;(async () => {

			// Token Prices
			;(async () => {
				var tokenPrices = await getTokenPrices();

				prices.innerHTML = "";

				for (var i = 0; i < tokenPrices.tokens.length; i++) {
					prices.innerHTML += `
						<div class="block">
							<div style=" width: 100%; ">
								<div class="logo_title">
									<img src="${tokenPrices.tokens[i].logo_url}">
									<a target="_blank" href="https://explorer.rsk.co/address/${tokenPrices.tokens[i].asset}">
										<h3>${tokenPrices.tokens[i].assetName} price:</h3>
										<i class="fas fa-external-link-alt"></i>
									</a>
									
								</div>
								
								<div >
									<span style="font-weight: 600;font-size: 27px;">
										<span>$${tokenPrices.tokens[i].price_USD.toFixed(5)}</span>
									</span>
								</div>
							</div>
							
						</div>
				  	`;
				}
				

			})();

		})();

	</script>

		
</body>
	
</html>