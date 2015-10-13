<?php

namespace axis2;

/**
 * @param string           $class
 * @param string           $message
 * @param bool|FALSE       $return
 *
 * @return void|string
 */
function display_admin_notice( $class, $message, $return = FALSE ) {

	$html = sprintf( '<div class="%s"><p>%s</p></div>', $class, $message );

	if( $return ) {
		return $html;
	}

	echo $html;

	/** @noinspection PhpInconsistentReturnPointsInspection */
	return;
}


function render( $template, array $context = array() ) {

	$view = Bootstrap::get_view();
	return $view->render( $template, $context );
}