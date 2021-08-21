<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Musiclap - Music is your solution!</title>
<link href="styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="cover">
<img src="pic/head.jpg" class="cov"/></div>
<header>
<h1>Musiclap</h1>
<h3>Music is your solution!</h3>
</header>

<div class="nav_h">
</div>

<?php	
include_once('lib/db.inc.php');
	
	$db = ierg4210_DB();
	$q = $db->prepare("SELECT * FROM categories LIMIT 100;");
	$q->execute();
	$cats = $q->fetchAll();

?> 

<nav>
<ul>
Categories:
<li><a href="index.php">All</a></li>
<?php
	for ($i = 0; $i < sizeof($cats); $i++) {
	?><li><a href="index.php?catid=<?php echo $cats[$i]['catid']; ?>"><?php echo $cats[$i]['name'];?></a></li><?php } echo "\n";?>
    <li><a href = "admin.php">Admin Page</a></li>
</ul>
   
</nav>


<div class="cart">
<h4>Shopping Cart (Total:$<span id='total'>0</span>)</h4>
<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="POST">
<ul id='shoppinglist'>
</ul>
</form>
</div>

<?php
	include_once('lib/db.inc.php');
	
	if (!is_numeric($_GET['prod'])) {
		header("index.php");
		exit();
	}
	$pid_t = (int)$_GET['prod'];	
	global $db2;
	$db2 = ierg4210_DB();
	$q2 = $db2->prepare("SELECT * FROM products WHERE pid = ?");
	$q2->execute(array($pid_t));
	$prodt = $q2->fetch();
	
	$q3 = $db2->prepare("SELECT * FROM categories WHERE catid = ?");
	$q3->execute(array($prodt['catid']));
	$catt = $q3->fetch();
	
?>


<ul class="content">
<a href="index.php">Home</a> > <a href="index.php?catid=<?php echo urlencode($prodt['catid']); ?>"><?php echo htmlspecialchars($catt['name']); ?></a> > <?php echo htmlspecialchars($prodt['name']); ?><p></p>
<li class="img">
<img src="incl/img/<?php echo htmlspecialchars($prodt['pid']); ?>.jpg"  class="large"/></li>
<li>

<strong><?php echo htmlspecialchars($prodt['name']); ?></strong><br>
$<?php echo htmlspecialchars($prodt['price']); ?><br>
<input name="Submit1" type="button" value="Add to Cart" onClick="ui.cart.add(<?php echo htmlspecialchars($prodt['pid']);?>)">
</li>
<li class = "descript">
<?php echo htmlspecialchars($prodt['description']); ?>
</ul>


<script type="text/javascript" src="incl/myLib.js"></script>
<script type="text/javascript" src="incl/ui.js"></script>
<script type="application/javascript">
ui.cart.updateHTML();
</script>
</body>
</html>
