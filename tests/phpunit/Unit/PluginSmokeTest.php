<?php
/**
 * Minimal PHPUnit smoke tests until plugin PHP is implemented.
 *
 * @package S2J_Content_Dates
 */

use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class PluginSmokeTest extends TestCase {

	public function test_composer_json_declares_php_8_3_or_later() {
		$composer = json_decode(
			(string) file_get_contents( TESTS_PLUGIN_DIR . '/composer.json' ),
			true
		);

		$this->assertIsArray( $composer );
		$this->assertSame( '>=8.3', $composer['require']['php'] );
	}
}
