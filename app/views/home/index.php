<?php
	// Force home page to be: http://localhost/letschat/public/
	if ($_SERVER['REQUEST_URI'] == "/letschat/public/home/" || $_SERVER['REQUEST_URI'] == "/letschat/public/home/index.php")
	{
		// Redirect to public
		header ("location: http://localhost/letschat/public/");
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Comments, chats, live chats, forums, LetsChat">
	
	<title>LetsChat &reg; - Say it like it is!</title>
	<link rel="shortcut icon" href="assets/images/favicon.png">
	
	<!-- Bootstrap -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<!-- Custom styles -->
	<link href="assets/css/main.css" rel="stylesheet" type="text/css">
	<!-- Fonts -->
	<link href="assets/css/font-awesome-4.6.3/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/WireOneFont.css" rel="stylesheet" type="text/css">
</head>

<body class="theme-invert">
	<!-- Main (Home) section -->
	<section class="section" id="head">
		<div class="container">
			<div class="row">
				<div class="col-md-10 col-lg-10 col-md-offset-1 col-lg-offset-1 text-center">	
					<!-- Site Title -->
					<h1 class="title">LetsChat <sup>&reg;</sup></h1>
					<h2 class="subtitle">Welcome, please sign in/sign up below.</h2>
					
				</div> <!-- /col -->
			</div> <!-- /row -->
			
			<div class="row">
				<div class="col-md-10 col-lg-10 col-md-offset-1 col-lg-offset-1 text-center">
					<form id="formLogin" method="POST" class="form-horizontal" role="form">
						<input type="hidden" name="mode" id="mode" value="signin" />
						
						<div class="form-group">
							<div class="col-xs-8 col-md-offset-2">
								<input type="email" id="loginEmail" name="loginEmail" class="form-control has-success" placeholder="Your username..." />
							</div>
						</div>
						<div class="form-group">
							<div class="col-xs-8 col-md-offset-2">
								<input type="password" id="loginPassword" name="loginPassword" class="form-control" placeholder="Your password..." />
							</div>
						</div>
						<div class="form-group form-actions">
							<div class="col-xs-1 col-md-offset-2">
								<button type="submit" class="btn btn-effect-ripple btn-sm btn-primary" id="signin"><i class="fa fa-sign-in"></i> Sign in</button>
							</div>
							<div class="col-xs-7 text-right">
								<label class="csscheckbox csscheckbox-primary">
									<input type="checkbox" id="loginBot" name="loginBot" value="1" />
									<span></span>
								</label>
								I'm not a webbot | <a href="http://localhost/letschat/public/signup/">Sign up</a>
							</div>
						</div>
					</form>
				</div> <!-- /col -->
			</div> <!-- /row -->
		</div>
	</section>
	
	<!-- Page footer -->
	<footer>
		<p class="text-center small">Copyright &copy; 2016 | Version 1.0</p>
	</footer>
	
	<!-- Load js libs only when the page is loaded. -->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>
	<script src="assets/js/modernizr.custom.72241.js"></script>
	<!-- Custom template scripts -->
	<script src="assets/js/main.js"></script>
	<script src="assets/js/toaster.js"></script>
	<!-- Do Error check -->
	<script type="text/javascript">
		jQuery(document).ready (function() {
			
			// Login user
			$('#signin').click(function(e) {
				// Disable browser defaults events
				e.preventDefault();
				
				// Get form inputs
				var loginEmail = $("#loginEmail").val();
				var loginPassword = $("#loginPassword").val();
				
				// Check uname
				if (loginEmail <= 0)
				{
					ShowMessage ("danger", "Error", "Please enter your email address.");
					exit();
				}
				
				if (loginPassword <= 0)
				{
					ShowMessage ("danger", "Error", "Please specify your password.");
					exit();
				}
				
				// Get bot checkbox
				var checked = $("#formLogin input[name='loginBot']:checked").length;
				
				// Check if permissions are selected
				if (checked <= 0)
				{
					ShowMessage ("danger", "Error", "Please confirm that you're not a web robbot.");
					exit();
				}
				
				// Get all form data
				formLoginData = $("#formLogin").serialize();
				
				$.ajax({
					type:'POST',
					data:formLoginData,
					url:'../app/core/Modes.php',
					success:function (loginResults) {
						// Get jason results
						result = JSON.parse (loginResults);						
						
						// Check login result
						if (result == 0)
						{
							// User doesn't exist
							ShowMessage ("danger", "Error", "This user doesn't exist! Please sign up first.");
							exit;
						}
						else if (result == 1)
						{
							// Password mismatch
							ShowMessage ("danger", "Error", "Your password is incorrect!");
							exit;
						}
						else
						{
							// Show message
							ShowMessage ("success", "Logging in", "You have been successfully logged in.");
							window.location.replace ("http://localhost/letschat/public/forum/" + result);
						}
					}
				});
			});
		});
	</script>
</body>
</html>