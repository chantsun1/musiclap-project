<?php
session_start();

include_once('lib/db.inc.php');
include_once('lib/csrf.php');
include_once('incl/resize_img.php');
include_once('lib/auth.php');

/*
function ierg4210_cat_fetchall() {
	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("SELECT * FROM categories LIMIT 100;");
	if ($q->execute())
		return $q->fetchAll();
}
*/

function ierg4210_cat_insert() {
	// input validation or sanitization
	if (!preg_match('/^[\w\-, ]+$/', $_POST['name']))
		throw new Exception("invalid-name");
	
	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("INSERT INTO categories (name) VALUES (?)");
	return $q->execute(array($_POST['name']));
}

function ierg4210_cat_edit() {
	
	// input validation or sanitization
	if (!preg_match('/^[\w\-, ]+$/', $_POST['name']))
		throw new Exception("invalid-name");
		
	// input validation or sanitization
	if (!is_numeric($_POST['catid']))
		throw new Exception("invalid-category");

	$_POST['catid'] = (int) $_POST['catid'];

	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("UPDATE categories SET name = ? WHERE catid = ?");
	return $q->execute(array($_POST['name'],$_POST['catid']));
}

function ierg4210_cat_delete() {
	// input validation or sanitization
	if (!is_numeric($_POST['catid']))
		throw new Exception("invalid-category");

	$_POST['catid'] = (int) $_POST['catid'];

	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("DELETE FROM categories WHERE catid = ?");
	return $q->execute(array($_POST['catid']));

}

// Since this form will take file upload, we use the tranditional (simpler) rather than AJAX form submission.
// Therefore, after handling the request (DB insert and file copy), this function then redirects back to admin.html
function ierg4210_prod_insert() {
	// input validation or sanitization
	if (!preg_match('/^[\w\-, ]+$/', $_POST['name']))
		throw new Exception("invalid-name");
	if (!preg_match('/^[\w\-, ]+$/', $_POST['description']))
		throw new Exception("invalid-description");
	if (!is_numeric($_POST['catid']))
		throw new Exception("invalid-category");
	if (!is_numeric($_POST['price']))
		throw new Exception("invalid-price");
		
	$_POST['catid'] = (int) $_POST['catid'];
	$_POST['price'] = (int) $_POST['price'];
	
	
	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	// TODO: complete the rest of the INSERT command
	
	$q = $db->prepare("INSERT INTO products (catid, name, price,description) VALUES (?,?,?,?)");
	$q->execute(array($_POST['catid'],$_POST['name'],$_POST['price'],$_POST['description']));
	
	// The lastInsertId() function returns the pid (primary key) resulted by the last INSERT command
	$lastId = $db->lastInsertId('products');

	// Copy the uploaded file to a folder which can be publicly accessible at incl/img/[pid].jpg
	if ($_FILES["file"]["error"] == 0
		&& $_FILES["file"]["type"] == "image/jpeg"
		&& $_FILES["file"]["size"] < 5000000) {
		
		unlink("incl/img/" . $lastId . ".jpg");
		// Note: Take care of the permission of destination folder (hints: current user is apache)
		if (move_uploaded_file($_FILES["file"]["tmp_name"], "incl/img/" . $lastId . ".jpg")) {
			
			//create thumbnail if successful upload
			$image = new SimpleImage();
			$image->load("incl/img/" . $lastId . ".jpg");
			$image->resizeToWidth(150);
			$image->save("incl/img/" . $lastId . "_tn.jpg");
			
			// redirect back to original page; you may comment it during debug
			
			header('Location: admin.php');
			exit();
		}

	}

	// Only an invalid file will result in the execution below
	
	// TODO: remove the SQL record that was just inserted
	
	global $db;
	$db = ierg4210_DB();
	
	$q = $db->prepare("DELETE FROM products WHERE catid = ?, name = ?, price = ?, description = ?");
	$q->execute(array($_POST['catid'],$_POST['name'],$_POST['price'],$_POST['description']));

	
	// To replace the content-type header which was json and output an error message
	header('Content-Type: text/html; charset=utf-8');
	echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
	exit();
}

// TODO: add other functions here to make the whole application complete
function ierg4210_prod_edit() {
	// input validation or sanitization
	if (!preg_match('/^[\w\-, ]+$/', $_POST['name']))
		throw new Exception("invalid-name");
	if (!preg_match('/^[\w\-, ]+$/', $_POST['description']))
		throw new Exception("invalid-description");
	if (!is_numeric($_POST['catid']))
		throw new Exception("invalid-category");
	if (!is_numeric($_POST['price']))
		throw new Exception("invalid-price");
	if (!is_numeric($_POST['pid']))
		throw new Exception("invalid-product");
		
	$_POST['catid'] = (int) $_POST['catid'];
	$_POST['price'] = (int) $_POST['price'];
	$_POST['pid'] = (int) $_POST['pid'];
	
	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	
	$q = $db->prepare("UPDATE products SET catid = ?, name = ?, price = ?,description = ? WHERE pid = ?");
	$q->execute(array($_POST['catid'],$_POST['name'],$_POST['price'],$_POST['description'],$_POST['pid']));

	// The lastInsertId() function returns the pid (primary key) resulted by the last INSERT command
	$lastId = $_POST['pid'];

	// Copy the uploaded file to a folder which can be publicly accessible at incl/img/[pid].jpg
	if ($_FILES["file"]["error"] == 0
		&& $_FILES["file"]["type"] == "image/jpeg"
		&& $_FILES["file"]["size"] < 5000000) {
		
		unlink("incl/img/" . $lastId . ".jpg");
		// Note: Take care of the permission of destination folder (hints: current user is apache)
		if (move_uploaded_file($_FILES["file"]["tmp_name"], "incl/img/" . $lastId . ".jpg")) {
		
			//create thumbnail if successful upload
			$image = new SimpleImage();
			$image->load("incl/img/" . $lastId . ".jpg");
			$image->resizeToWidth(150);
			$image->save("incl/img/" . $lastId . "_tn.jpg");
			
			// redirect back to original page; you may comment it during debug
			header('Location: admin.php');
			exit();
		}
	}
	// Only an invalid file will result in the execution below
	
	// TODO: remove the SQL record that was just inserted
	
	global $db;
	$db = ierg4210_DB();
	
	$q = $db->prepare("DELETE FROM products WHERE pid = ?");
	$q->execute(array($_POST['pid']));

	
	// To replace the content-type header which was json and output an error message
	header('Content-Type: text/html; charset=utf-8');
	echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
	exit();
}

