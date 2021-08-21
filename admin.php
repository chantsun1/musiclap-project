<?php
session_start();
include_once('lib/db.inc.php');
include_once('lib/csrf.php');
include_once('lib/auth.php');
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	
	<title>IERG4210 Shop - Admin Panel</title>
	<link href="incl/admin.css" rel="stylesheet" type="text/css"/>
</head>

<body>

<?php
	//validate the token required first
if (!validatecookie())
{
	header('Location: login.php');
	exit();
}
?>

<h1>IERG4210 Shop - Admin Panel</h1>
<form id="logout" method="POST" action="auth-process.php?action=<?php echo ($action = 'logout'); ?>" >
<br>Login in as <?php echo validatecookie(); ?>
<input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action); ?>"/>
<br>
<input type="submit" value="logout" />
</form>
<input id="changepw_button" type="submit" value="change password" />

<article id="main">
<section id="categoryPanel">
	<fieldset>
		<legend>New Category</legend>
		<form id="cat_insert" method="POST" action="admin-process.php?action=<?php echo ($action1 = 'cat_insert'); ?>" onSubmit="return false;">
			<label for="cat_insert_name">Name</label>
			<div><input id="cat_insert_name" type="text" name="name" required="true" pattern="^[\w\- ]+$" /></div>
			<input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action1); ?>"/>
			<br>
			<input type="submit" value="Submit" />
		</form>
	</fieldset>
	
	<!-- Generate the existing categories here -->
	<ul id="categoryList"></ul>
</section>

<section id="ChangePwPanel" class="hide">
	<fieldset>
		<legend>Change Password</legend>
		<form id="changepw" method="POST" action="admin-process.php?action=<?php echo ($action00 = 'changepw') ?>" onSubmit="return false;">
			<div>Original Password: <br><input id="orig_pw" type="password" name="orig_pw" required="true" pattern="^[\w\- ]+$" /></div>
			<div>New Password:<br> <input id="new_pw" type="password" name="new_pw" required="true" pattern="^[\w\- ]+$" /></div>
			<div>Enter New Password again:<br><input id="new_pw2" type="password" name="new_pw2" required="true" pattern="^[\w\- ]+$" /></div>
			<input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action00); ?>"/>
			<input type="submit" value="Submit" /> <input type="button" id="changepw_cancel" value="Cancel" />
		</form>
	</fieldset>
</section>


<section id="categoryEditPanel" class="hide">
	<fieldset>
		<legend>Editing Category</legend>
		<form id="cat_edit" method="POST" action="admin-process.php?action=<?php echo ($action2 = 'cat_edit') ?>" onSubmit="return false;">
			<label for="cat_edit_name">Name</label>
			<div><input id="cat_edit_name" type="text" name="name" required="true" pattern="^[\w\- ]+$" /></div>
			<input type="hidden" id="cat_edit_catid" name="catid" />
			<input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action2); ?>"/>
			<input type="submit" value="Submit" /> <input type="button" id="cat_edit_cancel" value="Cancel" />
		</form>
	</fieldset>
</section>

<section id="productPanel">
	<fieldset>
		<legend>New Product</legend>
		<form id="prod_insert" method="POST" action="admin-process.php?action=<?php echo ($action3 = 'prod_insert') ?>" enctype="multipart/form-data">
			<label for="prod_insert_catid">Category *</label>
			<div><select id="prod_insert_catid" name="catid"></select></div>

			<label for="prod_insert_name">Name *</label>
			<div><input id="prod_insert_name" type="text" name="name" required="true" pattern="^[\w\- ]+$" /></div>

			<label for="prod_insert_price">Price *</label>
			<div><input id="prod_insert_price" type="number" name="price" required="true" pattern="^[\d\.]+$" /></div>

			<label for="prod_insert_description">Description</label>
			<div><textarea id="prod_insert_description" name="description" pattern="^[\w\-, ]$"></textarea></div>

			<label for="prod_insert_name">Image *</label>
			<div><input type="file" name="file" required="true" accept="image/jpeg" /></div>
			<input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action3); ?>"/>
			<input type="submit" value="Submit" />
		</form>
	</fieldset>
    	<!-- Generate the corresponding products here -->


	<!-- Generate the corresponding products here -->
	<ul id="productList"></ul>

