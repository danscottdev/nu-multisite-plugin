<?php
/**
 *  Custom Settings Menu Page
 *  - Adds custom menu page to Multisite Admin menu
 *  - Creates submenu pages for Global Variables and Web Services
 *
 *  This class gets extended by Global_Variables{} and Web_Services{},
 *  both of which use render_field() and update() functions defined here
 *
 *  References
 * - https://developer.wordpress.org/reference/functions/add_menu_page/
 * - https://marioyepes.com/create-a-wordpress-multisite-settings-page/
 */

namespace NUEDU_Network\Inc;

use NUEDU_Network\Inc\Web_Services\Web_Services_Page;
use NUEDU_Network\Inc\Global_Variables\Global_Variables_page;

/**
 * Settings_Page
 */
class Settings_Page {

	/**
	 * Slug used for custom menu page setup functions + WP settings API
	 * Can be anything, just set it here so it's easy to change if needed
	 *
	 * @var string
	 * */
	protected $settings_slug = 'custom-network-settings';

	/** Constructor */
	public function __construct() {
		add_action( 'network_admin_menu', [ $this, 'create_menu_pages' ], 10 );
		add_action( 'admin_notices', [ $this, 'show_admin_notices' ] );
	}

	/** Build out custom menu, menu page, and settings fields */
	public function create_menu_pages() {

		// Add parent-level settings page to admin menu.
		add_menu_page(
			'NU Network Global Settings',    // Page title, outputted to screen.
			'Global Settings',               // Display name of menu option.
			'manage_network_options',        // Required user capability to access menu.
			$this->settings_slug . '-page',  // Page slug -> settings sections & fields need to reference this.
			[ $this, 'create_parent_page' ], // Callback fn to render actual admin page html.
			'dashicons-admin-site'           // Menu icon (globe).
		);

		/** Create Global Variables submenu page */
		new Global_Variables_Page();

		/** Create Web Services submenu page */
		new Web_Services_Page();

	}

	/**
	 * Output HTML for settings page
	 * This is the top-level menu page for the plugin.
	 * Currently just placeholder text because all the heavy lifting occurs on sub-pages
	 **/
	public function create_parent_page() {
		echo '<h1>National University -- Global Network Settings</h1>';
		echo '<p>Select a sub-menu page from the admin menu.</p>';

		ob_start();
		include plugin_dir_path( __DIR__ ) . '/views/shortcodes.php';
		echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput
	}


	/**
	 * Render admin page HTML
	 * Break out HTML templates into separate files
	 * This is because for some settings pages (web services) we want to override the default WP admin HTML, for better UI
	 *
	 * @param string $page_slug - Slug for the menu page we're generating. Needs to have a corresponding file in /views/ folder.
	 */
	public function render_page( $page_slug ) {
		$template_path = plugin_dir_path( __DIR__ ) . '/views/' . $page_slug . '.php';

		if ( ! file_exists( $template_path ) ) {
			return;
		}

		ob_start();
		include $template_path;
		echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput
	}


	/**
	 * Output HTML for input forms
	 * Gets called by Global_Variables{} and Web_Services{}, and any other future pages we want to add
	 *
	 * @param array $args => passed in via add_settings_section().
	 * Consists of field_slug and input_type from config array for given web service.
	 */
	public function render_field( $args ) {

		$input_type = esc_attr( $args['input_type'] );
		$val        = get_site_option( $args['field_slug'], '' );
		$output     = '';

		switch ( $input_type ) {

			case 'checkbox':
				// Special logic for checkbox inputs, because they don't have a "value" attribute.
				if ( ! isset( $val ) ) {
					$val = 0;
				}

				$output = '
					<input
					type="checkbox"
					name="' . esc_attr( $args['field_slug'] ) . '"
					value="1"' . checked( 1, $val, false ) . '/>';
				break;

			case 'dropdown':
				$output = '<select
							name="' . esc_attr( $args['field_slug'] ) . '"
							id="' . esc_attr( $args['field_slug'] ) . '"
							/>';

				$output .= '<option value="null">&nbsp;</option>';

				foreach ( $args['input_values'] as $dropdown_option ) {
					$output .= '<option value="' . esc_attr( $dropdown_option ) . '"' . selected( $val, $dropdown_option, false ) . '>' . esc_html( $dropdown_option ) . '</option>';
				}

				$output .= '</select>';
				break;

			case 'textarea':
				$output  = '<textarea
							name="' . esc_attr( $args['field_slug'] ) . '"
							rows="3"
							cols="35">';
				$output .= esc_attr( $val );
				$output .= '</textarea>';
				break;


			default:
				$output = '<input
							type="' . esc_attr( $args['input_type'] ) . '"
							name="' . esc_attr( $args['field_slug'] ) . '"
							value="' . esc_attr( $val ) . '"
							/>';
				break;
		}

		echo $output; //phpcs:ignore -- all dynamically-generated output components are already escaped prior to this line.

	}

	/**
	 * Update Functionality
	 *
	 * For multisite setup, we need a separate function for updating values, can't use standard WP Settings API
	 *      -> reference: https://marioyepes.com/create-a-wordpress-multisite-settings-page/
	 * Also note that we need to use update_site_option() as opposed to update_option(),
	 * And that values are stored in 'site_meta' wp db table, rather than 'wp_options' table
	 *
	 * @param string $page_slug - page slug that's calling this function.
	 */
	public function update( $page_slug ) {

		// Ensure fn is being called only from our custom admin page.
		// '-options' postfix to match nonce created by WP's settings_fields(), called when rendering fields to page.
		check_admin_referer( $page_slug . '-page-options' );

		global $new_whitelist_options; // WP global containing all registered settings.
		$options = $new_whitelist_options [ $page_slug ];

		/**
		 * Loop through all input fields on our custom admin page,
		 * and save/remove from wp database accordingly
		 */
		foreach ( $options as $option ) {
			if ( isset( $_POST[ $option ] ) ) {
				update_site_option( $option, sanitize_text_field( $_POST[ $option ] ) );
			} else {
				delete_site_option( $option );
			}
		}

		/**
		 * After updating options, reload current page but with "updated" URL parameter,
		 * in order to trigger "success" admin dialog box.
		 */
		wp_safe_redirect(
			add_query_arg(
				[
					'page'    => $page_slug . '-page',
					'updated' => true,
				],
				network_admin_url( 'admin.php' )
			)
		);
		exit; // Fn wp_safe_redirect() needs to exit manually.
	}
}
