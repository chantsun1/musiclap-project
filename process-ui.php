<?php
include_once('lib/db.inc.php');

function ierg4210_digest() {
	$item=json_decode($_POST['info'],true);
	//pid array preparation
	$pids = array();	
	foreach ($item as $k => $v) {
		$pids[]=$k;
		//echo "\$a[$k] => $v.\n";	
	}
	
	global $db;
	$db = ierg4210_DB();
	
	//price array preparation
	$prices = array();
	foreach ($pids as $id) {
	$q = $db->prepare("SELECT price FROM products where pid = (?)");
	$q->execute(array($id));
	$r = $q->fetch();
	//echo "\\$q.\n";
	foreach ($r as $k => $p)
		$prices[$id]= (int)$p;
	}
	//return $prices;
	
	$digest = array();
	array_push($digest,"HKD","chants_1354454038_biz@gmail.com");
	$sum = 0;
	
	foreach($item as $pid => $quan)
	{
		if ($quan < 1) {
			continue;
		} else {
		array_push($digest,$pid,$quan,$prices[$pid]);
		$sum += $quan*$prices[$pid];
		}
	}
	//return $sum;
	$digest[] = $sum;
	$digestString = implode('-', $digest);
	$salt = mt_rand();
	error_log(print_r("AAA".$digestString,true));
	$digestString = hash_hmac('sha1', $digestString, $salt);
	//return $digestString;
	
	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("INSERT INTO orders VALUES (null,?,?,null,?,?)");
	$q->execute(array($digestString,$salt,implode('-', $pids),0));
	$ltd = $db->lastInsertId('orders');
	
	$output = array();
	$output['digest'] = $digestString;
	$output['lastid'] = $ltd;
	return($output);
}

header('Content-Type: application/json');

// input validation
if (empty($_REQUEST['action']) || !preg_match('/^\w+$/', $_REQUEST['action'])) {
	echo json_encode(array('failed'=>'undefined'));
	exit();
}

// The following calls the appropriate function based to the request parameter $_REQUEST['action'],
//   (e.g. When $_REQUEST['action'] is 'cat_insert', the function ierg4210_cat_insert() is called)
// the return values of the functions are then encoded in JSON format and used as output
try {
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