<?php ULogin(0);?>
<?php Head("| Login");?>
<body>
	<div class="login_page block" style="padding: 50px; width: 500px;">
		<center>
			<img src="/resource/images/rsk_logo_white.svg" alt="Logo" style=" width: 200px; ">
			<h1>Login</h1>
		</center>
		<form action="/user_functions/login" method="POST">
			<label>Email:</label><br>
			<input type="email" name="email" required style="width: 100%; margin-top: 10px;"><br><br>
			<label>Password:</label><br>
			<input type="password" name="password" required style="width: 100%; margin-top: 10px;"><br><br>
			<input class="btn" type="submit" name="go_login" value="Login" style=" width: 100%; "><br><br>
		</form>

		<center>
			<p style="color: #888;">Don't have an account? <a href="/register" style="font-weight: 600; "><u>Create</u></a></p>
		</center>
	</div>


	<div class="notif-block" id="notif-block">		
		<?php MessageShow(); ?>
	</div>

	<?php Footer();?>

</body>
</html>