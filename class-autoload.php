<?php

namespace axis2;

/**
 * Class Autoload
 *
 * @package casper_axis2
 */
class Autoload {

	/**
	 * @var string full path of the 'apps' directory
	 */
	private $app_root;

	/**
	 * @var string namespace of apps directory
	 */
	private $base_namespace;

	public function __construct( $plugin_app_dir, $plugin_base_namespace ) {

		$this->app_root           = $plugin_app_dir;
		$this->base_namespace     = $plugin_base_namespace;
	}

	public function register_autoload() {

		spl_autoload_register( array( &$this, 'autoload' ) );
	}

	/**
	 * @param $class string callback from spl_autoload_register()
	 */
	public function autoload( $class ) {

		$class_path = $this->fqn_to_path( $class );

		if ( $class_path !== FALSE ) {
			/** @noinspection PhpIncludeInspection */
			require_once( $class_path );
		}
	}

	/**
	 * Converts $class to physical file path.
	 * if $class is not ours, then returns FALSE.
	 *
	 * @param $class string requested include (or require) file. Fully qualified name.
	 *
	 * @return bool|string
	 */
	private function fqn_to_path( &$class ) {

		$base_pos = strpos( $class, $this->base_namespace );
		if ( $base_pos !== 0 ) {
			return FALSE;
		}

		$bns_len    = strlen( $this->base_namespace );
		$stripped   = substr( $class, $bns_len + 1 );
		$components = explode( '\\', $stripped );

		if ( count( $components ) != 3 ) {
			return FALSE;
		}

		$app_name   = &$components[0];
		$part       = &$components[1];
		$class_name = &$components[2];

		$app_path   = $this->app_name_to_app_dir( $app_name );
		$class_file = $this->class_name_to_class_file( $class_name );

		$file_name = "{$this->app_root}/{$app_path}/{$part}/{$class_file}";

		return $file_name;
	}

	/**
	 * @param $app_name string app part from fully qualified name
	 *
	 * @return mixed
	 */
	private function app_name_to_app_dir( &$app_name ) {

		return str_replace( '_', '-', strtolower( $app_name ) );
	}

	/**
	 * @param $class_name string class name part from fully qualified name
	 *
	 * @return string
	 */
	private function class_name_to_class_file( &$class_name ) {

		$output = 'class';
		foreach ( explode( '_', str_replace( '_', '_', strtolower( $class_name ) ) ) as $tok ) {
			$output .= "-{$tok}";
		}
		$output .= '.php';

		return $output;
	}
}