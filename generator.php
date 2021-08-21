<?php 
echo ($salt = mt_rand()) . "<br/>"; 
echo hash_hmac('sha1', 'tct315778', $salt)
?>
