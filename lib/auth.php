<?php 

function validatecookie()
{
	if(!empty($_SESSION['auth']))
		return $_SESSION['auth']['em'];
	if(!empty($_COOKIE['auth']))
	{
		//stripslashes() Returns a string with backslashes stripped off.
		// (\' becomes ' and so on.)
		if($token = json_decode(stripslashes($_COOKIE['auth']),true))
		{
			if (time() > $token['exp']) return false; // to expire the user
			global $db;
			$db = ierg4210_DB();
			$q = $db->prepare('SELECT salt, password FROM useraccount WHERE email = ?');
			$q->execute(array($token['em']));
			
			if($r=$q->fetch())
			{
				//hash_hmac('sha1', $exp.$r['password'], $r['salt'])
				$realk=hash_hmac('sha1', $token['exp'].$r['password'], $r['salt']);
				if($realk == $token['k'])
				{
					$_SESSION['auth'] = $token;
					return $token['em'];
				}
			}
		}
	}
return false;
}

/*$q=$db->prepare('SELECT * FROM account WHERE email = ?');
$q->execute(array($email));
if($r=$q->fetch()){
//Check if the hash of the password is same as saved in database
//If yes, create authentication information in cookies and session
//program code on next slide
}

//expected format: $pw=hash_hmac('sha1', $plainPW, $salt);
$saltPassword=hash_hmac('sha1', $password, $r['salt'])
if($saltPassword == $r['password']){
$exp = time() + 3600 * 24 * 3; // 3days
$token = array(
'em'=>$r['email'], 'exp'=>$exp, 'k'=>hash_hmac('sha1', $exp.$r['password'], $r['salt'])
// create the cookie, make it HTTP only
// setcookie() must be called before printing anything out
setcookie('t4210', json_encode($token), $exp,'','',false,true);
// put it also in the session
$_SESSION['t4210'] = $token;
return true;
}*/

?>