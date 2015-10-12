<?php

namespace wskl_host\controls;

use wskl_host\bootstrap\Bootstrap;
use wskl_host\references;


class Base_Control {

	/**
	 * @var Bootstrap
	 */
	private $bootstrap;

	/**
	 * @var string the app name this control registered to
	 */
	private $app_name;

	/**
	 * @var string this control's slug
	 */
	private $slug;

	public function __construct( Bootstrap $bootstrap, $app_name, $slug ) {

		$this->bootstrap = $bootstrap;
		$this->app_name  = $app_name;
		$this->slug      = $slug;
	}

	public function get_bootstrap() {

		return $this->bootstrap;
	}

	public function run() {
		wp_die('ok');
	}

	protected function model( $model_slug, $app_name = '' ) {

		if( !$app_name ) {
			$app_name = $this->app_name;
		}

		$fqn = $this->compose_model_fqn( $app_name, $model_slug );
		$instance = new $fqn();

		return $instance;
	}

	protected function view( $view_slug, $app_name = '' ) {

		if( !$app_name ) {
			$app_name = $this->app_name;
		}

		$fqn = $this->compose_view_fqn( $app_name, $view_slug );
		$instance = new $fqn();

		return $instance;
	}

	private function compose_model_fqn( $app_name, $model_slug ) {

		$bootstrap = $this->get_bootstrap();
		$app_namespace = $bootstrap->get_app_namespace();

		return references\compose_fqn( $app_namespace, $app_name, 'model', $model_slug );
	}

	private function compose_view_fqn( $app_name, $view_slug ) {

		$bootstrap = $this->get_bootstrap();
		$app_namespace = $bootstrap->get_app_namespace();

		return references\compose_fqn( $app_namespace, $app_name, 'view', $view_slug );
	}
}