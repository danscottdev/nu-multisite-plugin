<?php
/**
 * Manage Plugin initialization
 */

namespace NUEDU_Network\Autoload;

/** Plugin initializer */
class Init {

	/**
	 * Classes/modules to load for this plugin
	 *
	 * @var class_names namespaces for modules/classes to load
	 */

	private $class_names = [
		'Inc\Settings_Page',                            // Creates all menu & submenu settings pages.
		'Inc\Global_Variables\Global_Shortcodes',       // Shortcodes to output values stored in Global Variables.
		'Inc\Web_Services\Web_Services_Integration',    // Integrates web service settings configurations with actual site front-ends.
		'Inc\Web_Services\Metadata\Metadata_LiveChat',  // Integrates LiveChat service options into page edit metadata, so wp-admin can toggle on/off on page-by-page basis.
	];

	/** Constructor */
	public function __construct() {
		$this->initiate_classes();
	}

	/** Load each module */
	public function initiate_classes() {
		foreach ( $this->class_names as $class_name ) {
			$full_name = 'NUEDU_Network\\' . $class_name;
			new $full_name();
		}
	}
}