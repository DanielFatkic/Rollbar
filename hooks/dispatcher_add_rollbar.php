//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

abstract class rollbar_hook_dispatcher_add_rollbar extends _HOOK_CLASS_
{


	public function init()
	{
		$this->initRollbar();
		parent::init();
	}


	protected function initRollbar()
	{
		if ( \IPS\Settings::i()->rollbar_api_key )
		{
			require_once \IPS\Application::getRootPath('rollbar')  . '/applications/rollbar/sources/vendor/autoload.php';
			$config = array(
				'access_token' => \IPS\Settings::i()->rollbar_api_key,
				'environment' => \IPS\Settings::i()->base_url,
				'root' => \IPS\ROOT_PATH
			);
			\Rollbar\Rollbar::init($config);
		}
	}

}
