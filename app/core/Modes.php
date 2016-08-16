<?php

// Include and instantiate Chat class
require_once ("../models/Chat.php");
$liveChat = new Chat;

// Get type of mode
if (isset ($_REQUEST['mode'])) $mode = $_REQUEST['mode'];

// Check mode
if ($mode == "signup")
{
	// Get posted data and clean string
	$username = htmlentities (trim ($_REQUEST['username']));
	$password = htmlentities (trim ($_REQUEST['password']));
	$firstname = htmlentities (trim ($_REQUEST['firstname']));
	$lastname = htmlentities (trim ($_REQUEST['lastname']));
	if (isset ($_REQUEST['gender'])) $gender = $_REQUEST['gender'];
	
	// Check if user extists
	$getUame = $liveChat->get ("members", ["username", "=", $username]);
	
	// If user found
	if ($getUame['count'] >= 1)
	{
		// Exit with error 1
		echo json_encode (1);
		exit();
	}
	else
	{
		// Generate unique UID
		$guid = md5 (uniqid());
		
		// Save user
		$saveUser = $liveChat->insert ("members", ["memberId" => $guid, "username" => $username, "pass" => $password,
										"name" => $firstname, "surname" => $lastname, "gender" => $gender, "date_created" => time()
										]);
		// If user created
		if ($saveUser == true)
		{
			// Start session
			$sessionId = md5 (uniqid());
			$startSession = $liveChat->insert ("sessions", ["sessionId" => $sessionId, "memberId" => $guid, "date_started" => time()]);
			
			// Exit and return guid
			if ($startSession === true) echo json_encode ($guid);
			exit();
		}
		else
		{
			// Exit with error
			echo json_encode (0);
			exit();
		}
	}
}
elseif ($mode == "signin")
{
	// Get posted data and clean string
	$loginEmail = htmlentities (trim ($_REQUEST['loginEmail']));
	$loginPassword = htmlentities (trim ($_REQUEST['loginPassword']));
	
	// Check user
	$getMember = $liveChat->get ("members", ["username", "=", $loginEmail]);
	
	// If user found
	if ($getMember['count'] >= 1)
	{
		// Get memberId
		foreach ($getMember['results'] as $results)
		{
			// Set memberId and pass
			$memberId = $results->memberId;
			$password = $results->pass;
		}
		
		// Check passwords match
		if ($password == $loginPassword)
		{
			// Start session
			$sessionId = md5 (uniqid());
			$startSession = $liveChat->insert ("sessions", ["sessionId" => $sessionId, "memberId" => $memberId, "date_started" => time()]);
			
			// Exit and return memberId
			if ($startSession === true) echo json_encode ($memberId);
			exit();
		}
		else
		{
			// Passwords didn't match
			echo json_encode (1);
			exit();
		}
	}
	else
	{
		// User not found
		echo json_encode (0);
		exit();
	}
}
elseif ($mode == "comment")
{
	// Get posted data and clean string
	$memberId = $_REQUEST['memberId'];
	$comment = htmlentities (trim ($_REQUEST['comment']));
	
	// Save commet
	$saveComment = $liveChat->insert ("comments", ["memberId" => $memberId, "comment" => $comment, "date_created" => time()]);
	
	// Exit with true
	echo json_encode ($saveComment);
	exit();
}
elseif ($mode == "delete")
{
	// Get posted data
	$commentId = $_REQUEST['commentIdDelete'];
	
	// Delete this comment
	$deleteComment = $liveChat->delete ("comments", ["commentId", "=", $commentId]);
	
	// Check if deleted
	if ($deleteComment['count'] == 1)
	{
		// Exit with success
		echo json_encode (1);
		exit();
	}
	else
	{
		// Exit with fail
		echo json_encode (0);
		exit();
	}
}
elseif ($mode == "reply")
{
	// Get posted data and clean string
	$replyIds = explode (",", $_REQUEST['commentIdReply']);
	$reply = htmlentities (trim ($_REQUEST['commentReply']));
	
	// Add reply message
	$addReply = $liveChat->insert ("replies", ["commentId" => $replyIds[0], "memberId" => $replyIds[1], "reply" => $reply, "date_created" => time()]);
	
	// Exit
	echo json_encode ($addReply);
	exit();
}
elseif ($mode == "update")
{
	// Get posted data and clean string
	$memberId = $_REQUEST['memberId'];
	$username = htmlentities (trim ($_REQUEST['username']));
	$newPassword = htmlentities (trim ($_REQUEST['newPassword']));
	$firstname = htmlentities (trim ($_REQUEST['firstname']));
	$lastname = htmlentities (trim ($_REQUEST['lastname']));
	if (isset ($_REQUEST['gender'])) $gender = $_REQUEST['gender'];
	
	// Update details
	$updateUser = $liveChat->update ("members", ["username" => $username, "pass" => $newPassword, "name" => $firstname,
												 "surname" => $lastname, "gender" => $gender], "memberId", $memberId);
	
	// Check if user updated
	if ($updateUser == true)
	{
		// Exit with success
		echo json_encode (1);
		exit();
	}
	else
	{
		// Exit with error
		echo json_encode (0);
		exit();
	}
}
elseif ($mode == "signout")
{
	// Get memberId
	$sessionId = $_REQUEST['sessionId'];
	
	// Update session logout time
	$sessionClosed = $liveChat->update ("sessions", ["date_ended" => time()], "sessionId", $sessionId);
	
	// See if user logged out
	if ($sessionClosed == true)
	{
		// Exit with success
		echo json_encode (1);
		exit();
	}
	else
	{
		// Session not ended
		echo json_encode (0);
		exit();
	}
}
else
{
	// Report bug in PHP logs
	return error_log ("The system existed without running any action!");
}