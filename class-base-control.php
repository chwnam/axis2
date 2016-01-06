<?php

namespace axis2;


class Base_Control {

	/**
	 * @var \axis2\Bootstrap
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

	public function __construct( array $args = array() ) {

		$this->bootstrap = pop_or_throw( $args, 'bootstrap' );
		$this->app_name  = pop_or_throw( $args, 'app-name' );
		$this->slug      = pop_or_throw( $args, 'slug' );
	}

	public function get_bootstrap() {

		return $this->bootstrap;
	}

	protected function model( $model_slug, $app_name = '' ) {

		$fqn = $this->model_class( $app_name, $model_slug );
		$instance = new $fqn();

		return $instance;
	}

	protected function model_class( $model_slug, $app_name = '' ) {

		if( !$app_name ) {
			$app_name = $this->app_name;
		}

		return $this->compose_model_fqn( $app_name, $model_slug );
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

		return compose_fqn( $app_namespace, $app_name, 'model', $model_slug );
	}

	private function compose_view_fqn( $app_name, $view_slug ) {

		$bootstrap = $this->get_bootstrap();
		$app_namespace = $bootstrap->get_app_namespace();

		return compose_fqn( $app_namespace, $app_name, 'view', $view_slug );
	}
}