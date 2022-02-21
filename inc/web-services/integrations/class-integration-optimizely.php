<?php
/**
 * Optimizely Front-end integration
 */

namespace NUEDU_Network\Inc\Web_Services\Integrations;

/**
 * Integration for Optimizely
 */
class Integration_Optimizely {

	/**
	 * Constructor
	 *
	 * @param int $site -> Network Site ID we're generating the service for.
	 * */
	public function __construct( $site ) {

		// First, generate the field we need to retrieve from the wp_sitemeta table.
		$this->site_id          = $site;
		$this->base_option_slug = 'web-services-site-' . $this->site_id . '-service-optimizely-field';

		// Second, fetch relevant values via get_site_option().
		$this->integration_method          = get_site_option( $this->base_option_slug . '-integration_method' );
		$this->file_id                     = get_site_option( $this->base_option_slug . '-file_id' );
		$this->edge_worker_url             = get_site_option( $this->base_option_slug . '-edge_worker_url' );
		$this->performance_edge_target_ids = get_site_option( $this->base_option_slug . '-performance_edge_target_ids' );

		// Convert target IDs into an array for later usage.
		$this->target_ids = array_map( 'trim', explode( ',', $this->performance_edge_target_ids ) );

	// ###Start Dev Test
		write_to_log( '-------------------------------' );
		write_to_log( 'Optimizely constructor' );
		write_to_log( 'Base Option Slug: ' . $this->base_option_slug );
		write_to_log( 'Integration Method: ' . $this->integration_method );
		write_to_log( 'File ID: ' . $this->file_id );
		write_to_log( 'Edge Worker URL: ' . $this->edge_worker_url );
		write_to_log( 'Target IDs: ' . $this->performance_edge_target_ids );
		write_to_log( '-------------------------------' );
	// ###END Dev Test

		// Third, add filters & hooks.
		add_filter( 'wp_resource_hints', [ $this, 'preconnect_optimizely' ], 10, 2 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_optimizely_web_file' ], -1000 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_performance_edge' ], -1000 );
		add_action( 'script_loader_tag', [ $this, 'do_script_loader_tag' ], 10, 3 );
	}

	/**
	 * Preconnect to Optimizely URL WP native way
	 * ref: https://developer.wordpress.org/reference/functions/wp_resource_hints/
	 *
	 * @param array  $urls          URLs to print for resource hints.
	 * @param string $relation_type The relation type the URLs are printed for, e.g. 'preconnect' or 'prerender'.
	 *
	 * @return array
	 */
	public function preconnect_optimizely( $urls, $relation_type ) {

		if (
			isset( $this->file_id )
			&& 'Performance Edge' !== $this->integration_method
			&& 'preconnect' === $relation_type
		) {
			$urls[] = '//cdn.optimizely.com';
		}

		return $urls;
	}

	/**
	 * Enqueue Scripts -- if Integration Method set to "Optimizely Web"
	 */
	public function enqueue_optimizely_web_file() {

		if ( 'Performance Edge' !== $this->integration_method || ! empty( $this->performance_edge_target_ids ) ) {

			// Bail if we are displaying the Performance Edge integration.
			if ( in_array( (string) get_the_ID(), $this->target_ids, true ) ) {
				return;
			}

			if ( ! empty( $this->file_id ) ) {
				$cache_buster = ! empty( $_GET['testing'] ) ? time() : $this->get_cache_buster_value(); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

				wp_enqueue_script( 'optimizely-web', 'https://cdn.optimizely.com/js/' . esc_attr( $this->file_id ) . '.js', [], $cache_buster, false );

			// ###Start Dev Test
				// write_to_log( '[OPT] Enqueue for Optimizely Web: https://cdn.optimizely.com/js/' . esc_attr( $this->file_id ) );

			}
		}
	}

	/**
	 * Enqueue Scripts -- if Integration Method set to "Performance Edge"
	 */
	public function enqueue_performance_edge() {

		if ( 'Performance Edge' === $this->integration_method ) {


			if ( ! empty( $this->edge_worker_url ) && empty( array_filter( $this->target_ids ) ) || in_array( (string) get_the_ID(), $this->target_ids, true ) ) {
				wp_enqueue_script( 'optimizely-performance-edge', $this->edge_worker_url, [], false, false ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NoExplicitVersion

				// ###Start Dev Test
				// write_to_log( '[OPT] Enqueue for Performance Edge: ' . esc_attr( $this->edge_worker_url ) );
			}
		}
	}

	/**
	 * Do Script Loader Tag
	 * Allows enqueued scripts to be loaded asynchronously, thus preventing the
	 * page from being blocked by js calls.
	 *
	 * @param  string $tag    The <script> tag for the enqueued script.
	 * @param  string $handle The script's registered handle.
	 *
	 * @return string The formatted HTML script tag of the given enqueued script.
	 */
	public function do_script_loader_tag( $tag, $handle ) {
		if ( 'optimizely-performance-edge' === $handle ) {
			$tag = str_replace( ' src=', ' referrerpolicy="no-referrer-when-downgrade" src=', $tag );
		}

		return $tag;
	}

	/**
	 * Cache buster value in epoch timestamp
	 *
	 * @param int $minutes Minutes to round down to for cache time.
	 *
	 * @return int
	 */
	private function get_cache_buster_value( $minutes = 15 ) {
		$current_time  = time();
		$minutes_epoch = $minutes * 60;

		$time_string = $current_time - ( $current_time % $minutes_epoch );

		return $time_string;
	}
}