//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class rollbar_hook_acp_advanced_controller_add_tabs extends _HOOK_CLASS_
{

	protected function _manageRollbar()
	{
		$form = new \IPS\Helpers\Form;
		$form->add( new \IPS\Helpers\Form\Text( 'rollbar_api_key', \IPS\Settings::i()->rollbar_api_key ) );

		if( \IPS\Settings::i()->rollbar_api_key )
		{
			\IPS\Member::loggedIn()->language()->words['rollbar_api_key_desc'] = '<a href="' . \IPS\Http\Url::internal( 'app=core&module=settings&controller=advanced&do=testRollbar') . '">Test connection</a>';
		}

		if ( $values = $form->values() )
		{
			$form->saveAsSettings();
			\IPS\Session::i()->log( 'acplog__rollbar_settings' );
			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=core&module=settings&controller=advanced&tab=rollbar' ), 'saved' );
		}

		\IPS\Output::i()->title		= \IPS\Member::loggedIn()->language()->addToStack('rollbar_settings');
		return $form;
	}

	protected function testRollbar()
	{
		\Rollbar\Rollbar::log( \Rollbar\Payload\Level::info(), 'Test info message');
		throw new \Exception('This is a Test exception - Check your Rollbar account for any new messages and errors!');
	}

}
