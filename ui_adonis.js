(function(){
	function j(d){
		for(var a=[],b=0,e=0,g=1,h,i,f;f=d[e];e++,g++)
			c[f.pid]&&(a.push("<li>"),
			a.push('<input type="hidden" name="item_number_'+g+'" value="'+f.pid+'" />'),
			a.push(f.name.escapeHTML()),
			a.push('<input type="hidden" name="item_name_'+g+'" value="'+f.name.escapeHTML()+'" />'),
			h=Math.abs(parseInt(c[f.pid])),
			a.push('<input type="number" name="quantity_'+g+'" min="0" max="99" maxlength="2" class="qty" value="'+h+'" onblur="ui.cart.update('+f.pid+',this.value)" />'),
			i=Math.abs(parseFloat(f.price)),
			a.push('<input type="hidden" name="amount_'+g+'" value="'+parseFloat(f.price)+'" />'),
			a.push("<span>$"+i+"</span>"),
			b+=h*i,a.push("</li>"));
			document.getElementById("cartTotal").innerHTML=b;
			document.getElementById("cart").innerHTML=1
			<a.length?a.join(""):"No item!";
			window.mobileUI&&window.mobileUI.setCartTotal(b)
	}
	var e=window.myLib=window.myLib||{};
	
	e.post2=function(b,a){
		e.processJSON("process.php?rnd="+(new Date).getTime(),b,a,{method:"POST"})
	};
	
	window.ui=window.ui||{cart:{storage:{}}};
	
	var b=ui.cart,c=b.storage;
	
	b.getSavedStore=function(){
		c=(c=window.localStorage.getItem("cart_storage"))?JSON.parse(c):{}
	};
	
	b.add=function(d){
		b.update(d,(c[d]||0)+1)
	};
	
	b.setVisibility=function(b){
		var a=document.querySelector(".cartList").classList;b?a.add("display"):a.remove("display")};
		b.toggleVisibility=function(){document.querySelector(".cartList").classList.toggle("display")
	};
	
	b.update=function(d,a){
		var e=!1,a=parseInt(a);0==a?delete c[d]:0>a||(e=c[d]?!1:!0,c[d]=a);
		window.localStorage.setItem("cart_storage",JSON.stringify(c));
		b.display(e)
	};
	
	b.display=function(d){
		d||!b.prodDetails?c&&e.post2({action:"fetchProducts",list:JSON.stringify(c)},function(a){j(b.prodDetails=a)}):j(b.prodDetails)
	};
	
	b.reset=function(){
		c={};
		window.localStorage.setItem("cart_storage",JSON.stringify(c));
		document.getElementById("cartTotal").innerHTML="0";
		document.getElementById("cart").innerHTML="No item!"
	};
	
	b.submit=function(b){
		var a=parseFloat(document.getElementById("cartTotal").innerHTML);
		if(!c||0>=a)return!1;
		e.post2({action:"buildOrder",list:JSON.stringify(c)},
		function(a){
			if(!a.digest||!a.invoice)return alert("error occurred!");
			c={};
			window.localStorage.setItem("cart_storage",JSON.stringify(c));
			b.custom.value=a.digest;
			b.invoice.value=a.invoice;
			b.submit()}
		);
		
		return!1
	};
	
	b.getSavedStore();
	b.display()
})();