<?php
/**
 * PHPUnit bootstrap for pure unit tests (no WordPress test suite / DB).
 *
 * @package S2J_Content_Dates
 */

define( 'TESTS_PLUGIN_DIR', dirname( __DIR__ ) );

$polyfills = TESTS_PLUGIN_DIR . '/vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php';
if ( file_exists( $polyfills ) ) {
	require_once $polyfills;
}
