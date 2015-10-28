<?php

namespace axis2;


class Admin_Notice {

	private $messages = array();

	public function add_notice( $class, $message ) {

		array_push(
			$this->messages,
			array(
			'class' => $class,
			'message' => $message,
			)
		);
	}

	public function display_notices() {

		for( $i = 0; $i < count( $this->messages) ; ++$i ) {
			add_action( 'admin_notices', array( $this, 'callback_display_notice') );
		}
	}

	public function callback_display_notice() {

		$elem = array_pop( $this->messages );

		if( $elem ) {
			$html = $this->html_template( $elem['class'], $elem['message'] );
			echo $html;
		}
	}

	private function html_template( $class, $message ) {
		return sprintf('<div class="%s"><p>%s</p></div>', $class, $message);
	}
}