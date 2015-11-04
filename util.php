<?php

namespace casper_axis2;

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

function pop_args( array &$args, $keyword, $default = NULL ) {

	if( !isset( $args[ $keyword ] ) ) {
		return $default;
	}

	$out = $args[ $keyword ];
	unset( $args[ $keyword ] );

	return $out;
}

function pop_or_throw( array &$args, $keyword ) {

	if( !isset( $args[ $keyword ] ) ) {
		throw new \LogicException( "the array requires a key '$keyword'" );
	}

	$out = $args[ $keyword ];
	unset( $args[ $keyword ] );

	return $out;
}


function render( $template, array $context = array() ) {

	$view = Bootstrap::get_view();
	return $view->render( $template, $context );
}