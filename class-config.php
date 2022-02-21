<?php
/**
 * CONFIG FILE
 * Holds all of our values for Global Variables and Web Services
 *
 * Split into own file/class to make it easy to update or add/remove in the future
 */

namespace NUEDU_Network;

/** Config */
class Config {

	/**
	 * Global Variables
	 *   'slug' => Used for saving/generating/getting the actual field in the wpdb.
	 *             **IMPORTANT** Changing this will potentially break things, since it changes the value that get_site_option() looks for
	 *   'title' => Label printed to the admin page for this field
	 *
	 * @var array
	 */
	public static $global_variables = [
		[
			'slug'       => 'alumni_number',
			'title'      => 'Total Alumni #',
			'input_type' => 'number',
		],
		[
			'slug'       => 'degrees_number',
			'title'      => 'Degrees/Programs Number',
			'input_type' => 'number',
		],
		[
			'slug'       => 'contact_phone',
			'title'      => 'Phone Number',
			'input_type' => 'number',
		],
		[
			'slug'       => 'main_address',
			'title'      => 'Main Address',
			'input_type' => 'textarea',
		],
	];



	/**
	 * Web Services Config Array
	 *
	 * @var array
	 */
	public static $global_web_services = [
		[
			'slug'   => 'google_tag_manager',
			'title'  => 'Google Tag Manager',
			'fields' => [
				[
					'slug'  => 'enabled',
					'title' => 'Enabled?',
					'type'  => 'checkbox',
				],
				[
					'slug'        => 'container_id',
					'title'       => 'Container ID',
					'description' => 'Example: GTM-XXXXXXX.',
					'type'        => 'text',
				],
				[
					'slug'        => 'wp_hook',
					'title'       => 'WP Hook',
					'description' => 'Hook to use to add GTM tag.',
					'type'        => 'dropdown',
					'values'      => [ 'wp_head', 'head_begins' ],
				],
			],
		],
		[
			'slug'   => 'optimizely',
			'title'  => 'Optimizely',
			'fields' => [
				[
					'slug'  => 'enabled',
					'title' => 'Enabled?',
					'type'  => 'checkbox',
				],
				[
					'slug'   => 'integration_method',
					'title'  => 'Integration Method',
					'type'   => 'dropdown',
					'values' => [ 'Performance Edge', 'Optimizely Web' ],
				],
				[
					'slug'        => 'file_id',
					'title'       => 'File ID',
					'description' => 'Only used with Optimizely Web integration.',
					'type'        => 'text',
				],
				[
					'slug'        => 'edge_worker_url',
					'title'       => 'Edge Worker URL',
					'description' => 'Only used with Performance Edge integration.',
					'type'        => 'text',
				],
				[
					'slug'        => 'performance_edge_target_ids',
					'title'       => 'Target IDs',
					'description' => 'Only for Performance Edge. List of comma-delimited page, post, or custom post type IDs to use the Performance Edge testing. If left blank, it will be applied to the whole site.',
					'type'        => 'text',
				],
			],
		],
		[
			'slug'   => 'brightedge',
			'title'  => 'BrightEdge',
			'fields' => [
				[
					'slug'  => 'enabled',
					'title' => 'Enabled?',
					'type'  => 'checkbox',
				],
				[
					'slug'        => 'sdk_account_id',
					'title'       => 'SDK Account ID',
					'description' => '',
					'type'        => 'text',
				],
			],
		],
		[
			'slug'   => 'livechat',
			'title'  => 'LiveChat',
			'fields' => [
				[
					'slug'  => 'enabled',
					'title' => 'Enabled?',
					'type'  => 'checkbox',
				],
				[
					'slug'        => 'license_id',
					'title'       => 'License ID',
					'description' => 'Ex: #######',
					'type'        => 'text',
				],
				[
					'slug'        => 'button_id',
					'title'       => 'Button Element ID',
					'description' => 'Ex: xx99x9x9xxx.',
					'type'        => 'text',
				],
			],
		],
		[
			'slug'   => 'linkequitymanager',
			'title'  => 'Link Equity Manager',
			'fields' => [
				[
					'slug'  => 'enabled',
					'title' => 'Enabled?',
					'type'  => 'checkbox',
				],
				[
					'slug'        => 'fallback_links',
					'title'       => 'Fallback Links',
					'description' => 'One link per line, where Title and URL are pipe delimited (Title of link|URL).',
					'type'        => 'text',
				],
			],
		],
		[
			'slug'   => 'api_keys',
			'title'  => 'API Keys',
			'fields' => [
				[
					'slug'        => 'youtube',
					'title'       => 'Youtube',
					'description' => 'API Key to be used across the site by services. If blank, the JS embed will fallback to a method that will not utilize the API Key.',
					'type'        => 'text',
				],
			],
		],
	];

}