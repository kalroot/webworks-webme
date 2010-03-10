<?php
$plugin=array(
	'name'=>'Panels',
	'description'=>'Allows content sections to be displayed throughout the site.',
	'admin'=>array(
		'menu'=>array(
			'top'=>'Misc'
		)
	),
	'frontend'=>array(
		'template_functions'=>array(
			'PANEL'=>array(
				'function' => 'showPanel'
			)
		)
	),
	'version'=>4
);
function showPanel($vars){
	global $PLUGINS;
	$name=isset($vars['name'])?$vars['name']:'';
	$p=dbRow('select visibility,disabled,body from panels where name="'.addslashes($name).'" limit 1');
	if(!$p){
		dbQuery("insert into panels (name,body) values('".addslashes($name)."','{\"widgets\":[]}')");
		return '';
	}
	if($p['disabled'])return '';
	if($p['visibility'] && $p['visibility']!='[]'){
		$visibility=json_decode($p['visibility']);
		if(!in_array($GLOBALS['PAGEDATA']->id,$visibility))return '';
	}
	$widgets=json_decode($p['body']);
	$h='';
	foreach($widgets->widgets as $widget){
		if(isset($widget->disabled) && $widget->disabled)continue;
		if(isset($widget->visibility) && count($widget->visibility)){
			if(!in_array($GLOBALS['PAGEDATA']->id,$widget->visibility))continue;
		}
		if(isset($widget->header_visibility) && $widget->header_visibility)$h.='<h4 class="panel-widget-header">'.htmlspecialchars($widget->name).'</h4>';
		if(isset($PLUGINS[$widget->type])){
			if(isset($PLUGINS[$widget->type]['frontend']['widget'])){
				$h.=$PLUGINS[$widget->type]['frontend']['widget']($widget);
			}
			else $h.='<em>plugin "'.htmlspecialchars($widget->type).'" does not have a widget interface.</em>';
		}
		else $h.='<em>missing plugin "'.htmlspecialchars($widget->type).'".</em>';
	}
	return $h;
}