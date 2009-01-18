function fontsize_showControls(){
	var wrapper=$E('.fontsize_controls');
	window.basefontsize=parseInt($E('body').getStyle('font-size'));
	var smaller=new Element('a',{
		'styles':{
			'font-size':'.9em'
		},
		'href':'javascript:fontsize_smaller()'
	}).appendText('[A]');
	var larger=new Element('a',{
		'styles':{
			'font-size':'1.1em'
		},
		'href':'javascript:fontsize_larger()'
	}).appendText('[A]');
	wrapper.appendChild(smaller);
	wrapper.appendChild(larger);
}
function fontsize_larger(){
	if(currentfontsize>0)return;
	window.basefontsize*=1.25;
	currentfontsize++;
	$E('body').setStyle('font-size',window.basefontsize+'px');
}
function fontsize_smaller(){
	if(currentfontsize<0)return;
	window.basefontsize*=.8;
	currentfontsize--;
	$E('body').setStyle('font-size',window.basefontsize+'px');
}
fontsize_showControls();
var currentfontsize=0;
