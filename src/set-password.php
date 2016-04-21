<!DOCTYPE html>
<html lang="en" class="set-password">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<title>Code Editor - Set Password</title>
		<noscript>
			<?php
				if(isset($_POST['password'])){
					file_put_contents('./editor.config.php',"<?php\n\$PASSWORD=md5(".escapeshellarg($_POST['password']).");\n?>",FILE_APPEND);
					header('Location: '.$_SERVER['SCRIPT_NAME']);
				}
			?>
		</noscript>
		<link rel="stylesheet" href="//cdn.jsdelivr.net/bootstrap/3.3.6/css/bootstrap.min.css">
		<style>
		.set-password body {
			margin-top: 30px;
			font-size: 13px;
		}
		
		.set-password .container {
			max-width: 380px;
		}
		</style>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
		<script>
		$(function()
		{
			$('button').click(function()
			{
				document.cookie = "editor-auth=" + document.forms[0].password.value + ";path=" + window.location.pathname + ";max-age=315360000" + (document.location.protocol === "http:" ? "" : ";secure");
			});
		});
		</script>
	</head>
	<body>
		<div class="container">
			<form role="form" method="post">
				<div class="form-group">
					<p class="help-block">
						This is the first time you have logged in and no password has been set. Create a new password below.
					</p>
					<label for="exampleInputPassword1">New password</label>
					<input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name="password" autofocus required>
				</div>
				<!--
				<div class="form-group">
					<label for="exampleInputPassword1">Confirm password</label>
					<input type="password" class="form-control" id="exampleInputPassword2" placeholder="Confirm Password" name="passwordConfirm" required>
				</div>
				-->
				<button type="submit" class="btn btn-default">Submit</button>
			</form>
		</div>
	</body>
</html>