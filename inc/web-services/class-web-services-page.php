<?php
/**
 *  Web Services Integration
 *  Allows configuration for various network-wide services
 */

namespace NUEDU_Network\Inc\Web_Services;

use NUEDU_Network;

/**
 * Web Services
 * Inherited from Settings_Page >>
 *   - var $settings_slug - string
 */
class Web_Services_Page extends \NUEDU_Network\Inc\Settings_Page {

	/**
	 * Slug name for the current page.
	 *
	 * @var string
	 * */
	protected $page_slug = 'web-services';

	/**
	 * $config -> Defines our global variables & their attributes.
	 * Pulls from root-directory/config.php during construct()
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * Array of all sites in the network
	 * Initialized to empty array, populated via get_sites() during construct()
	 *
	 * @var array
	 */
	protected $network_sites = [];

	/** Constructor **/
	public function __construct() {

		$this->network_sites = get_sites(); // Returns array of all WP_SITE objects.

		// Populate array of global variables we want to use.
		$this->config = NUEDU_Network\Config::$global_web_services;

		// Add "Web Services" menu option and render page.
		add_submenu_page(
			$this->settings_slug . '-page',    // parent top-level settings page slug.
			'Web Services',                    // Page title.
			'Web Services',                    // Menu title.
			'manage_network_options',          // required user capability.
			$this->page_slug . '-page',        // Slug for THIS PAGE.
			[ $this, 'render_page_callback' ]  // callback fn() to render admin page output.
		);

		/**
		 * To save WP network settings:
		 *  Can't use standard WP Settings API functionality, need separate update() function
		 *  Form on admin page needs to have action="edit.php?action={ACTION}"
		 *  Which then triggers network_admin_edit_{ACTION} hook, which runs update() function
		 */
		add_action( 'network_admin_edit_' . $this->page_slug . '-update', [ $this, 'update_settings' ] );

		// Setup rest of fields, settings, inputs.
		$this->configure_inputs();
	}

	/**
	 *  Create input fields for global variables
	 */
	public function configure_inputs() {

		// First, loop through all Web Services from the config array at the top of this file.
		foreach ( $this->config as $service ) {

			/**
			 * This is essentially an "empty" settings section being created for each top-level web service.
			 * The actual setting sections that we use are created later, for each [web service + network site] combo.
			 * Doing this to make the admin UI easier and better looking, based on how we generate the tables in render_page().
			 *
			 * @todo there's *probably* a better way to accomplish this.
			 */
			add_settings_section(
				$this->page_slug . '-section-' . $service['slug'],
				'Web Service: ' . $service['title'],
				null,
				$this->page_slug . '-page'
			);

			// Second, loop through network sites.
			foreach ( $this->network_sites as $site ) {
				$section_id = $this->page_slug . '-section-' . $service['slug'] . '-' . $site->domain;

				// Register settings section with WordPress.
				// Each ROW on the admin page settings table is it's own section group.
				add_settings_section(
					$section_id,                // Slug ID for this section.
					$site->domain,              // Title for this section.
					null,                       // Callback fn.
					$this->page_slug . '-page'  // Slug ID of settings page we want to show this on.
				);

				// Third, loop through all input fields defined in the config array for the given web service.
				foreach ( $service['fields'] as $field ) {
					$field_slug = $this->page_slug . '-site-' . $site->blog_id . '-service-' . $service['slug'] . '-field-' . $field['slug'];

					$field_values = ( isset( $field['values'] ) ) ? $field['values'] : null;
					// For each [web-service]->[network-site]->[field], register & create settings field in WP.
					register_setting(
						$this->page_slug,   // Option group.
						$field_slug,        // Option name.
					);

					/**
					 * Normally we would pass render_field() as the callback, to output the actual field HTML
					 * However since we're manually creating the admin page layout, we omit that here and call it as a part of render_page() instead.
					 */
					add_settings_field(
						$field_slug,                            // Field slug/option name.
						$field['title'],                        // Title of field, outputted to screen.
						null,                                   // callback to echo field.
						$this->page_slug . '-page',             // Page slug to render on.
						$section_id,                            // Section slug to render on.
						[                                       // args to pass to callback fn.
							'field_slug'   => $field_slug,
							'input_type'   => $field['type'],
							'input_values' => $field_values,
						]
					);
				}
			}
		}
	}

	/**
	 * Render admin page HTML
	 * Break out HTML template into separate file
	 * But first we need to pass the current page slug to that function
	 */
	public function render_page_callback() {
		$this->render_page( $this->page_slug );
	}

	/**
	 * Update Functionality
	 * Saving values to wpdb occurs via update() in parent class
	 * But first we need to pass the current page slug to that function
	 */
	public function update_settings() {
		$this->update( $this->page_slug );
	}

}