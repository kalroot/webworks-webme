$.browser.msie6 = document.all && !window.XMLHttpRequest;
function getWindowSize(){
	var a=document.documentElement,b=document.body;
	if(a&&(a.clientWidth||a.clientHeight))return {x:a.clientWidth,y:a.clientHeight};
	return {x:b.clientWidth,y:b.clientHeight};
}
window.getSize=function(){
	return {size:getWindowSize()};
}