/*
function ierg4210_prod_fetch_by_catid() {
	// input validation or sanitization
	if (!is_numeric($_GET['catid']))
		throw new Exception("invalid-category");	
	$catid_t = (int) $_GET['catid'];
	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("SELECT * FROM products WHERE catid = ?");
	$q->execute(array($catid_t));
	return $q->fetchAll();
}
*/

function ierg4210_prod_delete() {
	// input validation or sanitization
	if (!is_numeric($_POST['pid']))
		throw new Exception("invalid-category");
	$_POST['pid'] = (int) $_POST['pid'];
	
	//delete corresponding img
	unlink("incl/img/" . $_POST['pid'] . ".jpg");
	unlink("incl/img/" . $_POST['pid'] . "_tn.jpg");
	
	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("DELETE FROM products WHERE pid = ?");
	return $q->execute(array($_POST['pid']));
}
/*
function ierg4210_prod_fetch_by_pid() {
	if (!is_numeric($_GET['pid']))
		throw new Exception("invalid-category");	
	$pid_t = (int) $_GET['pid'];
	
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("SELECT * FROM products WHERE pid = ?");
	if ($q->execute(array($pid_t)))
		return $q->fetchAll();
}
*/
//header('Content-Type: text/html; charset=utf-8');
//validate the token required first
//include_once('lib/auth.php');
//if(validatecookie() == false) {
//	header('Location: login.php', true, 302);
//}

//include_once('lib/csrf.php');

function ierg4210_changepw(){

	if (empty($_POST['orig_pw']) || empty($_POST['new_pw']) || empty($_POST['new_pw2'])
		|| !preg_match("/^[\w@#$%\^\&\*\-]+$/", $_POST['orig_pw'])
		|| !preg_match("/^[\w@#$%\^\&\*\-]+$/", $_POST['new_pw'])
		|| !preg_match("/^[\w@#$%\^\&\*\-]+$/", $_POST['new_pw2'])) {
		throw new Exception('Invalid orignal or new password');
	} else {
		// Checking of new password
		if ($_POST['new_pw'] != $_POST['new_pw2']) {
			throw new Exception('Invalid new password');
		} else {
		// Checking of old password
			global $db;
			$db = ierg4210_DB();
			$q=$db->prepare('SELECT * FROM useraccount WHERE email = ?');
			$email = validatecookie();
			$q->execute(array($email));
			$r=$q->fetch();
			if(count($r) != 0){
				$password = $_POST['orig_pw'];
				$saltpassword=hash_hmac('sha1', $password, $r['salt']);
				if($saltpassword == $r['password']){
					//replace the old one with new password
					
					$newpassword=hash_hmac('sha1', $_POST['new_pw'],$r['salt']);
					$q2=$db->prepare('UPDATE useraccount SET password = (?) WHERE email = (?)');
					$q2->execute(array($newpassword,$email));
					
					// clear the cookies and session
					setcookie('auth',"", time()-3600,"/", "secure.shop107.ierg4210.org", 1, 1);
					session_destroy();
					// redirect to login page after logout
					session_regenerate_id(); 
					return $q2;
					//header('Location: login.php');
					//exit();
					//return true;
					
				} else {
					throw new Exception('Wrong Original password');
				}
			} else {
				throw new Exception('Wrong Original password');
			}
		}
	}

}

header('Content-Type: application/json');

if (!validatecookie())
{
	header('Location: login.php');
	exit();
}

// input validation
if (empty($_REQUEST['action']) || !preg_match('/^\w+$/', $_REQUEST['action'])) {
	echo json_encode(array('failed'=>'undefined'));
	exit();
}

// The following calls the appropriate function based to the request parameter $_REQUEST['action'],
//   (e.g. When $_REQUEST['action'] is 'cat_insert', the function ierg4210_cat_insert() is called)
// the return values of the functions are then encoded in JSON format and used as output
try {
	if(csrf_verifyNonce($_REQUEST['action'], $_POST['nonce']) == false) {
		throw new Exception('csrf-attack');
	}
	if (($returnVal = call_user_func('ierg4210_' . $_REQUEST['action'])) === false) {
		if ($db && $db->errorCode()) 
			error_log(print_r($db->errorInfo(), true));
		echo json_encode(array('failed'=>'1'));
	}
	echo 'while(1);' . json_encode(array('success' => $returnVal));
} catch(PDOException $e) {
	error_log($e->getMessage());
	echo json_encode(array('failed'=>'error-db'));
} catch(Exception $e) {
	echo 'while(1);' . json_encode(array('failed' => $e->getMessage()));
}

?>