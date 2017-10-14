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
	//Initiate the session, save path outside directory folder in a safe location that can't be accessed easily.
	ini_set("session.save_path", "server/sessionData");
	//Start session, tracking activity and storing it in the session directory.
	session_start(); 

	include "db_conn.php";

	if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true){
		$uname = $_SESSION['username'];
		$sqlUser = "SELECT idusers, profilePic FROM appusers WHERE username = '$uname'";
		$rUsers = mysqli_query($conn, $sqlUser) or die(mysqli_error($conn));
		$row = mysqli_fetch_assoc($rUsers) or die(mysqli_error($conn));
		$uid = $row['idusers'];
		$proPic = $row['profilePic'];
		$profilePicPlaceholder = "server/img/profilePlace.png";

		$sqlPosts = "SELECT * FROM appposts";
		$rPost = mysqli_query($conn, $sqlPosts) or die(mysqli_error($conn));
		while($rowPost = mysqli_fetch_assoc($rPost)){
			$postId = $rowPost['idposts'];
			$postBy = $rowPost['postBy'];
			$postTime = $rowPost['postTime'];
			$postDate = $rowPost['postDate'];
			$contentPost = $rowPost['postContent'];
		}		
		echo "<div id=\"communities\" data-role=\"page\" id=\"pageAll\">
				<div data-role=\"header\" id=\"pageHeader\">
					<form id=\"signoutForm\" method=\"post\" action=\"functions.php\" data-ajax=\"false\">
						<input type=\"submit\" name=\"signout\" id=\"signout\" data-iconpos=\"notext\" data-role=\"button\" data-icon=\"home\">
					</form>
					<h1>Communities</h1>
				</div>
				<div data-role=\"content\" id=\"pageContent\">
					<h3>Select a community</h3>";
						$sqlGetCom = "SELECT * FROM appcommunities";
						$rComGet = mysqli_query($conn, $sqlGetCom) or die(mysqli_error($conn));
						while($rowCommune = mysqli_fetch_assoc($rComGet)){
							$communId = $rowCommune['idcommunities'];
							$community = $rowCommune['communityName'];

							echo "<a href=\"index.php?comID=$communId#posts\" data-ajax=\"false\" data-role=\"button\">$community</a>";
						}
				echo "</div>
			</div>

			<div id=\"posts\" data-role=\"page\" id=\"pageAll\">";
				$communityPosts = $_GET['comID'];
				$sqlComName = "SELECT communityName FROM appcommunities WHERE idcommunities = $communityPosts";
				$rComName = mysqli_query($conn, $sqlComName) or die(mysqli_error($conn));
				$rowComName = mysqli_fetch_assoc($rComName);
				$comname = $rowComName['communityName'];
				echo "<div data-role=\"header\" id=\"pageHeader\">
					<a class=\"back\" data-icon=\"back\" data-iconpos=\"notext\" href=\"index.php#communities\" data-role=\"button\">Back</a>
					<form id=\"signoutForm\" method=\"post\" action=\"functions.php\" data-ajax=\"false\">
						<input type=\"submit\" name=\"signout\" id=\"signout\" data-iconpos=\"notext\" data-role=\"button\"  data-icon=\"home\">
					</form>
					<h1>Posts in $comname</h1>
					
				</div>
				<div data-role=\"content\" id=\"pageContent\">";
					$communityPosts = $_GET['comID'];
					$sqlPosts = "SELECT idposts, username, profilePic, postTime, postDate, postContent, postVote, communityId FROM appposts
						INNER JOIN appusers ON appusers.idusers = appposts.postBy
						WHERE communityId = '$communityPosts' 
						ORDER BY idposts DESC";

					$rPosts = mysqli_query($conn, $sqlPosts) or die(mysqli_error($conn));
					if(mysqli_num_rows($rPosts) != '0'){
						while($rowPosts = mysqli_fetch_assoc($rPosts)){
							$postId = $rowPosts['idposts'];
							$postUser = $rowPosts['username'];
							$postProPic = $rowPosts['profilePic'];
							$postTime = $rowPosts['postTime'];
							$postDate = $rowPosts['postDate'];
							$contentPost = $rowPosts['postContent'];
							$postVote = $rowPosts['postVote'];
							$postCommunity = $rowPosts['communityId'];
							echo "<div class=\"postCont\">
								<div class=\"postBy\">
									<img src=\"$profilePicPlaceholder\">
									<div class=\"user\">
										<div class=\"userName\">
											<a href=\"index.php?comID=$postCommunity&postID=$postId#comments\" data-ajax=\"false\">$postUser</a>
											<span>
												<form action=\"functions.php\" method=\"post\" data-ajax=\"false\">
													<input type=\"hidden\" name=\"postCommunity\" id=\"postCommunity\" value=\"$postCommunity\">
													<input type=\"hidden\" name=\"postId\" id=\"postId\" value=\"$postId\">
													<input type=\"submit\" name=\"postVote\" id=\"postVote\" value=\"^\">
												</form>
												$postVote
											</span>
										</div>
										<div class=\"postDate\">
											<p>$postTime, $postDate</p>
										</div>
									</div>
								</div>
								<div class=\"post\">
									<p>$contentPost</p>
								</div>";
								if($_SESSION['username'] == $postUser){
									echo "<form action=\"functions.php\" method=\"post\" data-ajax=\"false\" class=\"deletePost\">
										<input type=\"hidden\" name=\"postCommunity\" id=\"postCommunity\" value=\"$postCommunity\">
										<input type=\"hidden\" name=\"postId\" id=\"postId\" value=\"$postId\">
										<input type=\"submit\" name=\"deletePost\" id=\"deletePost\" data-icon=\"delete\" value=\"Delete Post\">
									</form>";
								}	
							echo "</div>";
						}
					} else {
						echo "<div class=\"noPosts\">
							<p>Oh... There are no posts here yet!</p>
							<p>Be the first to add one!</p>
						</div>";
					}

					echo "<div data-role=\"footer\" data-position= \"fixed\">
						<div data-role=\"navbar\">
							<ul>
								<li><a href=\"index.php?comID=$communityPosts#newPost\" data-role=\"button\">+</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>

			<div id=\"newPost\" data-role=\"page\" id=\"pageAll\">";
				$cmtyid = $_GET['comID'];
				echo "<div data-role=\"header\" id=\"pageHeader\">
					<a class=\"back\" data-icon=\"back\" data-iconpos=\"notext\" href=\"#posts\" data-role=\"button\">&lt;</a>
					<form id=\"signoutForm\" method=\"post\" action=\"functions.php\" data-ajax=\"false\">
						<input type=\"submit\" name=\"signout\" id=\"signout\" data-iconpos=\"notext\" data-role=\"button\"  data-icon=\"home\">
					</form>
					<h1>Post</h1>
				</div>
				<div data-role=\"content\" id=\"pageContent\">
					<form method=\"post\" action=\"functions.php\" data-ajax=\"false\">
						<h3>Say something to the community!</h3>
						<div class=\"post\">
							<input type=\"hidden\" name=\"userId\" id=\"userId\" value=\"$uid\">
							<input type=\"hidden\" name=\"communityId\" id=\"communityId\" value=\"$cmtyid\">
							<textarea id=\"postContent\" name=\"postContent\" placeholder=\"Share your thoughts...\"></textarea>
							<input type=\"submit\" name=\"leavePost\" id=\"leavePost\" value=\"Share\">
						</div>
					</form>
				</div>
			</div>";

			echo "<div id=\"comments\" data-role=\"page\" id=\"pageAll\">";
				$idpost = $_GET['postID'];
				$sqlPost = "SELECT idposts, username, profilePic, postTime, postDate, postContent, postVote, communityId FROM appposts
						INNER JOIN appusers ON appusers.idusers = appposts.postBy
						WHERE idposts = '$idpost'";
				$rPost = mysqli_query($conn, $sqlPost) or die(mysqli_error($conn));
				$rowPost = mysqli_fetch_assoc($rPost);

				$idp = $rowPost['idposts'];
				$userp = $rowPost['username'];
				$proPicp = $rowPost['profilePic'];
				$timep = $rowPost['postTime'];
				$datep = $rowPost['postDate'];
				$contentp = $rowPost['postContent'];
				$votep = $rowPost['postVote'];
				$communep = $rowPost['communityId'];

				echo "<div data-role=\"header\" id=\"pageHeader\">
					<a class=\"back\" data-icon=\"back\" data-iconpos=\"notext\" href=\"index.php?comID=$communep#posts\" data-role=\"button\" data-ajax=\"false\">&lt;</a>
					<form id=\"signoutForm\" method=\"post\" action=\"functions.php\" data-ajax=\"false\">
						<input type=\"submit\" name=\"signout\" id=\"signout\" data-iconpos=\"notext\" data-role=\"button\"  data-icon=\"home\">
					</form>
					<h1>Leave a Comment</h1>
				</div>
				<div data-role=\"content\" id=\"pageContent\">
					<div class=\"opCont\">
						<div class=\"opPostBy\">
							<img src=\"$profilePicPlaceholder\">
							<div class=\"opUser\">
								<div class=\"opUserName\">
									<h3>$userp</h3>
									<span>
										<form action=\"functions.php\" method=\"post\" data-ajax=\"false\">
											<input type=\"hidden\" name=\"postCommunity\" id=\"postCommunity\" value=\"$postCommunity\">
											<input type=\"hidden\" name=\"postId\" id=\"postId\" value=\"$idp\">
											<input type=\"submit\" name=\"commentPostVote\" id=\"commentPostVote\" value=\"^\">
										</form>
										$votep
									</span>
								</div>
								<div class=\"opDate\">
									<p>$timep, $datep</p>
								</div>
							</div>
						</div>
						<div class=\"op\">
							<p>$contentp</p>
						</div>
					</div>";

					$sqlComment = "SELECT idcomments, username, profilePic, commentTime, commentDate, commentContent, commentVote FROM appcomments 
						INNER JOIN appusers ON appusers.idusers = appcomments.commentBy 
						WHERE postID = '$idpost'
						ORDER BY idcomments";
					$rComment = mysqli_query($conn, $sqlComment) or die(mysqli_error($conn));
					while($rowComment = mysqli_fetch_assoc($rComment)){
						$idc = $rowComment['idcomments'];
						$userc = $rowComment['username'];
						$proPicC = $rowComment['profilePic'];
						$timeC = $rowComment['commentTime'];
						$dateC = $rowComment['commentDate'];
						$contentc = $rowComment['commentContent'];
						$votec = $rowComment['commentVote'];
					
						echo " 
							<div class=\"commentCont\">
								<div class=\"commentBy\">
									<div class=\"user\">
										<div class=\"commentUser\">
											<h4>$userc</h4>
											<span>
												<form action=\"functions.php\" method=\"post\" data-ajax=\"false\">
													<input type=\"hidden\" name=\"redirectPost\" id=\"redirectPost\" value=\"$idp\">
													<input type=\"hidden\" name=\"redirectComm\" id=\"redirectComm\" value=\"$communep\">
													<input type=\"hidden\" name=\"commentId\" id=\"commentId\" value=\"$idc\">
													<input type=\"submit\" name=\"commentVote\" id=\"commentVote\" value=\"^\">
												</form>
												$votec
											</span>
										</div>
									</div>
								</div>
								<div class=\"comment\">
									<p>$contentc</p>
									<div class=\"postDate\">
										<p>$timeC, $dateC</p>
									</div>
								</div>
							</div>
						";
					}
					echo "<form method=\"post\" action=\"functions.php\" data-ajax=\"false\">
						<div class=\"post\">
							<input type=\"hidden\" name=\"postId\" id=\"postId\" value=\"$idp\">
							<input type=\"hidden\" name=\"userId\" id=\"userId\" value=\"$uid\">
							<textarea id=\"postComment\" name=\"postComment\" placeholder=\"Leave a Comment...\"></textarea>
							<input type=\"submit\" name=\"leaveComment\" id=\"leaveComment\" value=\"Post Comment\">
						</div>
					</form>
				</div>
			</div>
		";

	}else{
		echo "
			<div id=\"signIn\" data-role=\"page\" id=\"pageAll\">
				<div data-role=\"header\" id=\"pageHeader\">
					<h1>Sign In</h1>
				</div>
				<div data-role=\"content\" id=\"pageContent\">
					<form method=\"post\" action=\"functions.php\" data-ajax=\"false\">
						<label for=\"username\">Username</label>
						<input type=\"text\" id=\"username\" name=\"username\">
						<label for=\"password\">Password</label>
						<input type=\"password\" id=\"password\" name=\"password\">

						<input type=\"submit\" name=\"signin\" id=\"signin\" value=\"Sign In\" data-role=\"button\">
						<a href=\"#signUp\" data-role=\"button\">Sign Up</a>
						<a href=\"#forgotPass\">Forgot Password?</a>
					</form>
				</div>
			</div>

			<div id=\"signinFail\" data-role=\"page\" id=\"pageAll\">
				<div data-role=\"header\" id=\"pageHeader\">
					<a class=\"back\" data-icon=\"back\" data-iconpos=\"notext\" href=\"#signIn\">&lt;</a>
					<h1>Sign In</h1>
				</div>
				<div data-role=\"content\" id=\"pageContent\">
					<label for=\"username\">Username</label>
					<input type=\"text\" id=\"username\" name=\"username\">
					<label for=\"password\">Password</label>
					<input type=\"password\" id=\"password\" name=\"password\">

					<a href=\"#posts\" data-role=\"button\">Sign in</a>
					<a href=\"#signUp\" data-role=\"button\">Sign Up</a>
					<a href=\"#forgotPass\">Forgot Password?</a>
				</div>
			</div>

			<div id=\"forgotPass\" data-role=\"page\" id=\"pageAll\">
				<div data-role=\"header\" id=\"pageHeader\">
					<a class=\"back\" data-icon=\"back\" data-iconpos=\"notext\" href=\"#signIn\">&lt;</a>
					<h1>Restore Password</h1>
				</div>
				<div data-role=\"content\" id=\"pageContent\">
					<form method=\"post\" action=\"functions.php\" data-ajax=\"false\">
						<label for=\"username\">Username</label>
							<input type=\"text\" id=\"username\" name=\"username\">
						<label for= \"newPass\">New Password</label>
							<input type=\"password\" id=\"password\" name=\"password\">
						<label for=\"confirmPass\">Confirm Password</label>
							<input type=\"password\" id=\"password\" name=\"conPassword\">

						<input type=\"submit\" name=\"restorePass\" id=\"restorePass\" value=\"Restore Password\" data-role=\"button\">
					</form>
				</div>
			</div>

			<div id=\"signUp\" data-role=\"page\" id=\"pageAll\">
				<div data-role=\"header\" id=\"pageHeader\">
					<a class=\"back\" data-icon=\"back\" data-iconpos=\"notext\" href=\"#signIn\">&lt;</a>
					<h1>Sign Up</h1>
				</div>
				<div data-role=\"content\" id=\"pageContent\">
					<form method=\"post\" action=\"functions.php\" enctype=\"multipart/form-data\" data-ajax=\"false\">
						<label for=\"username\">Username <span id=\"required\">*</span></label>
							<input type=\"text\" id=\"username\" name=\"username\" required>
						<label for=\"email\">E-mail <span id=\"required\">*</span></label>
							<input type=\"email\" id=\"email\" name=\"email\" required>
						<label for= \"newPass\">Password <span id=\"required\">*</span></label>
							<input type=\"password\" id=\"password\" name=\"password\" required>
						<label for=\"confirmPass\">Confirm Password <span id=\"required\">*</span></label>
							<input type=\"password\" id=\"password\" name=\"conPassword\" required>
						<!--<label for=\"profilePic\">Upload Profile Picture</label>
							<input type=\"file\" name=\"profilePic\" id=\"profilePic\">-->

						<input type=\"submit\" name=\"signup\" id=\"signup\" value=\"Sign Up\" data-role=\"button\">
						<p class=\"required\">* required</p>
					</form>
				</div>
			</div>
		";
	}
?>
	</body>
</html>