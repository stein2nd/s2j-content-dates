<?php
/**
 * Uninstall script for S2J Content Dates.
 *
 * @package S2J_Content_Dates
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 's2j_content_dates_types' );
delete_option( 's2j_content_dates_query_loop' );
