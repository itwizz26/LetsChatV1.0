<?php
	// Include Chat Class
	require_once ("../app/models/Chat.php");
	$liveChat = new Chat;
	
	// Get the latest active session for this user
	$getSession = $liveChat->get ("sessions", ["memberId", "=", $data['memberId']], " ORDER BY `date_ended` DESC");
	
	// Get session details
	foreach ($getSession['results'] as $results)
	{
		// Set auth sessionId
		$authSession = $results->sessionId;
		$dateEnded = $results->date_ended;
	}
	
	// If no session set or session ended, exit screen
	if ($getSession['count'] == 0)
	{
		// Exit
		header ("location: http://localhost/letschat/public/");
	}
	
	// If session was found, get the user details
	$getAuthUser = $liveChat->get ("members", ["memberId", "=", $data['memberId']]);
	
	// Get user bio
	foreach ($getAuthUser['results'] as $results)
	{
		// Full name
		$authFullName = ($results->name && $results->surname) ? $results->name . " " . $results->surname : $results->name;
		
		// Bio details
		$authUser = $results->username;
		$authPass = $results->pass;
		$authName = $results->name;
		$authLastName = $results->surname;
		$authGender = $results->gender;
		
		// UserId
		$authId = $results->memberId;
	}
	
	// Get all comments: TO DO in V2.0 - get only article specific chats
	$getComments = $liveChat->get ("comments", ["memberId", "!=", ""]);
?>

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
	<nav class="mainmenu">
		<div class="container">
			<div class="col-md-8 dropdown">
				<button type="button" class="navbar-toggle" data-toggle="dropdown"><span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
				<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
					<li><a href="#head" class="active"><i class="fa fa-comments"></i> Forum</a></li>
					<li><a href="#profile"><i class="fa fa-edit"></i> Edit profile</a></li>
					<li><a href="#signout"><i class="fa fa-sign-out"></i> Sign out</a></li>
				</ul>
			</div>
			<div class="col-md-4 text-right small">
<?php
			// If there is a name
			if ($authFullName) {
?>				
				Hello, <span class="bold"><?php echo $authFullName; ?></span><br />
<?php
			}
?>
				You are logged in as: <span class="bold"><?php echo $authUser; ?></span>
			</div>
		</div>
	</nav>
	
	<!-- First (Forum) section -->
	<section class="section" id="head">
		<div class="container">
			<h2 class="text-center title">Article</h2>
			<div class="row">
				<div class="col-sm-4 col-sm-offset-2">    
					<h5><strong>Lorem ipsum<br></strong></h5>
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorum, ullam, ducimus, eaque, ex autem est dolore illo similique quasi unde sint rerum magnam quod amet iste dolorem ad laudantium molestias enim quibusdam inventore totam fugit eum iusto ratione alias deleniti suscipit modi quis nostrum veniam fugiat debitis officiis impedit ipsum natus ipsa. Doloremque, id, at, corporis, libero laborum architecto mollitia molestiae maxime aut deserunt sed perspiciatis quibusdam praesentium consectetur in sint impedit voluptates! Deleniti, sequi voluptate recusandae facere nostrum?</p>    
				</div>
				<div class="col-sm-4">
					<h5><strong>More, more lipsum!<br></strong></h5>    
					<p>Tempore, eos, voluptatem minus commodi error aut eaque neque consequuntur optio nesciunt quod quibusdam. Ipsum, voluptatibus, totam, modi perspiciatis repudiandae odio ad possimus molestias culpa optio eaque itaque dicta quod cupiditate reiciendis illo illum aspernatur ducimus praesentium quae porro alias repellat quasi cum fugiat accusamus molestiae exercitationem amet fugit sint eligendi omnis adipisci corrupti. Aspernatur.</p>    
					<h5><strong>About the author<br></strong></h5>    
					<p><a href="javascript: voud(0)">Author Name</a></p>
				</div>
			</div>
			<h2 class="text-center title">Share your thoughts</h2>
			<div class="row">
				<div class="col-sm-12">
					<form id="formComment" method="POST" class="form-horizontal">
						<input type="hidden" name="mode" id="mode" value="comment" />
						<input type="hidden" name="memberId" id="memberId" value="<?php echo $authId; ?>" />
						
						<div class="form-group form-actions">
							<div class="col-xs-8 col-xs-offset-2">
								<textarea name="comment" id="comment" class="form-control" placeholder="Type your comment..."></textarea>
							</div>
						</div>
						
						<div class="form-group form-actions">
							<div class="col-xs-2 col-md-offset-2 text-left">
								<button type="submit" class="btn btn-effect-ripple btn-sm btn-primary" id="sendComment"><i class="fa fa-check"></i> Send</button>
							</div>
							<div class="col-xs-6 text-right">
								<label class="csscheckbox csscheckbox-primary">
									<input type="checkbox" id="commentBot" name="commentBot" value="1" />
									<span></span>
								</label>
								I'm not a webbot
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="container">
			<h2 class="text-center title">Comments</h2>
			<h4 class="col-sm-offset-1 small">Total: <?php echo $getComments['count']; ?> comments <a href="javascript: void (0);" id="refresh" title="Refresh chats"><i class="fa fa-refresh"></i></a></h4>
