<?php
/**
 * Google Tag manager
 */

namespace NUEDU_Network\Inc\Web_Services\Integrations;

/**
 * Integration
 */
class Integration_GTM {

	// Possible hooks: 'wp_head', 'wp_head_begins'.

	/**
	 * Network Site ID that we're loading this integration for.
	 * We pull from $site which gets passed in during __construct
	 *
	 * @var int
	 */
	private $site_id;

	/**
	 * Slug of the field (WP site option) we need to pull
	 *
	 * @var string
	 */
	private $base_option_slug;

	/**
	 * Google Tag manager ID >> "Container ID" populated via WP Admin settings field
	 *
	 * @var string
	 */
	private $gtm_id;

	/**
	 * Create custom integration based on service.
	 *
	 * @param array $site => passed in from parent object.
	 **/
	public function __construct( $site ) {

		// Generate the field we need to get from wp_sitemeta table.
		// And then actually fetch it via get_site_option().
		$this->site_id          = $site;
		$this->base_option_slug = 'web-services-site-' . $this->site_id . '-service-google_tag_manager-field';
		$this->gtm_id           = get_site_option( $this->base_option_slug . '-container_id' );

		$hook = get_site_option( $this->base_option_slug . '-wp_hook' );

		add_action( $hook, [ $this, 'add_gtm_to_wp_head' ] );
		add_action( 'wp_body_open', [ $this, 'add_gtm_no_script' ] );
	}

	/**
	 * Add the front-end markup for Google Tag Manager.
	 */
	public function add_gtm_to_wp_head() {

		if ( ! empty( $this->gtm_id ) ) {

			// Don't load actual tracking script if we're working locally.
			if ( defined( 'WP_ENV' ) && 'local' === WP_ENV ) {
				?>

				<script>
					console.log('WEB SERVICES: Google Tag Manager enabled for site:<?php echo esc_html( $this->site_id ); ?> container_id: <?php echo esc_html( $this->gtm_id ); ?>');
				</script>

				<?php

			} else {
				?>

				<!-- Google Tag Manager -->
				<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
				new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
				j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
				'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
				})(window,document,'script','dataLayer','<?php echo esc_html( $this->gtm_id ); ?>');</script>
				<!-- End Google Tag Manager -->

				<?php
			}
		}
	}


	/**
	 * Add the markup required by GTM in the body for no script situations
	 *
	 * @return void
	 */
	public function add_gtm_no_script() {

		if ( ! empty( $this->gtm_id ) ) {
			?>

			<!-- Google Tag Manager (noscript) -->
			<noscript>
				<iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr( $this->gtm_id ); ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe>
			</noscript>
			<!-- End Google Tag Manager (noscript) -->

			<?php
		}
	}
}