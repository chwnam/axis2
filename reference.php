<?php
namespace wskl_host\references;

function slug_to_class_name( $slug, $postfix ) {

	$output = '';
	foreach ( explode( '_', str_replace( '-', '_', strtolower( $slug ) ) ) as $tok ) {
		$output .= ucfirst( $tok ) . '_';
	}
	$output .= ucfirst( $postfix );

	return $output;
}

function compose_fqn( $app_namespace, $app_name, $component, $slug ) {

	$app_name      = str_replace( '-', '_', strtolower( $app_name ) );
	$class_name    = slug_to_class_name( $slug, $component );

	return "{$app_namespace}\\$app_name\\{$component}\\{$class_name}";
}