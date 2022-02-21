<?php
/**
 * Activation class for multisite plugin
 *
 * @package national-university
 *
 * https://www.codetab.org/tutorial/wordpress-plugin-development/activation/multisite-activation/
 * --> future reference, if specific code needs to run on all network sites on plugin activation
 */

namespace NUEDU_Network\Autoload;

/**
 * Activation
 * Plugin activation hook
 * Currently, only checks to make sure plugin is only activated at network-level, and not site-level
 * Split out into own file/function in case we want to do anything else here in the future
 */
class Activation {
	/**
	 * Constructor
	 *
	 * @param boolean $network_wide Arg provided by WP core on plugin activation hooks.
	 */
	public function __construct( $network_wide ) {
		$this->check_if_multisite( $network_wide );
	}

	/**
	 * Make sure plugin is only enabled at network-level, not individual site-level
	 *
	 * @param boolean $network_wide Arg provided by WP core on plugin activation hooks.
	 */
	protected function check_if_multisite( $network_wide ) {
		if ( ! $network_wide || ! is_multisite() ) {
			die( 'This plugin should only be activated at the network level.' );
		}
	}
}