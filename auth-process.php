<?php
session_start();
include_once('lib/db.inc.php');

function ierg4210_login(){
	if (empty($_POST['email']) || empty($_POST['pw']) 
		|| !preg_match("/^[\w=+\-\/][\w='+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$/", $_POST['email'])
		|| !preg_match("/^[\w@#$%\^\&\*\-]+$/", $_POST['pw'])) {
		throw new Exception('Wrong Credentials');
	} else {
	
		// Implement the login logic here
		global $db;
		$db = ierg4210_DB();
		$q=$db->prepare('SELECT * FROM useraccount WHERE email = ?');
		$email = $_POST['email'];
		$q->execute(array($email));
		$r=$q->fetch();
		if(count($r) != 0){
			$password = $_POST['pw'];
			$saltpassword=hash_hmac('sha1', $password, $r['salt']);
			if($saltpassword == $r['password']){
				$exp = time() + 3600 * 24 * 3; // 3days
				$token = array('em'=>$r['email'], 'exp'=>$exp, 'k'=>hash_hmac('sha1', $exp.$r['password'], $r['salt']));
				
				// create the cookie 
				setcookie('auth', json_encode($token), $exp, "/", "secure.shop107.ierg4210.org", 1, 1); 
				//setcookie('auth',json_encode($token));
				// put it also in $_SESSION 
				$_SESSION['auth'] = $token; 
				// change the PHPSESSID after login 
				session_regenerate_id(); 
				header('Location: admin.php', true, 302);
				exit();
				//return true;
			} else {
				throw new Exception('Wrong Credentials');
			}
		} else {
			throw new Exception('Wrong Credentials');
		}
	}
}
/*	if ($login_success){
		// redirect to admin page
		header('Location: admin.php', true, 302);
		exit();
	} else
		throw new Exception('Wrong Credentials');
}
*/
function ierg4210_logout(){
	// clear the cookies and session
	setcookie('auth',"", time()-3600,"/", "secure.shop107.ierg4210.org", 1, 1);
	session_destroy();
	// redirect to login page after logout
	session_regenerate_id(); 
	header('Location: admin.php', true, 302);
	exit();
}




header('Content-type: text/html; charset=utf-8');

	// input validation
	if (empty($_REQUEST['action']) || !preg_match('/^\w+$/', $_REQUEST['action']))
		throw new Exception('Undefined Action');
	else {
		//check if the form request can present a valid nonce
		include_once('lib/csrf.php');
		if(csrf_verifyNonce($_REQUEST['action'], $_POST['nonce']) == false) {
			throw new Exception('csrf-attack');
		}
	}
try {	
	// run the corresponding function according to action
	if (($returnVal = call_user_func('ierg4210_' . $_REQUEST['action'])) === false) {
		if ($db && $db->errorCode()) 
			error_log(print_r($db->errorInfo(), true));
		throw new Exception('Failed');
	} else {
		// no functions are supposed to return anything
		// echo $returnVal;
	}

} catch(PDOException $e) {
	error_log($e->getMessage());
	header('Refresh: 10; url=login.php?error=db');
	echo '<strong>Error Occurred:</strong> DB <br>Redirecting to login page in 10 seconds...';
} catch(Exception $e) {
	header('Refresh: 10; url=login.php?error=' . $e->getMessage());
	echo '<strong>Error Occurred:</strong> ' . $e->getMessage() . '<br>Redirecting to login page in 10 seconds...';
}
?>