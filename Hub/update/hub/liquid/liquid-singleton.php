<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

abstract class Liquid_Singleton {
	private static array $instances = [];

	protected function __construct() {
	}

	private function __clone() {
	}

	/**
	 * @throws Exception
	 */
	public function __wakeup() {
		throw new Exception( 'Cannot unserialize singleton' );
	}

	public static function instance() {
		$class = static::class;

		if ( ! isset( self::$instances[ $class ] ) ) {
			self::$instances[ $class ] = new static();
			self::$instances[ $class ]->init();
		}

		return self::$instances[ $class ];
	}
}
