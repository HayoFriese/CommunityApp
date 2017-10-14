<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
    	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    	<title>App Test</title>
    	<link rel="stylesheet" href="resources/css/main.css" type="text/css">
    	<link rel="stylesheet" href="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
		<script src="//code.jquery.com/jquery-2.2.1.min.js"></script>
		<script src="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    </head>
	<body>
<?php
	ini_set("session.save_path", "server/sessionData");
	session_start(); 
	include "db_conn.php";

	echo "<div id=\"errors\" data-role=\"page\">
				<div data-role=\"header\">
					<h1>Errors</h1>
				</div>
				<div data-role=\"content\">";

	if(isset($_POST['signup'])){
		$errors = [];

		$signupU = filter_has_var(INPUT_POST, 'username') ? $_POST['username']: null;
		$signupE = filter_has_var(INPUT_POST, 'email') ? $_POST['email']: null;
		$signupP = filter_has_var(INPUT_POST, 'password') ? $_POST['password']: null;
		$signupCP = filter_has_var(INPUT_POST, 'conPassword') ? $_POST['conPassword']: null;

		$signupU = trim($signupU);
		$signupE = trim($signupE);
		$signupP = trim($signupP);
		$signupCP = trim($signupCP);

		$signupU = filter_var($signupU, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$signupE = filter_var($signupE, FILTER_SANITIZE_EMAIL);
		$signupP = filter_var($signupP, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$signupCP = filter_var($signupCP, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

		//empty username
            if(empty($signupU)){
                $errors[] = 'Please enter a username';
            }
        //Check if username already exists, username mainly
            elseif(!empty($signupU)){ 
        	    $exists = mysqli_query($conn, "SELECT username FROM appusers WHERE username = '".$signupU."'");
                if (mysqli_num_rows($exists)){
                    $errors[] = 'It looks like this username already exists! Please type in another username';
                }
            }
        //Empty
            elseif(empty($upass) || empty($upcheck)){
                $errors[] = 'Please enter a password';
        	}
        //Check if both passwords match
            elseif($upass != $upcheck){
                $errors[] = 'Your Passwords did not match. Please try again';
            }

        if(!empty($errors)){
    	   	echo '<ul>';
    	   	foreach ($errors as $key => $value) {
    	   		echo '<li>' .  $value . '</li>';
    	   	}
    	   	echo '</ul>';
    	} else {
    		$password = sha1($signupP);

    		$sql = "INSERT INTO appusers(username, password, email) VALUES(?, ?, ?)";

			$stmt = mysqli_prepare($conn, $sql) or die(mysqli_error($conn));
            mysqli_stmt_bind_param($stmt, "sss", $signupU, $password, $signupE) or die(mysqli_error($conn));
		  	mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
            mysqli_stmt_close($stmt) or die(mysqli_error($conn));

            $userid = mysqli_insert_id($conn);

            if(isset($_FILES['profilePic'])){
            //Create Subdirectory
     		//Set the subdirectory name
      			$subdir = $userid;
      		//set the directory path name
      			$dir = ('server/img/users/'.$subdir);
      		//make the directory
      			mkdir($dir, 0777);
      		//Image Upload

   				$pname = $_FILES['profilePic']['name'];
   				$allowedExts = array("gif", "jpeg", "jpg", "png");
   				$temp = explode(".", $pname);
   				$extension = end($temp);
   				if ((($_FILES['profilePic']['type'] == "image/gif")
    				|| ($_FILES['profilePic']['type'] == "image/jpeg")
    				|| ($_FILES['profilePic']['type'] == "image/jpg")
    				|| ($_FILES['profilePic']['type'] == "image/png"))
    				&& ($_FILES['profilePic']['size'] < 1073741824)
    				&& in_array($extension, $allowedExts))
    				{
    					if ($_FILES['profilePic']['error'] > 0){
    				  		echo "Return Code: " . $_FILES['profilePic']['error']."<br>";
    				  	} else {
    				  		if (file_exists($dir . $pname)){
    				    		echo "<p>File Already Exists</p>";
    						} else {
    				    		$pnames = $_FILES['profilePic']['tmp_name'];
    				    		//move the files you upload into the newly 	generated folder.
        		  				if (move_uploaded_file($pnames, "$dir/$pname")){
        		    				$ppathname = ($dir."/".$pname);
        		    				
        		    				$sqlProPic = "UPDATE appusers SET profilePic =  ? WHERE idusers = ?";
        		  					
									$stmt = mysqli_prepare($conn, $sqlProPic) or die(mysqli_error($conn));
									mysqli_stmt_bind_param($stmt, "sd", $ppathname, $userid) or die(mysqli_error($conn));
									mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
									mysqli_stmt_close($stmt) or die(mysqli_error($conn));
        		  				} else {
        		    			echo "<p>not moved</p>";
        		  			}
        		  		}
      				}
      			} else {
      				echo "Invalid file";
    			}	 
    		}
    		mysqli_close($conn);
           	header('location: index.php#signIn');
		}
	}

	elseif(isset($_POST['signin'])){
		$errors = [];

		$signinU = filter_has_var(INPUT_POST, 'username') ? $_POST['username']: null;
		$signinP = filter_has_var(INPUT_POST, 'password') ? $_POST['password']: null;
	
		$signinU = trim($signinU);
		$signinP = trim($signinP);

		$signinU=filter_var($signinU, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$signinP=filter_var($signinP, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

		$signinU= filter_var($signinU, FILTER_SANITIZE_SPECIAL_CHARS);
		$signinP = filter_var($signinP, FILTER_SANITIZE_SPECIAL_CHARS);
		
		if(!empty($signinU) || $signinU == ''){
			$errors = '<p>Please Enter a username</p>';
		}
		elseif(!empty($signinP) || $signinP == ''){
			$errors = '<p>Please Enter a password</p>';
		}

		if(is_array($errors)){
			echo "<ul>";
			foreach($errors as $key => $value){
				echo "<li>$value </li>";
			}
			echo "</ul>";
		} else {
			$sql = "SELECT password FROM appusers WHERE username = ?";
			$stmt = mysqli_prepare($conn, $sql) or die(mysqli_error($conn));
			mysqli_stmt_bind_param($stmt, "s", $signinU) or die(mysqli_error($conn));
			mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
			mysqli_stmt_bind_result($stmt, $passHash) or die(mysqli_error($conn));

			if (mysqli_stmt_fetch($stmt)){
				if(sha1($signinP) == $passHash){
					$_SESSION['username'] = $signinU;
					$_SESSION['signed_in'] = true;

					header("location: index.php#communities");
				} else {
					echo  "<p>Password Incorrect</p>
					<a href=\"index.php#signIn\" data-role=\"button\" data-ajax=\"false\">Back</a>";
				}
			} else {
				echo "<p>Sorry we don't seem to have that username.</p>";
			}
			mysqli_stmt_close($stmt);
			mysqli_close($conn);
		}
	}

	elseif(isset($_POST['restorePass'])){
		$errors = [];

		$resetU = filter_has_var(INPUT_POST, 'username') ? $_POST['username']: null;
		$resetP = filter_has_var(INPUT_POST, 'password') ? $_POST['password']: null;
		$resetCP = filter_has_var(INPUT_POST, 'conPassword') ? $_POST['conPassword']: null;

		$resetU = trim($resetU);
		$resetP = trim($resetP);
		$resetCP = trim($resetCP);

		$resetU = filter_var($resetU, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$resetP = filter_var($resetP, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$resetCP = filter_var($resetCP, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

		//empty username
            if(empty($resetU)){
                $errors[] = 'Please enter a username';
            }
            elseif(empty($resetP) || empty($resetCP)){
                $errors[] = 'Please enter a password';
        	}
        //Check if username already exists, username mainly
            elseif(!empty($resetU)){ 
        	    $exists = mysqli_query($conn, "SELECT username FROM appusers WHERE username = '".$resetU."'");
                if (mysqli_num_rows($exists)){
                	if($resetP != $resetCP){
                		$errors[] = 'Your Passwords did not match. Please try again';
            		}
            		if(!empty($errors)){
    				   	echo '<ul>';
    				   	foreach ($errors as $key => $value) {
    				   		echo '<li>' .  $value . '</li>';
    				   	}
    				   	echo '</ul>';
    				} else {
    					$password = sha1($resetP);

    					$sql = "UPDATE appusers SET password = ? WHERE username = ?";
						$stmt = mysqli_prepare($conn, $sql) or die(mysqli_error($conn));
						mysqli_stmt_bind_param($stmt, "ss", $password, $resetU) or die(mysqli_error($conn));
						mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
        				mysqli_stmt_close($stmt) or die(mysqli_error($conn));
        				mysqli_close($conn) or die(mysqli_error($conn));

        				header("location: index.php#signIn");
    				}
                } else {
                	$errors[] = 'Username does not exist';
                }
                if(!empty($errors)){
    			   	echo '<ul>';
    			   	foreach ($errors as $key => $value) {
    			   		echo '<li>' .  $value . '</li>';
    			   	}
    			   	echo '</ul>';
    			}
            } 		
	}

	elseif(isset($_POST['signout'])){
		//Gathers all existing session data.
		$_SESSION = array(); 		

		//Destroys session.
		session_destroy(); 

		//returns to previous url that the user was on.
		header("location: index.php");
	}

	elseif(isset($_POST['leavePost'])){
		$errors = [];

		$postBy = filter_has_var(INPUT_POST, 'userId') ? $_POST['userId']: null;
		$postBy = trim($postBy);
		$communityId = filter_has_var(INPUT_POST, 'communityId') ? $_POST['communityId']: null;
		$communityId = trim($communityId);

		$postCont = filter_has_var(INPUT_POST, 'postContent') ? $_POST['postContent']: null;
		$postCont = trim($postCont);
		$postCont = filter_var($postCont, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$postCont = filter_var($postCont, FILTER_SANITIZE_SPECIAL_CHARS);
		
		if(!empty($postCont) || $postCont == ''){
			$errors = '<p>Please Fill Out the Form</p>';
		}
		if(is_array($errors)){
			echo "<ul>";
			foreach($errors as $key => $value){
				echo "<li>$value </li>";
			}
			echo "</ul>";
		} else {
			$postDate = date("Y-m-d");
			$postTime = date("H:i:s");

			$sql = "INSERT INTO appposts(postBy, postTime, postDate, postContent, communityId) VALUES(?, ?, ?, ?, ?)";
			
			$stmt = mysqli_prepare($conn, $sql) or die(mysqli_error($conn));
			mysqli_stmt_bind_param($stmt, "dsssd", $postBy, $postTime, $postDate, $postCont, $communityId) or die(mysqli_error($conn));
		  	mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
            mysqli_stmt_close($stmt) or die(mysqli_error($conn));
            mysqli_close($conn) or die(mysqli_error($conn));

            header("location: index.php?comID=$communityId#posts");
		}
	}

	elseif(isset($_POST['leaveComment'])){
		$errors = [];

		$commentBy = filter_has_var(INPUT_POST, 'userId') ? $_POST['userId']: null;
		$commentBy = trim($commentBy);

		$postID = filter_has_var(INPUT_POST, 'postId') ? $_POST['postId']: null;
		$postID = trim($postID);

		$commentCont = filter_has_var(INPUT_POST, 'postComment') ? $_POST['postComment']: null;
		$commentCont = trim($commentCont);
		$commentCont = filter_var($commentCont, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$commentCont = filter_var($commentCont, FILTER_SANITIZE_SPECIAL_CHARS);
		
		if(!empty($commentCont) || $commentCont == ''){
			$errors = '<p>Please Fill Out the Form</p>';
		}
		if(is_array($errors)){
			echo "<ul>";
			foreach($errors as $key => $value){
				echo "<li>$value </li>";
			}
			echo "</ul>";
		} else {
			$commentDate = date("Y-m-d");
			$commentTime = date("H:i:s");

			$sql = "INSERT INTO appcomments(commentBy, commentTime, commentDate, commentContent, postID) VALUES(?, ?, ?, ?, ?)";
			
			$stmt = mysqli_prepare($conn, $sql) or die(mysqli_error($conn));
			mysqli_stmt_bind_param($stmt, "ssssd", $commentBy, $commentTime, $commentDate, $commentCont, $postID) or die(mysqli_error($conn));
		  	mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
            mysqli_stmt_close($stmt) or die(mysqli_error($conn));

            $sqlGet = "SELECT communityId FROM appposts WHERE idposts = $postID";
            $rGet = mysqli_query($conn, $sqlGet) or die(mysqli_error($conn));
            $rowGet = mysqli_fetch_assoc($rGet);
            $commId = $rowGet['communityId'];

            mysqli_close($conn) or die(mysqli_error($conn));
            header("location: index.php?comID=$commId&postID=$postID#comments");
		}
	}

	elseif(isset($_POST['postVote'])){
		$postId = filter_has_var(INPUT_POST, 'postId') ? $_POST['postId']: null;
		$postId = trim($postId);
		$postId = filter_var($postId, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$postId = filter_var($postId, FILTER_SANITIZE_SPECIAL_CHARS);

		$postCommunity = filter_has_var(INPUT_POST, 'postCommunity') ? $_POST['postCommunity']: null;
		$postCommunity = trim($postCommunity);

		$postVote = 1;
		$sql = "UPDATE appposts SET postVote = postVote + ? WHERE idposts = ? AND communityId = ?";
		$stmt = mysqli_prepare($conn, $sql) or die(mysqli_error($conn));
		mysqli_stmt_bind_param($stmt, "ddd", $postVote, $postId, $postCommunity) or die(mysqli_error($conn));
		mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
        mysqli_stmt_close($stmt) or die(mysqli_error($conn));
        mysqli_close($conn) or die(mysqli_error($conn));

        header("location: index.php?comID=".$postCommunity."#posts");
	}

	elseif(isset($_POST['commentPostVote'])){
		$postId = filter_has_var(INPUT_POST, 'postId') ? $_POST['postId']: null;
		$postId = trim($postId);
		$postId = filter_var($postId, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$postId = filter_var($postId, FILTER_SANITIZE_SPECIAL_CHARS);

		$postCommunity = filter_has_var(INPUT_POST, 'postCommunity') ? $_POST['postCommunity']: null;
		$postCommunity = trim($postCommunity);

		$postVote = 1;
		$sql = "UPDATE appposts SET postVote = postVote + ? WHERE idposts = ? AND communityId = ?";
		$stmt = mysqli_prepare($conn, $sql) or die(mysqli_error($conn));
		mysqli_stmt_bind_param($stmt, "ddd", $postVote, $postId, $postCommunity) or die(mysqli_error($conn));
		mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
        mysqli_stmt_close($stmt) or die(mysqli_error($conn));
        mysqli_close($conn) or die(mysqli_error($conn));

        header("location: index.php?comID=$postCommunity&postID=$postId#comments");
	}

	elseif(isset($_POST['commentVote'])){
		$commentId = filter_has_var(INPUT_POST, 'commentId') ? $_POST['commentId']: null;
		$commentId = trim($commentId);
		$commentId = filter_var($commentId, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$commentId = filter_var($commentId, FILTER_SANITIZE_SPECIAL_CHARS);

		$redirectPost = filter_has_var(INPUT_POST, 'redirectPost') ? $_POST['redirectPost']: null;
		$redirectPost = trim($redirectPost);
		$redirectPost = filter_var($redirectPost, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$redirectPost = filter_var($redirectPost, FILTER_SANITIZE_SPECIAL_CHARS);

		$commentVote = 1;
		$sql = "UPDATE appcomments SET commentVote = commentVote + ? WHERE idcomments = ?";
		$stmt = mysqli_prepare($conn, $sql) or die(mysqli_error($conn));
		mysqli_stmt_bind_param($stmt, "dd", $commentVote, $commentId) or die(mysqli_error($conn));
		mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
        mysqli_stmt_close($stmt) or die(mysqli_error($conn));

        $sqlGet = "SELECT communityId FROM appposts WHERE idposts = $redirectPost";
        $rGet = mysqli_query($conn, $sqlGet) or die(mysqli_error($conn));
        $rowGet = mysqli_fetch_assoc($rGet);
        $commId = $rowGet['communityId'];

        mysqli_close($conn) or die(mysqli_error($conn));
        header("location: index.php?comID=$commId&postID=$redirectPost#comments");
	}

	elseif(isset($_POST['deletePost'])){
		$postId = filter_has_var(INPUT_POST, 'postId') ? $_POST['postId']: null;
		$postId = trim($postId);
		$postId = filter_var($postId, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$postId = filter_var($postId, FILTER_SANITIZE_SPECIAL_CHARS);

		$postCommunity = filter_has_var(INPUT_POST, 'postCommunity') ? $_POST['postCommunity']: null;
		$postCommunity = trim($postCommunity);
		$postCommunity = filter_var($postCommunity, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		$postCommunity = filter_var($postCommunity, FILTER_SANITIZE_SPECIAL_CHARS);

		$sql = "DELETE FROM appposts WHERE idposts = ?";
		$stmt = mysqli_prepare($conn, $sql) or die(mysqli_error($conn));
		mysqli_stmt_bind_param($stmt, "d", $postId) or die(mysqli_error($conn));
		mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
        mysqli_stmt_close($stmt) or die(mysqli_error($conn));

        $sqlComDel = "DELETE FROM appcomments WHERE postID = ?";
        $stmt = mysqli_prepare($conn, $sqlComDel) or die(mysqli_error($conn));
		mysqli_stmt_bind_param($stmt, "d", $postId) or die(mysqli_error($conn));
		mysqli_stmt_execute($stmt) or die(mysqli_error($conn));
        mysqli_stmt_close($stmt) or die(mysqli_error($conn));
        mysqli_close($conn) or die(mysqli_error($conn));	

        header("location: index.php?comID=".$postCommunity."#posts");	
	}
	echo "<p><a href=\"{$_SERVER['HTTP_REFERER']}\">Back..</a></p>";
?>

			</div>
		</div>";
	</body>
</html>