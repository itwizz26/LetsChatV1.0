<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Comments, chats, live chats, forums, LetsChat">
	
	<title>LetsChat &reg; - Say it like it is!</title>
	<link rel="shortcut icon" href="../../public/assets/images/favicon.png">
	
	<!-- Bootstrap -->
	<link href="../../public/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<!-- Custom styles -->
	<link href="../../public/assets/css/main.css" rel="stylesheet" type="text/css">
	<!-- Fonts -->
	<link href="../../public/assets/css/font-awesome-4.6.3/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link href="../../public/assets/css/WireOneFont.css" rel="stylesheet" type="text/css">
</head>

<body class="theme-invert">
	<!-- Main (register) section -->
	<section class="section" id="head">
		<div class="container">
			<div class="row">
				<div class="col-md-10 col-lg-10 col-md-offset-1 col-lg-offset-1 text-center">	
					<!-- Site Title -->
					<h2 class="subtitle">Welcome, please enter your details below.</h2>
					
				</div> <!-- /col -->
			</div> <!-- /row -->
			
			<div class="row">
				<div class="col-sm-8 col-sm-offset-2">    
					<form id="formSignup" method="POST" class="form-horizontal" role="form">
						<input type="hidden" name="mode" id="mode" value="signup" />
						
						<div class="form-group">
							<div class="col-xs-8 col-md-offset-2">
								<input type="email" id="username" name="username" class="form-control" placeholder="Username..." />
							</div>
						</div>
						<div class="form-group">
							<div class="col-xs-8 col-md-offset-2">
								<input type="password" id="password" name="password" class="form-control" placeholder="Password..." />
							</div>
						</div>
						<div class="form-group">
							<div class="col-xs-8 col-md-offset-2">
								<input type="password" id="retypepass" name="retypepass" class="form-control" placeholder="Retype password..." />
							</div>
						</div>
						
						<div class="form-group form-actions">
							<div class="col-xs-2 col-md-offset-2">
								<label class="csscheckbox csscheckbox-primary">
									<input type="radio" id="gender" name="gender" value="M" />
									<span></span>
								</label> Male
							</div>
							<div class="col-xs-2 text-right">
								<label class="csscheckbox csscheckbox-primary">
									<input type="radio" id="gender" name="gender" value="F" />
									<span></span>
								</label> Female
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-xs-8 col-md-offset-2">
								<input type="text" id="firstname" name="firstname" class="form-control" placeholder="Firstname..." />
							</div>
						</div>
						<div class="form-group">
							<div class="col-xs-8 col-md-offset-2">
								<input type="text" id="lastname" name="lastname" class="form-control" placeholder="Lastname..." />
							</div>
						</div>
						
						<div class="form-group form-actions">
							<div class="col-xs-1 col-md-offset-2">
								<button type="submit" class="btn btn-effect-ripple btn-sm btn-primary" id="signup"><i class="fa fa-user"></i> Sign up</button>
							</div>
							<div class="col-xs-7 text-right">
								<label class="csscheckbox csscheckbox-primary">
									<input type="checkbox" id="signinBot" name="signinBot" value="1" />
									<span></span>
								</label>
								I'm not a bot
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
	<!-- Page footer -->
	<footer>
		<p class="text-center small">Copyright &copy; 2016 | Version 1.0</p>
	</footer>
	
	<!-- Load js libs only when the page is loaded. -->
	<script src="../../public/assets/js/jquery.min.js"></script>
	<script src="../../public/assets/js/bootstrap.min.js"></script>
	<script src="../../public/assets/js/modernizr.custom.72241.js"></script>
	<!-- Custom template scripts -->
	<script src="../../public/assets/js/main.js"></script>
	<script src="../../public/assets/js/toaster.js"></script>
	<!-- Do Error check -->
	<script type="text/javascript">
		jQuery(document).ready (function($) {
			
			// Signup user
			$('.btn-primary').click(function(e) {
				// Disable browser defaults events
				e.preventDefault();
				
				// Get form inputs
				var username = $("#username").val();
				var password = $("#password").val();
				var retypepass = $("#retypepass").val();
				var regx = /^[A-Za-z0-9]+$/;
				
				// Check uname
				if (username <= 0)
				{
					ShowMessage ("danger", "Error", "Please enter a username.");
					exit();
				}
				else if (!regx.test (username))
				{
					// Check if alphanumeric
					ShowMessage ("danger", "Error", "Only alphanumeric values allowed.");
					exit();
				}
				else if (username.length < 3)
				{
					ShowMessage ("danger", "Error", "Username must be 3 characters or more.");
					exit();
				}
				
				// Check passwords
				if (password <= 0)
				{
					ShowMessage ("danger", "Error", "Please specify your password.");
					exit();
				}
				else if (password != retypepass)
				{
					ShowMessage ("danger", "Error", "Your passwords don't match!");
					exit();
				}
				else if (password.length < 4)
				{
					// Too short
					ShowMessage ("danger", "Error", "Password too short! Must be at least 4 characters long.");
					exit();
				}
				
				// Get bot checkbox
				var checked = $("#formSignup input[name='signinBot']:checked").length;
				
				// Check if permissions are selected
				if (checked <= 0)
				{
					ShowMessage ("danger", "Error", "Please confirm that you're not a web robbot.");
					exit();
				}
				
				// Get all form data
				formSigninData = $("#formSignup").serialize();
				
				$.ajax({
					type:'POST',
					data:formSigninData,
					url:'../../app/core/Modes.php',
					success:function (signinResults) {
						// Get jason results
						result = JSON.parse (signinResults);						
						
						// Check login result
						if (result == 1)
						{
							// User exists
							ShowMessage ("danger", "Error", "This username is already taken! Please try another one.");
							exit;
						}
						else if (result == 0) {
							// Session not started
							ShowMessage ("danger", "Error", "Your session could not be started! Go to the sign in screen and login from there.");
							exit;
						}
						else
						{
							// Show message
							ShowMessage ("success", "Success", "User has been successfully created. Logging in.");
							setTimeout(function(){
								// Reload after 3 seconds
								window.location.replace ("http://localhost/letschat/public/forum/" + result);
							}, 3000);
						}
					}
				});
			});
		});
	</script>
</body>
</html>