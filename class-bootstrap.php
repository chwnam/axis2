<?php

namespace axis2;

require_once( 'class-autoload.php' );
require_once( 'class-base-control.php' );
require_once( 'class-base-dispatch.php' );
require_once( 'class-base-view.php' );
require_once( 'reference.php' );
require_once( 'util.php' );


class Bootstrap {

	/**
	 * @var string
	 */
	private $plugin_main_file;

	/**
	 * @var string
	 */
	private $plugin_dir;

	/** @var string apps' path */
	private $app_path;

	/**
	 * @var string apps namespace
	 */
	private $app_namespace;

	private $dispatch_catalog;      // 필터 정보. 각 개체마다 stdClass. app_name, filter_slug, path 요소를 가지고 있음.
	private $dispatches;            // 인스턴스화 된 filter object array

	/**
	 * @var \axis2\Autoload
	 */
	private $auto_loader;

	static private $app_root_name    = 'app';
	static private $dispatch_postfix = 'dispatch';

	/**
	 * @var \axis2\Base_view
	 */
	static private $view             = NULL;

	public function get_app_namespace() {

		return $this->app_namespace;
	}

	public function get_app_path() {

		return $this->app_path;
	}

	public function get_plugin_dir() {

		return $this->plugin_dir;
	}

	/**
	 * @return Base_view
	 */
	public static function get_view() {
		return static::$view;
	}

	public function startup( $plugin_main_file, $app_namespace, array $allowed_app_names ) {

		$this->plugin_main_file = $plugin_main_file;
		$this->app_namespace    = $app_namespace;
		$this->plugin_dir       = dirname( $this->plugin_main_file );
		$app_root_name          = static::$app_root_name;
		$this->app_path         = "{$this->plugin_dir}/{$app_root_name}";

		$this->auto_loader = new AutoLoad( $this->app_path, $this->app_namespace );
		$this->auto_loader->register_autoload();

		$this->dispatch_catalog = static::lookup_dispatch( $this->app_path, $allowed_app_names );
		$this->dispatches       = $this->init_dispatches( $this->dispatch_catalog );

		if( !static::$view ) {
			static::$view = new Base_View( $this );
		}
	}

	/**
	 * 각 앱 디렉토리 내부의 dispatches 디렉토리 내부의 filter class 파일을 찾아 목록을 만듭니다.
	 *
	 * @param $app_dir           string 앱 디렉토리
	 * @param $allowed_app_names array  플러그인에서 사용하는 앱 이름들
	 *
	 * @return array 각 요소는 stdClass 이며 세 개의 속성을 가집니다.
	 *               - app_name    string 앱의 이름
	 *               - slug        string 필터의 슬러그. 이걸로 클래스 이름을 만듭니다.
	 *               - path        string 파일의 경로.
	 */
	private static function lookup_dispatch( $app_dir, array $allowed_app_names ) {

		$output  = array();
		$postfix = static::$dispatch_postfix;

		if ( ! is_dir( $app_dir ) ) {
			return array();
		}

		$app_dir = untrailingslashit( $app_dir );

		foreach ( $allowed_app_names as $app_name ) {

			$dispatch_dir = "{$app_dir}/{$app_name}/{$postfix}";
			if ( ! is_dir( $dispatch_dir ) ) {
				continue;
			}

			$dispatch_files = array_diff( scandir( $dispatch_dir ), array( '.', '..', ) );
			$matches        = array();
			foreach ( $dispatch_files as $dispatch_file ) {

				if ( preg_match( "/^(class-)?(.+)-{$postfix}\\.php$/", $dispatch_file, $matches ) ) {
					$output[ $matches[2] ] = (object) array(
						'app_name' => $app_name,
						'slug'     => $matches[2],
						'path'     => $dispatch_dir . "/$dispatch_file"
					);
				}
			}
		}

		return $output;
	}

	/**
	 * filter 목록을 받아 include 시키고 instance 화 시킵니다.
	 *
	 * @param array $dispatch_catalog 디스패치 카탈로그입니다.
	 *
	 * @see \axis2\Bootstrap::lookup_dispatch()
	 *
	 * @return array 필터 인스턴스입니다. 각 내용은 슬러그로 색인되어 있습니다.
	 */
	private function init_dispatches( array &$dispatch_catalog ) {

		$filters          = array();
		$postfix          = static::$dispatch_postfix;
		$class_postfix    = ucfirst( $postfix );

		foreach ( $dispatch_catalog as $item ) {

			$app_name = str_replace( '-', '_', $item->app_name );
			$path     = $item->path;

			$class_prefix = array();
			foreach ( explode( '_', str_replace( '-', '_', $item->slug ) ) as $w ) {
				$class_prefix[] = ucfirst( $w );
			}
			$class_prefix = implode( '_', $class_prefix );

			$namespace = "{$this->app_namespace}\\{$app_name}\\{$postfix}";
			$fqn       = "{$namespace}\\{$class_prefix}_{$class_postfix}";

			/** @noinspection PhpIncludeInspection */
			require_once( $path );

			/** @var \axis2\Base_Dispatch $instance */
			$instance               = new $fqn( $this, $app_name );
			$filters[ $item->slug ] = $instance;
			$instance->init_dispatch();
		}

		return $filters;
	}
}