<?php
			// Check if comments
			if ($getComments['count'] > 0)
			{
				foreach ($getComments['results'] as $comment)
				{
					// Get this user's bio info
					$getThisMember = $liveChat->get ("members", ["memberId", "=", $comment->memberId]);
					
					//loop through user's details
					foreach ($getThisMember['results'] as $member)
					{
						// Set fullname
						$fullName = ($member->name) ? $member->name . " " . $member->surname : $member->username;
						$gender = ($member->gender == "M") ? '<i class="fa fa-male"></i> ' : '<i class="fa fa-female"></i> ';
					}
?>
					<div class="row">
						<div class="col-sm-10 col-md-offset-1">
							<div class="thumbnail">
								<p class="small"><?php echo $gender; ?> <span class="bold"><?php echo ucwords ($fullName); ?></span> said:</p>
								<p><?php echo $comment->comment; ?></p>
								<span class="small"><em>At <?php echo date ("H:ia, \o\\n l \\t\h\\e jS, M Y", $comment->date_created); ?></em></span>
								<hr />
								<p>
<?php
									// Only owners can remove comments
									if ($authId == $comment->memberId) {
?>
										<a href="javascript: void (0)" class="btn btn-danger" role="button" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $comment->commentId; ?>"><i class="fa fa-trash"></i> Remove</a>
<?php
									}
									
									// Don't show reply for owner
									if ($authId != $comment->memberId) {
?>
										<a href="javascript: void (0)" class="btn btn-warning" role="button" data-toggle="modal" data-target="#replyModal" data-id="<?php echo $comment->commentId, ",", $authId; ?>"><i class="fa fa-reply"></i> Reply</a>
<?php
									}
?>
								</p>
							</div>
						</div>
					</div>
<?php
					// Get replies
					$getReplies = $liveChat->get ("replies", ["commentId", "=", $comment->commentId]);
					
					// If replies found
					if ($getReplies['count'] >= 1)
					{
?>
						<div class="row">
							<h5 class="col-sm-9 col-sm-offset-2 small">Total: <?php echo $getReplies['count']; ?> replies</h5>
<?php
							foreach ($getReplies['results'] as $reply)
							{
								// Get the reply users bio
								$getReplyMember = $liveChat->get ("members", ["memberId", "=", $reply->memberId]);
								
								//loop through user's details
								foreach ($getReplyMember['results'] as $replyMember)
								{
									// Set fullname
									$replyFullName = ($replyMember->name) ? $replyMember->name . " " . $replyMember->surname : $replyMember->username;
									$replyGender = ($replyMember->gender == "M") ? '<i class="fa fa-male"></i> ' : '<i class="fa fa-female"></i> ';
								}
?>							
								<div class="col-sm-9 col-sm-offset-2">
									<div class="thumbnail warning">
										<p class="small"><?php echo $replyGender; ?> <span class="bold"><?php echo ucwords ($replyFullName); ?></span> replied:</p>
										<p><em><?php echo $reply->reply; ?></em></p>
										<span class="small"><em>At <?php echo date ("H:ia, \o\\n l \\t\h\\e jS, M Y", $reply->date_created); ?></em></span>
									</div>
								</div>
<?php
							}
?>
						</div>
<?php
					}
				}
			}
			else
			{
?>
				<h4 class="col-sm-offset-1">Be the first to comment.</h4>
<?php
			}
