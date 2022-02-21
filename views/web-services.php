<?php
	/**
	 * Render admin page HTML
	 * Custom coded for better admin page UI/layout, we're bypassing WP's standard settings API render fn.
	 * The code below is based off of WordPress's do_settings_sections(), but modified a for cleaner & more intuitive layout
	 * reference https://developer.wordpress.org/reference/functions/do_settings_sections/
	 * reference https://developer.wordpress.org/reference/functions/do_settings_fields/
	 */

	// Styling specific to this admin page.
	wp_enqueue_style( 'custom_web_services_admin_css', plugins_url( 'admin-styles.css', __FILE__ ), [], '1.0' );

	$slug = $this->page_slug;
?>


<div id="nu-web-services__admin" class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<p>Global Web Service Integrations: enable/disable & configure across all network sites!</p>

	<?php if ( isset( $_GET['updated'] ) ) : ?>
		<div id="message" class="updated notice is-dismissible">
			<p>Options Saved</p>
		</div>
	<?php endif; ?>

	<form action="edit.php?action=<?php echo esc_attr( $slug ); ?>-update" method="POST">
		<?php
		global $wp_settings_fields;

		// WP generates hidden input fields for form.
		settings_fields( $slug . '-page' );

		/**
		 * For each Web Service, we output an HTML table with input fields
		 * Column headers: Web Service name and input names
		 * Column rows: network sites
		 *
		 * Since this template is being called from Web_Services{}, we have access to $global_web_services defined there
		 */

		foreach ( NUEDU_Network\Config::$global_web_services as $web_service ) { // phpcs:ignore -- see above comment
			?>

			<table style="margin-top: 3rem;">
				<tr>
					<th style="width:250px;">
						<h2 style="background-color:#000; color:#fff; padding:10px 0; margin: 0 auto; margin-right: 2em;">
							<?php echo wp_kses_post( $web_service['title'] ); ?>
						</h2>
					</th>

					<?php foreach ( $web_service['fields'] as $field ) { ?>
						<th>
							<?php if ( isset( $field['description'] ) ) { ?>
								<span data-tooltip="<?php echo esc_html( $field['description'] ); ?>">
									<?php echo wp_kses_post( $field['title'] ); ?>
								</span>
							<?php } else { ?>
								<?php echo wp_kses_post( $field['title'] ); ?>
							<?php } ?>
						</th>
					<?php } ?>
				</tr>

				<?php foreach ( $this->network_sites as $site ) { ?>
					<tr>
						<td><?php echo wp_kses_post( $site->domain ); ?></td>

						<?php foreach ( $web_service['fields'] as $input ) { ?>
							<td style="text-align: center;">
								<?php
									$section_slug = $slug . '-section-' . $web_service['slug'] . '-' . $site->domain;
									$section      = $wp_settings_fields[ $slug . '-page' ][ $section_slug ];
									$field_slug   = $slug . '-site-' . $site->blog_id . '-service-' . $web_service['slug'] . '-field-' . $input['slug'];
									$field        = $section[ $field_slug ];

									// render_field() from parent class Settings_Page.
									$this->render_field( $field['args'] );
								?>
							</td>
						<?php } ?>

					</tr>
				<?php } ?>

			</table>
			<?php
		}
		?>

	<?php submit_button(); ?>

	</form>
</div> <!-- .wrap -->

<script>
	// Hide unnecessary input rows if a given service/site combo isn't enabled, to cut down on page clutter

	jQuery(document).ready(function( $ ) {

		// On page load, hide input fields for any row where "enabled" isn't checked
		const checkboxes = document.querySelectorAll('input[type="checkbox"]');
		checkboxes.forEach((checkbox) => {
			if(!checkbox.checked){
				// If unchecked, hide all <td> elements after the current one.
				$(checkbox).parent().nextAll().css("display", "none");
			}
		})

		// Add click listener to toggle display property whenever an 'enabled' checkbox is changed
		const adminPage = document.getElementById('nu-web-services__admin');

		adminPage.addEventListener('click', function(e){
			if(e.target.type === 'checkbox'){
				e.target.checked
					? $(e.target).parent().nextAll().css("display", "table-cell")
					: $(e.target).parent().nextAll().css("display", "none");
			}
		})
	});
</script>