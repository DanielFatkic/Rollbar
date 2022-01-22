<?php
/**
 * @brief		Community Enhancements
 * @author		<a href='https://www.invisioncommunity.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) Invision Power Services, Inc.
 * @license		https://www.invisioncommunity.com/legal/standards/
 * @package		Invision Community
 * @subpackage	Rollbar Error Handler
 * @since		22 Jan 2022
 */

namespace IPS\rollbar\extensions\core\CommunityEnhancements;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Community Enhancement
 */
class _Rollbar
{
	/**
	 * @brief	Enhancement is enabled?
	 */
	public $enabled	= FALSE;

	/**
	 * @brief	IPS-provided enhancement?
	 */
	public $ips	= FALSE;

	/**
	 * @brief	Enhancement has configuration options?
	 */
	public $hasOptions	= TRUE;

	/**
	 * @brief	Icon data
	 */
	public $icon	= "";
	
	/**
	 * Constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$this->enabled = (bool) \IPS\Settings::i()->rollbar_api_key != 0;
		ray($this);
	}
	
	/**
	 * Edit
	 *
	 * @return	void
	 */
	public function edit()
	{
		$form = new \IPS\Helpers\Form;
		$form->add( new \IPS\Helpers\Form\Text( 'rollbar_api_key', \IPS\Settings::i()->rollbar_api_key ) );
		if ( $values = $form->values() )
		{
			try
			{
				$this->testSettings($values);
				$form->saveAsSettings($values);

				\IPS\Output::i()->inlineMessage	= \IPS\Member::loggedIn()->language()->addToStack('saved');
			}
			catch ( \LogicException $e )
			{
				$form->error = $e->getMessage();
			}
		}
		
		\IPS\Output::i()->sidebar['actions'] = array(
			'help'	=> array(
				'title'		=> 'Rollbar',
				'icon'		=> 'question-circle',
				'link'		=> \IPS\Http\Url::internal( "http://www.rollbar.com" ),
				'target'	=> '_blank'
			),
		);
		
		\IPS\Output::i()->output = \IPS\Theme::i()->getTemplate( 'global' )->block( 'enhancements__rollbar_Rollbar', $form );
	}
	
	/**
	 * Enable/Disable
	 *
	 * @param	$enabled	bool	Enable/Disable
	 * @return	void
	 * @throws	\LogicException
	 */
	public function toggle( $enabled )
	{

		if ( $enabled )
		{
			if ( !\IPS\Settings::i()->rollbar_api_key )
			{
				throw new \DomainException;
			}
		}
		else
		{
			\IPS\Settings::i()->changeValues( array( 'rollbar_api_key' => 0 ) );
		}
	}
	
	/**
	 * Test Settings
	 *
	 * @return	void
	 * @throws	\LogicException
	 */
	protected function testSettings( array $values )
	{
		/* If we enable SendGrid but do not supply an API key, this is a problem */
		if( !$values['rollbar_api_key'] )
		{
			throw new \InvalidArgumentException( "rollbar_api_key_required" );
		}

		require_once \IPS\Application::getRootPath('rollbar')  . '/applications/rollbar/sources/vendor/autoload.php';
		$config = array(
			'access_token' => $values['rollbar_api_key'],
			'environment' => \IPS\Settings::i()->base_url,
			'root' => \IPS\Application::getRootPath('core')
		);
		\Rollbar\Rollbar::init($config);
		$r = \Rollbar\Rollbar::log( \Rollbar\Payload\Level::info(), 'Test info message');
		if( $r->getStatus() != 200 )
		{
			throw new \LogicException($r->getInfo()['message']);
		}
	}
}