?>
		</div>
	</section>
	
	<!-- Second (Profile) section -->
	<section class="section" id="profile">
		<div class="container">
			<h2 class="text-center title">Update your details</h2>
			<div class="row">
				<div class="col-sm-12">
					<form id="formUpdate" method="POST" class="form-horizontal">
						<input type="hidden" name="mode" id="mode" value="update" />
						<input type="hidden" name="memberId" id="memberId" value="<?php echo $authId; ?>" />
						<input type="hidden" name="oldPass" id="oldPass" value="<?php echo $authPass; ?>" />
						
						<div class="form-group">
							<div class="col-xs-8 col-md-offset-2">
								<input type="email" id="username" name="username" class="form-control" value="<?php echo $authUser; ?>" placeholder="Username..." />
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-xs-8 col-md-offset-2">
								<input type="password" id="oldPassword" name="oldPassword" class="form-control" value="" placeholder="Old password..." />
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-xs-8 col-md-offset-2">
								<input type="password" id="newPassword" name="newPassword" class="form-control" value="" placeholder="New password..." />
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-xs-8 col-md-offset-2">
								<input type="text" id="firstname" name="firstname" class="form-control" value="<?php echo $authName; ?>" placeholder="Firstname..." />
							</div>
						</div>
						<div class="form-group">
							<div class="col-xs-8 col-md-offset-2">
								<input type="text" id="lastname" name="lastname" class="form-control" value="<?php echo $authLastName; ?>" placeholder="Lastname..." />
							</div>
						</div>
						
						<div class="form-group form-actions">
							<div class="col-xs-2 col-md-offset-2">
								<label class="csscheckbox csscheckbox-primary">
									<input type="radio" id="gender" name="gender" value="M" <?php echo ($authGender == "M") ? 'checked="checked"' : ""; ?> />
									<span></span>
								</label> Male
							</div>
							<div class="col-xs-2 text-right">
								<label class="csscheckbox csscheckbox-primary">
									<input type="radio" id="gender" name="gender" value="F" <?php echo ($authGender == "F") ? 'checked="checked"' : ""; ?> />
									<span></span>
								</label> Female
							</div>
						</div>
						
						<div class="form-group form-actions">
							<div class="col-xs-2 col-md-offset-2 text-left">
								<button type="submit" class="btn btn-effect-ripple btn-sm btn-primary" id="updateUser"><i class="fa fa-edit"></i> Edit</button>
							</div>
							<div class="col-xs-6 text-right">
								<label class="csscheckbox csscheckbox-primary">
									<input type="checkbox" id="updateBot" name="updateBot" value="1" />
									<span></span>
								</label>
								I'm not a webbot
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
	
	<!-- Third (Signout) section -->
	<section class="section" id="signout">
		<div class="container">
			<h3 class="text-center">Good Bye!</h3>
			<h2 class="text-center title">Thanks for the chat. Come back soon.</h2>
			<div class="row">
				<form id="formSignout" method="POST" class="form-horizontal">
					<input type="hidden" name="mode" id="mode" value="signout" />
					<input type="hidden" name="sessionId" id="sessionId" value="<?php echo $authSession; ?>" />
					
					<div class="form-group form-actions">
						<div class="col-xs-2 col-md-offset-2 text-left">
							<button type="submit" class="btn btn-effect-ripple btn-sm btn-primary" id="signoutProceed"><i class="fa fa-sign-out"></i> Proceed</button>
						</div>
						<div class="col-xs-6 text-right">
							<label class="csscheckbox csscheckbox-primary">
								<input type="checkbox" id="signoutBot" name="signoutBot" value="1">
								<span></span>
							</label>
							I'm not a webbot
						</div>
					</div>
				</form>
			</div>
		</div>
	</section>
	
	<!-- Page footer -->
	<footer>
		<p class="text-center small">Copyright &copy; 2016 | Version 1.0</p>
	</footer>
	
	<!-- Delete Modal -->
	<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Remove comment</h4>
				</div>
				<div class="modal-body">
					<form name="deleteCommentForm" id="deleteCommentForm" method="POST" class="form-horizontal">
						<input type="hidden" name="mode" id="mode" value="delete" />
						<input type="hidden" name="commentIdDelete" id="commentIdDelete" value="" />
						
						<p>You are about to permenantly remove this comment and it's replies. This action is irrevasible.</p>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-success" id="DeleteComment">Proceed</button>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Reply Modal -->
	<div class="modal fade" id="replyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Type reply</h4>
				</div>
				<div class="modal-body">
					<form name="replyCommentForm" id="replyCommentForm" method="POST" class="form-horizontal">
						<input type="hidden" name="mode" id="mode" value="reply" />
						<input type="hidden" name="commentIdReply" id="commentIdReply" value="" />
						
						<textarea name="commentReply" id="commentReply" class="form-control" placeholder="Type reply comment..."></textarea>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-success" id="SendReply">Send Reply</button>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Load js libs only when the page is loaded. -->
	<script src="../../public/assets/js/jquery.min.js"></script>
	<script src="../../public/assets/js/bootstrap.min.js"></script>
	<script src="../../public/assets/js/modernizr.custom.72241.js"></script>
	<!-- Custom template scripts -->
	<script src="../../public/assets/js/main.js"></script>
	<script src="../../public/assets/js/toaster.js"></script>
	<!-- Do Error check -->
	<script type="text/javascript">
		jQuery(document).ready (function() {
			// Refresh page after 3 seconds
			$(document).on("click", "#refresh", function () {
				setTimeout(function(){
					window.location.reload(true);
				}, 3000);
			});
			
			// Save comment
			$('#sendComment').click(function(e) {
				// Disable browser defaults events
				e.preventDefault();
				
				// Get form inputs
				var comment = $("#comment").val();
				var checked = $("#formComment input[name='commentBot']:checked").length;
				
				// Check uname
				if (comment <= 0)
				{
					ShowMessage ("danger", "Error", "Please enter a comment.");
					exit();
				}
				
				// Check if checkbox ticked
				if (checked <= 0)
				{
					ShowMessage ("danger", "Error", "Please confirm that you're not a web robbot.");
					exit();
				}
				
				// Get all form data
				formCommentData = $("#formComment").serialize();
				
				// Do ajax call
				$.ajax({
					type:'POST',
					data:formCommentData,
					url:'../../app/core/Modes.php',
					success:function (commentResults) {
						// Get jason results
						result = JSON.parse (commentResults);						
						
						// Check login result
						if (result == false)
						{
							// Message could not be saved
							ShowMessage ("danger", "Error", "This user doesn't exist! Please sign up first.");
							exit;
						}
						else
						{
							// Comment saved
							ShowMessage ("success", "Saved", "Your comment was successfully saved.");
							
							// Refresh comments after 3 seconds
							setTimeout(function(){
								window.location.reload(true);
							}, 3000);
						}
					}
				});
			});
			
			// Set commentId
			$(document).on("click", ".btn-danger", function () {
				var thisDeleteId = $(this).data("id");
				$("#commentIdDelete").val (thisDeleteId);
			});
			
			// Delete comment
			$('#DeleteComment').click(function(e) {
				// Disable browser defaults events
				e.preventDefault();
				
				// Get all form data
				deleteCommentFormData = $("#deleteCommentForm").serialize();
				
				// Do ajax call
				$.ajax({
					type:'POST',
					data:deleteCommentFormData,
					url:'../../app/core/Modes.php',
					success:function (deleteCommentResults) {
						// Get jason results
						result = JSON.parse (deleteCommentResults);
						
						// Check delete
						if (result == 0)
						{
							// Comment not deleted
							ShowMessage ("danger", "Error", "Comment could not be removed. Please try again.");
							exit;
						}
						else
						{
							// Comment saved
							ShowMessage ("success", "Removed", "Your comment was successfully removed.");
							
							// Refresh window after 3 seconds
							setTimeout(function(){
								window.location.reload(true);
							}, 3000);
						}
					}
				});
			});
			
			// Set comment reply Id
			$(document).on("click", ".btn-warning", function () {
				var thisReplyId = $(this).data("id");
				$("#commentIdReply").val (thisReplyId);
			});
			
			// Reply comment
			$('#SendReply').click(function(e) {
				// Disable browser defaults events
				e.preventDefault();
				
				// Get form inputs
				var commentReply = $("#commentReply").val();
				
				// Check uname
				if (commentReply <= 0)
				{
					ShowMessage ("danger", "Error", "Please enter a reply comment.");
					exit();
				}
				
				// Get all form data
				replyCommentFormData = $("#replyCommentForm").serialize();
				
				// Do ajax call
				$.ajax({
					type:'POST',
					data:replyCommentFormData,
					url:'../../app/core/Modes.php',
					success:function (replyCommentResults) {
						// Get jason results
						result = JSON.parse (replyCommentResults);						
						
						// Check if reply saved
						if (result == false)
						{
							// Message could not be saved
							ShowMessage ("danger", "Error", "Reply message was not saved! Please try again.");
							exit;
						}
						else
						{
							// Comment saved
							ShowMessage ("success", "Replied", "Your reply was successfully saved.");
							
							// Refresh comments after 3 seconds
							setTimeout(function(){
								window.location.reload(true);
							}, 3000);
						}
					}
				});
			});
			
			// Update user
			$('#updateUser').click(function(e) {
				// Disable browser defaults events
				e.preventDefault();
				
				// Get form inputs
				var username = $("#username").val();
				var oldPass = $("#oldPass").val();
				var oldPassword = $("#oldPassword").val();
				var newPassword = $("#newPassword").val();
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
				
				// Check new password stated
				if (newPassword && oldPassword <= 0)
				{
					ShowMessage ("danger", "Error", "Please specify your old password.");
					exit();
				}
				else if ((newPassword && oldPassword) && (newPassword == oldPassword))
				{
					// Passwords don't match
					ShowMessage ("danger", "Error", "Your new password cannot be the same as your old one.");
					exit();
				}
				
				// Check password match
				if (oldPassword && (oldPass != oldPassword))
				{
					ShowMessage ("danger", "Error", "Old password incorrect.");
					exit();
				}
				
				// Check passs length
				if (oldPassword && newPassword.length < 4)
				{
					// Too short
					ShowMessage ("danger", "Error", "Password too short! Must be at least 4 characters long.");
					exit();
				}
				
				// Get bot checkbox
				var checked = $("#formUpdate input[name='updateBot']:checked").length;
				
				// Check if permissions are selected
				if (checked <= 0)
				{
					ShowMessage ("danger", "Error", "Please confirm that you're not a web robbot.");
					exit();
				}
				
				// Get all form data
				formUpdateData = $("#formUpdate").serialize();
				
				$.ajax({
					type:'POST',
					data:formUpdateData,
					url:'../../app/core/Modes.php',
					success:function (updateResults) {
						// Get jason results
						result = JSON.parse (updateResults);						
						
						// Check update success
						if (result == 0)
						{
							// User exists
							ShowMessage ("danger", "Error", "Could not update your details! Please try again.");
							exit;
						}
						else
						{
							// Refresh page after 3 seconds
							ShowMessage ("success", "Success", "Your details have been update.");
							setTimeout(function(){
								window.location.reload (true);
							}, 3000);
						}
					}
				});
			});
			
			// Signout of this session
			$('#signoutProceed').click(function(e) {
				// Disable browser defaults events
				e.preventDefault();
				
				// Get webbot checkbox
				var checked = $("#formSignout input[name='signoutBot']:checked").length;
				
				// Check if checkbox ticked
				if (checked <= 0)
				{
					ShowMessage ("danger", "Error", "Please confirm that you're not a web robbot.");
					exit();
				}
				
				// Get all form data
				formSignoutData = $("#formSignout").serialize();
				
				// Do ajax call
				$.ajax({
					type:'POST',
					data:formSignoutData,
					url:'../../app/core/Modes.php',
					success:function (signoutResults) {
						// Get jason results
						result = JSON.parse (signoutResults);						
						
						// Check if user signed out
						if (result == 0)
						{
							// Not signed out
							ShowMessage ("danger", "Error", "Could not sign you out! Please try again.");
							exit;
						}
						else
						{
							// Signed out - go to home page
							ShowMessage ("success", "Success", "Your have been successfully signed out.");
							
							// Redirect after 3 seconds
							setTimeout(function(){
								window.location.replace ("http://localhost/letschat/public/");
							}, 3000);
						}
					}
				});
			});
		});
	</script>
</body>
</html>