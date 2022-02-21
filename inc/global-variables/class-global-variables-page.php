<?php
/**
 *  Global Variables Settings Page
 *
 *  - Creates a WP Admin Settings Page for 'Global Variables', to store numbers that should be consistent across all network sites
 *  - Values are saved in wpdb, and then accessible anywhere in the site via get_site_option()
 *  - Variables can also be outputted via shortcodes, see class-global-shortcodes.php
 */

namespace NUEDU_Network\Inc\Global_Variables;

use NUEDU_Network;

/**
 * Extended from Settings_Page{} parent class:
 *
 * @var $settings_slug  => slug for the top-level admin menu page (since Global Variables is a submenu)
 * @method update()     => Handles form submission for the settings pages via WP Settings API
 */
class Global_Variables_Page extends \NUEDU_Network\Inc\Settings_Page {

	/**
	 * Slug name used for creating the current WP Admin settings page & options fields.
	 *
	 * @var string
	 **/
	protected $page_slug = 'global-variables';

	/**
	 * $config -> Defines our global variables & their attributes.
	 * Pulls from root-directory/config.php during construct()
	 *
	 * @var array
	 */
	protected $config;

	/** Constructor */
	public function __construct() {

		// Populate array of global variables we want to use.
		$this->config = NUEDU_Network\Config::$global_variables;

		// Add "Global Variables" submenu option and render page.
		add_submenu_page(
			$this->settings_slug . '-page',    // parent slug.
			'Global Variables',                // Page title.
			'Global Variables',                // Menu title.
			'manage_network_options',          // required user capability.
			$this->page_slug . '-page',        // Menu (sub-page) slug.
			[ $this, 'render_page_callback' ]  // callback fn().
		);

		// Setup rest of fields, settings, inputs.
		$this->configure_inputs();

		/**
		 * Note: WP process for saving *network* settings is different than the standard way of saving for single-site settings.
		 *
		 * To save WP network settings:
		 *  Can't use standard WP Settings API functionality, need separate update() function.
		 *  Form on admin page needs to have action="edit.php?action={ACTION}"
		 *  Which then triggers network_admin_edit_{ACTION} hook, which runs update() function
		 */
		add_action( 'network_admin_edit_' . $this->page_slug . '-update', [ $this, 'update_settings' ], 10, 1 );

	}



	/** Create input fields for global variables */
	public function configure_inputs() {

		add_settings_section(
			$this->page_slug . '-section',     // Slug name to identify the section.
			'Contact Info & Other Numbers',    // Title of section.
			[ $this, 'render_section' ],       // Callback fn() to render section output.
			$this->page_slug . '-page',        // Slug of page to show this section.
		);

		/**
		 *  Foreach item in our Global Variables config array:
		 *  - Register the field with WP via the Settings API
		 *  - Add it to the admin settings page
		 */
		foreach ( $this->config as $field ) {
			$field_slug = $this->page_slug . '-field-' . $field['slug'];

			register_setting(
				$this->page_slug,                       // Option group.
				$field_slug,                            // Option name.
			);

			add_settings_field(
				$field_slug,                            // Field slug/option name.
				$field['title'],                        // Title of field, outputted to screen.
				[ $this, 'render_field' ],              // callback to echo field. render_field() on parent class Settings_Page.
				$this->page_slug . '-page',             // Page slug to render on.
				$this->page_slug . '-section',          // Section slug to render on.
				[                                       // args to pass to callback fn.
					'field_slug' => $field_slug,
					'input_type' => $field['input_type'],
				]
			);
		}


	}


	/**
	 * Render admin page HTML
	 * Handled by parent class
	 * But first we need to pass the current page slug to that function
	 */
	public function render_page_callback() {
		$this->render_page( $this->page_slug );
	}

	/** Render HTML for settings sections
	 *  Only one section for now, so not really necessary, but leaving here as a placeholder in case we want it later
	 */
	public function render_section() {
		echo '<h2>Numbers that should be the same throughout all network sites. Can be outputted to the front end via shortcodes.</h2>';
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