</section>

	
	<section id="productEditPanel" class="hide">
		<!-- 
			Design your form for editing a product's catid, name, price, description and image	
			- the original values/image should be prefilled in the relevant elements (i.e. <input>, <select>, <textarea>, <img>)
			- prompt for input errors if any, then submit the form to admin-process.php (AJAX is not required)
		-->
    <fieldset>
		<legend>Editing Product</legend>
		<form id="prod_edit" method="POST" action="admin-process.php?action=<?php echo ($action4 = 'prod_edit') ?>" enctype="multipart/form-data">
			<label for="prod_edit_catid">Category *</label>
			<div><select id="prod_edit_catid" name="catid"></select></div>

			<label for="prod_edit_name">Name *</label>
			<div><input id="prod_edit_name" type="text" name="name" required="true" pattern="^[\w\.\-, ]+$" /></div>

			<label for="prod_edit_price">Price *</label>
			<div><input id="prod_edit_price" type="number" name="price" required="true" pattern="^[\d\.]+$" /></div>

			<label for="prod_edit_description">Description</label>
			<div><textarea id="prod_edit_description" name="description" pattern="^[\w\s\-\., ]$"></textarea></div>

			<label for="prod_edit_name">Image</label>
			<div><input type="file" name="file" accept="image/jpeg|image/gif|image/png" pattern="([^\s]+(?=\.(jpg|gif|png))\.\2)"  /></div>
			<div id='editImage'></div>
			<input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action4); ?>"/>
			<input type="hidden" id="prod_edit_pid" name="pid" />

			<input type="submit" value="Submit" /> <input type="button" id="prod_edit_cancel" value="Cancel" />
		</form>
	</fieldset>
	</section>
<div class="clear"></div>
</article>

<?php ($action8 = 'prod_delete') ?>
<input id="nonce_pd" type="hidden" name="nonce" value="<?php echo csrf_getNonce($action8); ?>"/>
<?php ($action9 = 'cat_delete') ?>
<input id="nonce_cd" type="hidden" name="nonce" value="<?php echo csrf_getNonce($action9); ?>"/>

<script type="text/javascript" src="incl/myLib.js"></script>
<script type="text/javascript">

