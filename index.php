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
	?><li><a href="index.php?catid=<?php echo urlencode($cats[$i]['catid']); ?>"><?php echo htmlspecialchars($cats[$i]['name']); ?></a></li><?php } echo "\n";?>
    <li><a href = "admin.php">Admin Page</a></li>
</ul>
   
</nav>

<?php

	if(isset($_GET['catid'])) { //&& ((int)$_GET['catid'] <= (sizeof($cats)))) {
		if (!is_numeric($_GET['catid'])) {
			header("index.php");
			exit();
		}
		$start = (int)$_GET['catid'];
		$end = $start;
	} else {
		$start = 0;
		$end = (int)sizeof($cats);
	}
?>

<ul class="tableless">	

<a href="index.php">Home</a> > <?php echo htmlspecialchars($cats[$start-1]['name']); ?> <p></p>

<?php
for ($i = $start; $i <= $end; $i++) {
	?>
<?php
	$db = ierg4210_DB();
	$q = $db->prepare("SELECT * FROM products WHERE catid = ?");
	$q->execute(array($cats[$i-1]['catid']));
	$prods = $q->fetchAll();	
?>
	<?php
      for ($j =0; $j < sizeof($prods); $j++) {
	  	?>
      <li>
      <a href="details.php?prod=<?php echo urlencode($prods[$j]['pid'])?>"><img src="incl/img/<?php echo htmlspecialchars($prods[$j]['pid'])?>_tn.jpg" class="thumbn"/></a><br>
        <a href="details.php?prod=<?php echo urlencode($prods[$j]['pid'])?>"><?php echo htmlspecialchars($prods[$j]['name'])?></a><br>
        HK$<?php echo htmlspecialchars($prods[$j]['price'])?><br>
        <input type="button" value="Add to Cart" onClick="ui.cart.add(<?php echo htmlspecialchars($prods[$j]['pid'])?>)" />
        </li>
	<?php
	}
	?>
<?php
}
?>
</ul>

<div class="cart">
<h4>Shopping Cart (Total:$<span id='total'>0</span>)</h4>

<ul id='shoppinglist'>
</ul>

</div>


<script type="text/javascript" src="incl/myLib.js"></script>
<script type="text/javascript" src="incl/ui.js"></script>
<script type="application/javascript">
ui.cart.updateHTML();
</script>
</body>
</html>
