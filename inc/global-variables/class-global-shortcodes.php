<?php
/**
 * Front-end implementation of shortcodes.
 *
 * @package national-university
 */

namespace NUEDU_Network\Inc\Global_Variables;

/**
 * Shortcodes Setup
 */
class Global_Shortcodes {


	/**
	 * Construct it
	 */
	public function __construct() {
		add_shortcode( 'global_alumni_number', [ $this, 'output_alumni_number' ] );
		add_shortcode( 'global_degrees_number', [ $this, 'output_degrees_number' ] );
		add_shortcode( 'global_contact_phone', [ $this, 'output_global_contact_phone' ] );
	}

	/**
	 * Shortcode to output alumni number
	 * [global_alumni_number]
	 *
	 * args = divisor & decimals
	 * Allows us to decide on a case-by-case basis if we want to display 180,000 or 180k or 180.0k or etc
	 *
	 * @param array $atts Attributes passed to the shortcode.
	 * @return string
	 */
	public function output_alumni_number( $atts ) {

		$alumni_number = intval( get_site_option( 'global-variables-field-alumni_number' ) );

		if ( empty( $alumni_number ) ) {
			return;
		}

		$args = shortcode_atts( [
			'divisor'  => 1,
			'decimals' => 0,
		], $atts );

		// Since shortcode $atts are always passed as Strings, need to convert to ints for this to work.
		// Also if parameters are entered incorrectly (not numeric), then give fallbacks.
		$decimals = (int) $args['decimals'];
		$divisor  = (int) $args['divisor'];
		$divisor  = is_numeric( $args['divisor'] ) ? intval( $args['divisor'] ) : 1;

		// Convert to integer and apply parameters as needed.
		$output = $alumni_number / $divisor;
		$output = number_format( $output, $decimals );
		return $output;
	}

	/**
	 * Shortcode to output degrees/programs number
	 * [global_degrees_number]
	 *
	 * @param array $atts Attributes passed to the shortcode.
	 * @return string
	 */
	public function output_degrees_number( $atts ) {
		$degrees_number = intval( get_site_option( 'global-variables-field-degrees_number' ) );

		if ( empty( $degrees_number ) ) {
			return;
		}

		return number_format( $degrees_number );
	}

	/**
	 * Shortcode to output NU contact phone number
	 * [global_contact_phone]
	 *
	 * args: link
	 * lets us decide if we just want to display the phone number, or if we want to have it be a clickable <a> link
	 *
	 * @param array $atts Attributes passed to the shortcode.
	 * @return string
	 */
	public function output_global_contact_phone( $atts ) {
		$contact_phone = get_site_option( 'global-variables-field-contact_phone' );

		$prepend = '';
		$append  = '';

		if ( empty( $contact_phone ) ) {
			return;
		}

		$args = shortcode_atts( [
			'link' => 0,
		], $atts );

		if ( $args['link'] ) {
			$prepend = '<a href="tel:+1' . $contact_phone . '">';
			$append  = '</a>';
		}

		return $prepend . $contact_phone . $append;
	}


}