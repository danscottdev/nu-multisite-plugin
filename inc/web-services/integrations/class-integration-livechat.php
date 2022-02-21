<?php
/**
 * Web Services: Live Chat
 */

namespace NUEDU_Network\Inc\Web_Services\Integrations;

/**
 * Integration for LiveChat
 */
class Integration_LiveChat {

	/**
	 * Match slug of web service
	 *
	 * @var string
	 */
	private $service_slug = 'livechat';

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

		$this->livechat_license_id = get_site_option( $this->base_option_slug . '-license_id' );
		$this->livechat_button_id  = get_site_option( $this->base_option_slug . '-button_id' );

		// write_to_log( 'Livechat Constructor' );

		add_action( 'wp_footer', [ $this, 'add_chat_footer_markup' ], 99 );

		/**
		 * Below hooks were included in original NU-Core-Functionality plugin for Livechat, but the callback functions didn't exist?
		 * @todo Look into where these callback functions live:
		 *
		 * add_action( 'wp_footer', [ $this, 'add_livechat_code' ], 99 );
		 * add_filter( 'walker_nav_menu_start_el', [ $this, 'switch_to_buttons' ], 10, 2 );
		 */

	}


	/**
	 * Add the markup necessary to launch the chat in the footer
	 *
	 * @return void
	 */
	public function add_chat_footer_markup() {
		if ( is_singular( 'page' ) && ! empty( get_post_meta( get_the_ID(), '_page_livechat', true ) ) ) {
			?>
			<style>
				#chat-icon,
				.livechat-button{
					display: none;
				}
			</style>
			<?php
			return;
		}

		if ( is_singular( 'program' ) ) :
			?>
			<div class="newChat" id="chat-icon">
				<img src="<?php echo esc_url( NUEDU_NETWORK_FUNC_URL . 'assets/images/icon-chat.svg' ); ?>" alt="Chat with an Advisor">
			</div>
			<?php
		endif;
		?>
			<script>
				var chatLinks = document.querySelectorAll( '#chat-icon, .livechat-button button' );
				chatLinks.forEach( function( chatLink ) {
					chatLink.addEventListener( 'click', function( event ) {
						event.preventDefault();

						var chatWidget = document.getElementById( 'chat-widget-container' );
						if ( chatWidget ) {
							LiveChatWidget.call( 'maximize' );
							chatWidget.classList.add( 'd-block' );
						}
					} );
				} );

				LiveChatWidget.on( 'visibility_changed', function( data ) {
					if ( data.visibility === 'minimized' ) {
						var chatWidget = document.getElementById( 'chat-widget-container' );
						chatWidget.classList.remove( 'd-block' );
					}
				} );

				<?php if ( is_singular( 'program' ) ) : ?>
				if ( window.innerWidth >= 768 ) {
					window.addEventListener( 'load', function() {
						setTimeout( function() {
							var chatWidget = document.getElementById( 'chat-widget-container' );
							if ( chatWidget && LiveChatWidget ) {
								LiveChatWidget.call( 'maximize' );
								chatWidget.classList.add( 'd-block' );
							}
						}, 3000 );
					} );
				}
				<?php endif; ?>
			</script>
		<?php
	}



}