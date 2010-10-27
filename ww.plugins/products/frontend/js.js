$(function(){
	$('a.products-lightbox').lightBox();
	$('div.product-images img').click(function(){
		var src=$('a.products-lightbox img').attr('src')
			,id=this.src.replace(/.*kfmget\/([0-9]*)[^0-9].*/,'$1')
			,$wrapper=$(this).closest('.products-product');
		$wrapper
			.find('a.products-lightbox')
			.attr('href','/kfmget/'+id)
			.find('img')
			.attr('src',src.replace(/kfmget\/([0-9]*)/,'kfmget/'+id));
	});
	var cache={},lastXhr;
	$('input[name=products-search]')
		.autocomplete({
			source: function(request, response){
				var term = request.term;
				if ( term in cache ) {
					response( cache[ term ] );
					return;
				}
				lastXhr = $.getJSON( 
					"/ww.plugins/products/frontend/search.php", 
					request, 
					function( data, status, xhr ) {
						cache[ term ] = data;
						if ( xhr === lastXhr ) {
							response( data );
						}
					}
				);
			}
		})
		.focus(function(){
			this.value='';
		})
		.change(function(){
			var $this=$(this)
				,$form=$this.closest('form');
			if(!$form.length){
				$form=$this.wrap('<form style="display:inline" action="'+
					(document.location.toString())+'" />');
			}
			setTimeout(function(){
				$this.closest('form').submit();
			},500);
		});
});
