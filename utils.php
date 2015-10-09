<?php

namespace wskl_host\utils;

/**
 * @param string     $class
 * @param string     $message
 * @param bool|FALSE $return
 *
 * @return string|void
 */
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