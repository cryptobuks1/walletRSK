<?php ULogin(0);?>
<?php Head("| Register");?>
<body>

	<div class="login_page block" style="padding: 50px; width: 500px;">
		<center>
			<img src="/resource/images/rsk_logo_white.svg" alt="Logo" style=" width: 200px; ">
			<h1>Registration</h1>
		</center>
		<form action="/user_functions/register" method="POST">
			<label>Email:</label><br>
			<input type="email" name="email" required style="width: 100%; margin-top: 10px;"><br><br>
			<label>RSK wallet address:</label><br>
			<input type="text" name="wallet" title="EMV-compatible wallet address" pattern="0x([A-Fa-f0-9]{40})" required style="width: 100%; margin-top: 10px;"><br><br>
			<label>Password:</label><br>
			<input type="password" name="password" required style="width: 100%; margin-top: 10px;"><br><br>
			<input class="btn" type="submit" name="go_register" value="Register" style=" width: 100%; "><br><br>
		</form>

		<center>
			<p style="color: #888;">Already have an account?&nbsp;<a href="login" style="font-weight: 600; "><u>Login</u></a></p>
		</center>
	</div>

	<div class="notif-block" id="notif-block">		
		<?php MessageShow(); ?>
	</div>

	<?php Footer();?>

</body>
</html>