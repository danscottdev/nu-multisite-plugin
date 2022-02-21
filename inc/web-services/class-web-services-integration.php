<?php
/**
 * Web Services Integrations
 * Take the values stored in wpdb via the Web Services admin page
 * and actually integrate them into the network sites based on their individual configurations
 */

namespace NUEDU_Network\Inc\Web_Services;

use NUEDU_Network;
use NUEDU_Network\Inc\Web_Services\Integrations\Integration_GTM;
use NUEDU_Network\Inc\Web_Services\Integrations\Integration_Optimizely;
use NUEDU_Network\Inc\Web_Services\Integrations\Integration_BrightEdge;
use NUEDU_Network\Inc\Web_Services\Integrations\Integration_LiveChat;

/**
 * Web_Services_Integration
 *
 * For actually loading the 'enabled' services for a given site
 * --> Check if we're on the admin page and don't load if so
 * --> Check what sub-site we're currently on
 * --> Load all 'enabled' services, with the inputted configurations
 */
class Web_Services_Integration {

	/**
	 * Placeholder for all web services, defined in Web_Services class
	 *
	 * @var array
	 * */
	protected $all_services;

	/**
	 * Array of all sites in the network
	 * Initialized to empty array, populated via get_sites() during construct()
	 *
	 * @var array
	 */
	protected $all_sites = [];



	/**
	 * Constructor
	 * Fetch variables from Web_Services class so we can use them here
	 */
	public function __construct() {

		// First, check if we're currently on the network admin page.
		// If we are, we don't need to load the front-end integrations.
		// So just abort instead.
		if ( is_network_admin() || is_admin() ) {
			// write_to_log( 'admin page' );
			return;
		} else {

			$this->all_services = NUEDU_Network\Config::$global_web_services;

			add_action( 'wp_loaded', [ $this, 'init_web_services' ] );
		}
	}


	/**
	 * 1) Check what site we're currently on, so we can only load integrations for that site rather than everything for everyone
	 * 2) Loop through all web services. Check if each web service is 'enabled' for the current site
	 * 3) If it is, run service-specific integration code
	 */
	public function init_web_services() {

		$site_id = get_current_blog_id();

		// Generate option slug to check for.
		// Slug format: 'web-services-site-${SITE_ID}-service-${SERVICE_SLUG}-field-${FIELD_SLUG}'.
		foreach ( $this->all_services as $service ) {

			$service_slug = $service['slug'];
			$field_slug   = 'enabled'; // First, only check for enabled.
			$option_slug  = 'web-services-site-' . $site_id . '-service-' . $service_slug . '-field-' . $field_slug;

			if ( get_site_option( $option_slug ) ) {
				$this->create_web_integration( $service, $site_id );
			}
		}

	}

	/**
	 * Create actual front-end integration based on web service & site ID
	 *
	 * @param array $service => Web Service.
	 * @param int   $site => Site ID.
	 */
	public function create_web_integration( $service, $site ) {
		switch ( $service['slug'] ) {
			case 'google_tag_manager':
				new Integration_GTM( $site );
				break;
			case 'optimizely':
				new Integration_Optimizely( $site );
				break;
			case 'brightedge':
				new Integration_BrightEdge( $site );
				break;
			case 'livechat':
				new Integration_LiveChat( $site );
				break;
		}
	}

}