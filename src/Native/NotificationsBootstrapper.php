<?php namespace Cartalyst\Notifications\Native;
/**
 * Part of the Notifications package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Notifications
 * @version    0.1.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Session\Store;
use Illuminate\Filesystem\Filesystem;
use Cartalyst\Notifications\Notifier;
use Illuminate\Session\FileSessionHandler;
use Cartalyst\Notifications\Notifications;
use Cartalyst\Notifications\FlashNotifier;
use Cartalyst\Notifications\Storage\NativeSession;

class NotificationsBootstrapper {

	/**
	 * Configuration array.
	 *
	 * @var array
	 */
	protected static $config = [];

	/**
	 * Creates a sentinel instance.
	 *
	 * @return \Cartalyst\Notifications\Notifications
	 */
	public function createNotifications()
	{
		$notifications = new Notifications();

		$this->createNotifier($notifications);
		$this->createFlashNotifier($notifications);

		return $notifications;
	}

	/**
	 * Sets the configuration array.
	 *
	 * @param  array  $config
	 * @return void
	 */
	public static function setConfig(array $config)
	{
		static::$config = $config;
	}

	/**
	 * Returns the configuration array.
	 *
	 * @return array
	 */
	public static function getConfig()
	{
		return static::$config;
	}

	/**
	 * Creates and adds a new notifier.
	 *
	 * @param  \Cartalyst\Notifications\Notifications  $notifications
	 * @return void
	 */
	protected function createNotifier($notifications)
	{
		$notifier = new Notifier(static::$config);
		$notifications->addNotifier('default', $notifier);
	}

	/**
	 * Creates and adds a new flash notifier.
	 *
	 * @param  \Cartalyst\Notifications\Notifications  $notifications
	 * @return void
	 */
	protected function createFlashNotifier($notifications)
	{
		if ($session = $this->createSession())
		{
			$flashNotifier = new FlashNotifier(static::$config, $session);
			$notifications->addNotifier('flash', $flashNotifier);
		}
	}

	/**
	 * Creates a session instance.
	 *
	 * @return \Cartalyst\Notifications\Storage\StorageInterface|null
	 */
	protected function createSession()
	{
		if (class_exists('Illuminate\Filesystem\Filesystem') && class_exists('Illuminate\Session\FileSessionHandler'))
		{
			$fileSessionHandler = new FileSessionHandler(new Filesystem(), __DIR__.'/../../../../../storage/sessions');

			$store = new Store('foo', $fileSessionHandler);

			return new NativeSession($store);
		}
	}

}