<?php
include_once('lib/db.inc.php');
/*
function ierg4210_prod_fetch() {
	//$list = json_decode($_GET['list'], true);
	$list = $_GET['list'];
	$pid = array();
	
	foreach ($list as $key => $val)
	{
		$pids[]=$key;
	}
		
	//generate query
	$query = "SELECT pid,name,price FROM products WHERE pid = (?)";
	$i = sizeof($pids);
	while($i > 1)
	{
		$query = $query." OR pid = (?)";
		$i--;
	}
	
	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare($query);
	if ($q->execute($pids))
		return $q->fetchAll();
}

function ierg4210_prod_fetch_spec() {
	$prods = json_decode($_GET['json'], true);
	
	$pids = array();
	foreach ($prods as $key => $val)
	{
		if ($val['qty']) $pids[]=$key;
	}
	
	//generate query
	$query = "SELECT pid,name,price FROM products WHERE pid = ?";
	$i = sizeof($pids);
	while($i > 1)
	{
		$query = $query." OR pid = ?";
		$i--;
	}
	
	// DB manipulation
	global $db;	
	$db = ierg4210_DB();
	$q = $db->prepare($query);//? or pid = ? or pid =?;
	if ($q->execute($pids)) return $q->fetchAll();
}
*/
function ierg4210_cat_fetchall() {
	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("SELECT * FROM categories LIMIT 100;");
	if ($q->execute())
		return $q->fetchAll();
}

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

function ierg4210_prod_fetch_by_pid() {
	if (!is_numeric($_GET['pid']))
		throw new Exception("invalid-product");	
	$pid_t = (int) $_GET['pid'];
	
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("SELECT * FROM products WHERE pid = ?");
	if ($q->execute(array($pid_t)))
		return $q->fetchAll();
}

function ierg4210_prod_fetchAll() {
	//$list = json_decode($_GET['list'], true);
	//$list = array_keys($list);
	// DB manipulation
	global $db;
	$db = ierg4210_DB();
	$q = $db->prepare("SELECT pid,name,price FROM products;");
	if ($q->execute())
		return $q->fetchAll();
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