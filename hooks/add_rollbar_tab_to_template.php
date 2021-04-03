//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class rollbar_hook_add_rollbar_tab_to_template extends _HOOK_CLASS_
{

/* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return parent::hookData();
}
/* End Hook Data */



	public function tabs($tabNames, $activeId, $defaultContent, $url, $tabParam='tab', $tabClasses='', $panelClasses='')
	{
		if( \IPS\Dispatcher::i()->module->key === 'settings' and \IPS\Dispatcher::i()->controller === 'advanced')
		{
			$tabNames['rollbar'] = 'rollbar_settings';
		}
		return parent::tabs($tabNames, $activeId, $defaultContent, $url, $tabParam, $tabClasses, $panelClasses);
	}
}
