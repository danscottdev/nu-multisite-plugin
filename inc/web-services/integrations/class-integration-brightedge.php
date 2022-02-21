<?php
/**
 * BrightEdge
 */

namespace NUEDU_Network\Inc\Web_Services\Integrations;

/**
 * Integration
 *
 * @todo In the actual <script> tag, looks like there are multiple variables (other than sdk id) being set? Should those be included in admin page?
 */
class Integration_BrightEdge {

	/**
	 * Match slug of web service
	 *
	 * @var string
	 */
	private $service_slug = 'brightedge';

	/**
	 * Using construct method to add any actions and filters
	 *
	 * @param array $site => passed in from parent object.
	 */
	public function __construct( $site ) {

		// Generate the field we need to get from wp_sitemeta table.
		// And then actually fetch it via get_site_option().
		$this->site_id          = $site;
		$this->base_option_slug = 'web-services-site-' . $this->site_id . '-service-' . $this->service_slug . '-field';
		$this->brightedge_id    = get_site_option( $this->base_option_slug . '-sdk_account_id' );

		add_action( 'head_begins', [ $this, 'add_brightedge_code' ], 0 ); // 'head_begins' custom hook created by our themes.
	}

	/**
	 * Add the markup required for BrightEdge to be delivered in an optimized way.
	 * Relies on the theme specific hook "head_begins"
	 *
	 * @return void
	 */
	public function add_brightedge_code() {

		if ( ! empty( $this->brightedge_id ) ) {

		// ##DEV TESTING -- START
			if ( defined( 'WP_ENV' ) && 'local' === WP_ENV ) {
				?>
				<script>
					console.log('WEB SERVICES: BrightEdge enabled for site:<?php echo esc_html( $this->site_id ); ?> sdk id: <?php echo esc_html( $this->brightedge_id ); ?>');
				</script>
				<?php
			} else {
		// ## DEV TESTING -- END


				// ------------ BrightEdge Code ------------
				// Access to and use of BrightEdge Link Equity Manager is governed by the
				// Infrastructure Product Terms located at: www.brightedge.com/infrastructure-product-terms.
				// Customer acknowledges and agrees it has read, understands and agrees to be bound by the
				// Infrastructure Product Terms.
				// IXF: Deploy these scripts as the first item in the <head>.  Because this content is SEO-related,
				// it needs to be loaded into the DOM as early as possible to ensure that Google will include
				// it in the page snapshot.
				// IXF: By default, all URL parameters are ignored. If you have URL parameters that add value to
				// page content.  Add them to this config value, separated by the pipe character (|).
				?>

				<script>
					(function(){
						var script = document.createElement( 'script' );
						script.type = 'text/javascript';
						script.async = true;
						script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.bc0a.com/be_ixf_js_sdk.js';
						script.onload = function() {
							var be_sdk_options = {
								'api.endpoint': 'https://xxxxxxxxxx', //blanked out for portfolio purposes
								'sdk.account': '<?php echo esc_js( $this->brightedge_id ); ?>',
								'whitelist.parameter.list': 'ixf',
								'loglevel': '3'
							};
							BEJSSDK.construct( be_sdk_options );
						};
						document.getElementsByTagName( 'head' )[0].appendChild( script );
					})();
				</script>

				<?php
			}
		}
	}
}