<?php
/**
 * Add custom LiveChat metabox to edit posts pages
 * Allows us to toggle livechat on/off on a page-by-page basis.
 */

namespace NUEDU_Network\Inc\Web_Services\Metadata;

/**
 * Metadata
 */
class Metadata_LiveChat {
	/**
	 * Using construct method to add any actions and filters
	 */
	public function __construct() {

		// If we're not on a single-site admin page, don't bother.
		if ( ! is_admin() || is_network_admin() ) {
			// write_to_log( 'LiveChat Meta: abort' );
			return;
		}

		// Check if LiveChat service is enabled for this site, otherwise don't bother.
		$current_site = get_current_blog_id();
		$option       = 'web-services-site-' . $current_site . '-service-livechat-field-enabled';
		$is_enabled   = get_site_option( $option );

		if ( $is_enabled ) {
			// write_to_log( 'LiveChat Register Metabox' );
			add_action( 'fm_post_page', [ $this, 'register_metabox' ] );
		} else {
			return;
		}
	}

	/**
	 * Register custom metabox
	 */
	public function register_metabox() {
		$fm = new \Fieldmanager_Checkbox( [
			'name'        => '_page_livechat',
			'label'       => 'Turn off livechat?',
			'description' => 'Whether or not to display live chat. (checkbox means off)',
		] );

		$fm->add_meta_box( 'Livechat', 'page', 'side', 'low' );
	}
}