(function(){

	function updateUI() {
		myLib.get2({action:'cat_fetchall'}, function(json){
			// loop over the server response json
			//   the expected format (as shown in Firebug): 
			for (var options = [], listItems = [],
					i = 0, cat; cat = json[i]; i++) {
				options.push('<option value="' , parseInt(cat.catid) , '">' , cat.name.escapeHTML() , '</option>');
				listItems.push('<li id="cat' , parseInt(cat.catid) , '"><span class="name">' , cat.name.escapeHTML() ,
				 '</span> <span class="delete">[Delete]</span> <span class="edit">[Edit]</span></li>');
			}
			el('prod_insert_catid').innerHTML = '<option></option>' + options.join('');
			el('prod_edit_catid').innerHTML = '<option></option>' + options.join('');
			el('categoryList').innerHTML = listItems.join('');
		});
		el('productList').innerHTML = '';
	}
	updateUI();
	
	el('categoryList').onclick = function(e) {
		if (e.target.tagName != 'SPAN')
			return false;
		
		var target = e.target,
			parent = target.parentNode,
			id = target.parentNode.id.replace(/^cat/, ''),
			name = target.parentNode.querySelector('.name').innerHTML;
		
		// handle the delete click
		if ('delete' === target.className) {
			noncet = document.getElementById("nonce_cd").value;
			confirm('Sure?') && myLib.post({action: 'cat_delete', catid: id, nonce: noncet}, function(json){
				alert('"' + name + '" is deleted successfully!');
				updateUI();
			});
		
		// handle the edit click
		} else if ('edit' === target.className) {
			// toggle the edit/view display
			el('categoryEditPanel').show();
			el('categoryPanel').hide();
			
			// fill in the editing form with existing values
			el('cat_edit_name').value = name;
			el('cat_edit_catid').value = id;
		
		//handle the click on the category name
		} else {
			el('prod_insert_catid').value = id;
			// populate the product list or navigate to admin.php?catid=<id>
			
			myLib.get2({action:'prod_fetch_by_catid', catid: id}, function(json){
			// loop over the server response json
			//   the expected format (as shown in Firebug): 
			for (var listItems = [],
					i = 0, prod; prod = json[i]; i++) {
				listItems.push('<li id="prod' , parseInt(prod.pid) , '"><span class="name">' , prod.name.escapeHTML() , '</span> <span class="delete">[Delete]</span> <span class="edit">[Edit]</span></li>');
			}
			el('productList').innerHTML = listItems.join('');
		});
			
		}
	}
	
	
	el('cat_insert').onsubmit = function() {
		return myLib.submit(this, updateUI);
	}
	
	el('changepw').onsubmit = function() {
		return myLib.submit(this, function() {
			window.location = "https://secure.shop107.ierg4210.org/admin.php";
		});
	}
	
	el('changepw_cancel').onclick = function() {
		// toggle the edit/view display
		el('ChangePwPanel').hide();
		el('categoryPanel').show();
		el('productPanel').show();
	}
	
	el('changepw_button').onclick = function() {
		el('ChangePwPanel').show();
		el('categoryPanel').hide();
		el('categoryEditPanel').hide();
		el('productPanel').hide();
		el('productEditPanel').hide();
	}

	el('cat_edit').onsubmit = function() {
		return myLib.submit(this, function() {
			// toggle the edit/view display
			el('categoryEditPanel').hide();
			el('categoryPanel').show();
			updateUI();
		});
	}
	
/*	el('prod_edit').onsubmit = function() {
		return myLib.submit(this, function() {
			// toggle the edit/view display
			el('productEditPanel').hide();
			el('productPanel').show();
			updateUI();
		});
	}
*/
	el('cat_edit_cancel').onclick = function() {
		// toggle the edit/view display
		el('categoryEditPanel').hide();
		el('categoryPanel').show();
	}
	el('prod_edit_cancel').onclick = function() {
		// toggle the edit/view display
		el('productEditPanel').hide();
		el('productPanel').show();
	}
	
	el('productList').onclick = function(e) {

		var target = e.target,
			parent = target.parentNode,
			id = target.parentNode.id.replace(/^prod/, ''),
			name = target.parentNode.querySelector('.name').innerHTML;
		
		// handle the delete click
		if ('delete' === target.className) {
			noncet2 = document.getElementById("nonce_pd").value;
			confirm('Sure?') && myLib.post({action: 'prod_delete', pid: id, nonce: noncet2}, function(json){
				alert('"' + name + '" is deleted successfully!');
				updateUI();
			});
		
		// handle the edit click
		} else if ('edit' === target.className) {
			
			myLib.get2({action:'prod_fetch_by_pid', pid: id}, function(json){
				var prod = json[0];
				// fill in the editing form with existing values
				el('prod_edit_pid').value = prod.pid;
				el('prod_edit_catid').value = prod.catid;
				el('prod_edit_name').value = prod.name;
				el('prod_edit_price').value = prod.price;
				el('prod_edit_description').value = prod.description;
				//el('editImage').innerHTML = "<img id='smallImage' src="incl/img/<p" />;
			});
			// toggle the edit/view display
			el('productEditPanel').show();
			el('productPanel').hide();
		
		}
		
	}

})();
</script>
</body>
</html>
