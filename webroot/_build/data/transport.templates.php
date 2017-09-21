<?php

	$templates = array();
	
	foreach (glob($sources['templates'].'/*.tpl') as $key => $value) {
		$name = str_replace('.template.tpl', '', substr($value, strrpos($value, '/') + 1, strlen($value)));

        $templates[$name] = $modx->newObject('modTemplate');
        $templates[$name]->fromArray(array(
			'id' 			=> 1,
			'templatename'	=> ucfirst($name),
			'description'	=> PKG_NAME.' '.PKG_VERSION.'-'.PKG_RELEASE.' template for MODx Revolution',
			'static'        => '1',
			'static_file'   => '/components/'.PKG_NAME_LOWER.'/elements/templates/'.$name.'.template.tpl',
            'source'        => '4',
			'icon'          => 'icon-play-circle',
			'content'		=> getSnippetContent($value)
		));
	}
	
	return $templates;

?>