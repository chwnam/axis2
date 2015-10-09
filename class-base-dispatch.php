<?php

namespace wskl_host\libs;


abstract class Base_Dispatch {

	private $bootstrap;

	abstract public function init_filter();

	public function set_bootstrap( $bootstrap ) {
		$this->bootstrap = $bootstrap;
	}

	public function get_bootstrap() {
		return $this->bootstrap;
	}

	public function control( $app_name, $control_slug ) {


	}
}