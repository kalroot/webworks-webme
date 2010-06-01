function os_add_one(ev){
	var tr=$(ev.target).closest('tr');
	var md5=tr.attr('id');
	var amt=tr.data('amt');
	os_set_amt(md5,amt+1,tr);
}
function os_subtract_one(ev){
	var tr=$(ev.target).closest('tr');
	var md5=tr.attr('id');
	var amt=tr.data('amt');
	os_set_amt(md5,amt-1,tr);
}
function os_remove_all(ev){
	var tr=$(ev.target).closest('tr');
	var md5=tr.attr('id');
	os_set_amt(md5,0,tr);
}
function os_set_amt(md5,amt,tr){
	$.getJSON('/ww.plugins/online-store/j/set_amt.php?md5='+md5+'&amt='+amt,function(ret){
		amt=ret.amt;
		tr.data('amt',amt);
		$('.amt-number',tr).text(amt);
		$('.item-total',tr).text('€'+ret.item_total);
		$('.total',tr.closest('table')).text('€'+ret.total);
		if(amt<1){
			tr.prev().fadeOut("normal",function(){
				$(this).remove();
			});
			tr.fadeOut("normal",function(){
				$(this).remove();
			});
		}
	});
}
function os_wheres_the_basket(from){
	var f_off=from.offset();
	var f_size=[from.width(),from.height()];
	var to=$('.online-store-basket-widget');
	var t_off=to.offset();
	var t_size=[to.width(),to.height()];
	var slider=$('<div style="position:absolute;border:1px solid white;background:#ff0;opacity:.2;left:'+f_off.left+'px;top:'+f_off.top+'px;width:'+f_size[0]+'px;height:'+f_size[1]+'px">TEST</div>').appendTo(document.body);
	slider.animate({
		left:t_off.left+'px',
		top:t_off.top+'px',
		width:t_size[0]+'px',
		height:t_size[1]+'px',
		opacity:.8
	},1000,'linear',function(){
		$(this).fadeOut('normal',function(){
			$(this).remove();
		});
	});
}
function os_reset_basket(res){
	var html='<table><tr><th>&nbsp;</th><th>Price</th><th>Amount</th><th>Total</th></tr>';
	for(var md5 in res.items){
		var item=res.items[md5];
		if(md5.length!='32' || !item.amt)continue;
		html+='<tr class="os_item_name"><td colspan="4"><a href="'+item.url+'">'+item.short_desc+'</a></td></tr><tr class="os_item_numbers" id="'+md5+'"><td>&nbsp;</td><td>€'+item.cost+'</td><td class="amt">'+item.amt+'</td><td class="item-total">'+(item.cost*item.amt)+'</td></tr>';
	}
	html+='<tr class="os_total"><th colspan="3">Total</th><td class="total">€'+res.total+'</td></tr></table><a href="/common/redirector.php?type=online-store">Proceed to Checkout</a>';
	$('.online-store-basket-widget').html(html);
	os_setup_basket_events();
}
function os_setup_basket_events(){
	$('tr.os_item_numbers .amt').each(function(){
		var $this=$(this);
		var amt=parseInt($this.text());
		$this.html('<span class="amt-number">'+amt+'</span><span class="amt-links">('
			+'<a href="javascript:;" class="amt-plus">+</a>|'
			+'<a href="javascript:;" class="amt-minus">-</a>|'
			+'<a href="javascript:;" class="amt-del">x</a>'
			+')</span>'
		);
		var tr=$this.closest('tr');
		tr.data('amt',amt);
	});
	$('.amt .amt-plus').click(os_add_one);
	$('.amt .amt-minus').click(os_subtract_one);
	$('.amt .amt-del').click(os_remove_all);
}
$(document).ready(function(){
	os_setup_basket_events()
});