(function() {
	window.ui = {}; 
	window.ui.cart = {}; 
	var storage = {};
	

	 ui.cart.updateHTML = function() {
		storage = window.localStorage.getItem('cart_storage');
		// el('shoppinglist').innerHTML = "wowggggow";
		if (!storage|| storage == '{}') { 
			el('total').innerHTML = 0;
			el('shoppinglist').innerHTML = 'NO items!';
		} else {
			storage = storage ? JSON.parse(storage) : {}; //other way??
			var postObject = {
				action : "prod_fetchAll"
				//list : storage
			};
			myLib.get2(postObject, function(json){ 
			var cart = [];
			// loop over json to construct HTML w/output sanitizations 
			// calculate total price 
			cart.push('<form method="POST" id="shoppingCart" name="shoppingCart"  action="https://www.sandbox.paypal.com/cgi-bin/webscr" onsubmit="return ui.cart.submit.(this)">');
			for (var listItems = [], quantity = 0, total = 0, i = 0, j = 0, prod; prod = json[i]; i++) {
					quantity = parseInt(storage[parseInt(prod.pid)]);	
					if ( quantity > 0 ) {
					j = j + 1;
					listItems.push('<li id="prod', parseInt(prod.pid), '"><span class="name">', prod.name.escapeHTML(), '</span><br><input type="number" class = "quant" min="0" max="99" id="quan', parseInt(prod.pid), '" onblur="ui.cart.setQty(', parseInt(prod.pid), ',this.value)" value="',quantity,'" /><span class="price">@', parseInt(prod.price), '</span></li>');
					listItems.push('<input type="hidden" name="item_number_', j, '" value=', parseInt(prod.pid), '>');
					listItems.push('<input type="hidden" name="item_name_', j, '" value="', prod.name.escapeHTML(), '">');
					listItems.push('<input type="hidden" name="amount_', j, '" value="', prod.price, '">');
					listItems.push('<input type="hidden" name="quantity_', j, '" value="', parseInt(storage[prod.pid]), '">');			
					//listItems
						//el('quan 1').value = "10"; //parseInt(quantity);
						//window.alert();
						
						total += quantity * parseInt(prod.price);
						
					} else {
						continue;
					}
			}
			//for 
				var hidden = [];
				hidden.push('<input type="hidden" name="cmd" value="_cart">');
				hidden.push('<input type="hidden" name="upload" value="1">');
				hidden.push('<input type="hidden" name="business" value="chants_1354454038_biz@gmail.com">');
				hidden.push('<input type="hidden" name="currency_code" value="HKD">');
				hidden.push('<input type="hidden" name="charset" value="utf-8">');
				hidden.push('<input type="hidden" name="custom" id="custom" value="0">');
				hidden.push('<input type="hidden" name="invoice" id="invoice" value="0">');
				hidden.push('<input onclick="checkoutFunction();"  type="button" name="checkout" value="Checkout">');
				hidden.push('<button type="button" name="ClearCart" onclick = "ui.cart.clear()">Clear</button>');
				
				el('total').innerHTML = parseInt(total);
				el('shoppinglist').innerHTML = cart.join('')+listItems.join('')+hidden.join('');
				// put HTML into the hover-able shopping list


			})

		}
	}

	
	checkoutFunction = function() {
		storage = window.localStorage.getItem('cart_storage');
		//storage = storage ? JSON.parse(storage) : {};
		myLib.post3({action:'digest', info: storage}, function(json) {
		//alert(json['custom']);
			el('custom').value = json['digest'];
			el('invoice').value = json['lastid'];
			document.shoppingCart.submit();
			//window.localStorage.clear();
			window.localStorage.removeItem('cart_storage');
			return true; 
		});
		return false;   
	}
	
	ui.cart.setQty = function(pid, qty) {
		storage = window.localStorage.getItem('cart_storage');
		storage = storage ? JSON.parse(storage) : {}; //other way??
		if (qty > 0) {
			storage[pid] = qty;
		} else {
			delete storage[pid];
		}
		window.localStorage.setItem('cart_storage', JSON.stringify(storage));
		ui.cart.updateHTML();
	}
	
	ui.cart.add = function(pid) {
		storage = window.localStorage.getItem('cart_storage');
		storage = storage ? JSON.parse(storage) : {};
		if (storage[pid]) {
			storage[pid]++;
		} else
			storage[pid] = 1;
		window.localStorage.setItem('cart_storage', JSON.stringify(storage));
		window.alert('added!');
		ui.cart.updateHTML();
	}

		ui.cart.clear = function() {
		window.localStorage.clear();
		//window.localStorage.setItem('cart_storage', JSON.stringify(storage));
		ui.cart.updateHTML();
	}
	
	

})();