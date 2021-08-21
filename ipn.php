<?php
include_once('lib/db.inc.php');

	error_reporting(E_ALL ^ E_NOTICE);
	error_log(print_r("This is ipn.php",true));
	$header = "";
	$req = 'cmd=_notify-validate';
	if(function_exists('get_magic_quotes_gpc'))
	{ $get_magic_quotes_exists = true; }
	foreach ($_POST as $key => $value)
	{
		if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1)
		{
			$value = urlencode(stripslashes($value));
		}
		else
		{
			$value = urlencode($value);
		}
		$req .= "&$key=$value";
	}
	
	$header .= "POST /cgi-bin/webscr HTTP/1.1\r\n";
	$header .= "Host: www.sandbox.paypal.com\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
	$fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
	
	if (!$fp)
	{
		error_log(print_r("Cannot connect to paypal",true));
		exit();
	}
	else
	{
		error_log(print_r("Connected to paypal",true));
		fputs ($fp, $header . $req);
		while (!feof($fp))
		{	
			$iserr = false;
			$res = fgets ($fp, 1024);
			if (strcmp ($res, "VERIFIED\r\n") == 0)
			{
				//foreach ($_POST as $key => $value){ error_log(print_r($key."...".$value,true)); }
				
				if (empty($_POST['payment_status']) || $_POST['payment_status'] != 'Completed')
				{
					error_log(print_r("Error:Payment ".$_POST['payment_status'],true));
					$iserr = true;
				}
				
				if (empty($_POST['txn_type']) || $_POST['txn_type'] != 'cart')
				{
					error_log(print_r("Error:txn_type ".$_POST['txn_type'],true));
					$iserr = true;
				}
				
				$items = array();	
				for($i = 1; $pid = (int)$_POST['item_number'.$i];$i++)
				{
					$items[$pid] = array();
					if ((int)$_POST['quantity'.$i]< 1) continue;
					$items[$pid]['qty'] = (int)$_POST['quantity'.$i];
					$items[$pid]['price'] = (int)number_format($_POST['mc_gross_'.$i]/$items[$pid]['qty'],1);
				}
								
				$digest = array();
				array_push($digest,"HKD","chants_1354454038_biz@gmail.com");
				$sum = 0;
				foreach($items as $pid => $item)
				{
					array_push($digest,$pid,$item['qty'],$item['price']);
					$sum += $item['qty']*$item['price'];
				}
				
				if (empty($_POST['mc_gross']) || $_POST['mc_gross'] != $sum)
				{
					error_log(print_r("Error:mc_gross ".$_POST['mc_gross'],true));
					$iserr = true;
				}
				
				$digest[] = $sum;
				$digestString = implode('-', $digest);
				global $db;
				$db = ierg4210_DB();
				$q = $db->prepare('SELECT salt, digest, time FROM orders WHERE id = ?');
				if ($q->execute(array($_POST['invoice']))) $r = $q->fetch();
				error_log(print_r($r['time']."AAA".$digestString,true));
				$digestString = hash_hmac('sha1', $digestString, $r['salt']);
				if (empty($_POST['transaction_subject']) || $r['digest'] != $digestString)
				{
					error_log(print_r("Error:digest ".$r['digest']."AAA".$digestString,true));
					$iserr = true;
				}					
				
				$q = $db->prepare('SELECT id FROM orders WHERE txn_id = ?');
				if ($q->execute(array($_POST['txn_id']))) $r = $q->fetch();
				if ($q->rowCount() > 0)
				{
					error_log(print_r("Error:already processed.",true));
					$iserr = true;					
				}
				
				if ($iserr) exit();
				else
				{
					error_log(print_r("Success!",true));
					$q = $db->prepare("UPDATE orders SET txn_id = ?, time = ? WHERE id = ? ");
					$q->execute(array($_POST['txn_id'],time(),$_POST['invoice']));
				}
			}
			else if (strcmp ($res, "INVALID\r\n") == 0)
			{
				error_log(print_r("Paypal says it's invalid",true));
			}
		}
	}
	fclose ($fp);
?>