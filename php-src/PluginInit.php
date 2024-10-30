<?php
namespace HungryFlamingo\WpAffiliateLinks;

// @codeCoverageIgnoreStart
defined( 'ABSPATH' ) || die(); // Avoid direct file access
// @codeCoverageIgnoreEnd


class PluginInit {

	/**
	 * Initialize admin, enqueue scripts etc.
	 *
	 * @param void
	 * @return void
	 */
	public static function plugin_init() {

		/** Initialize frontend */
		if ( ! is_admin() ) {
			/** Enqueue frontend scripts and styles */
			add_action( 'wp_enqueue_scripts', array( '\HungryFlamingo\WpAffiliateLinks\Enqueue', 'enqueue_frontend_css' ), 10 );
			#add_action( 'wp_enqueue_scripts', array( '\HungryFlamingo\WpAffiliateLinks\Enqueue', 'enqueue_frontend_js' ), 10 );

			/** Initialize affiliate link generation */
			$generate_links = new GenerateLinks();
			add_filter( 'the_content', array( $generate_links, 'hungry_flamingo_auto_add_affiliate_links' ), 10 );

		}

		/** Initialize backend */
		if ( is_admin() ) {
			add_action( 'admin_menu', array( '\HungryFlamingo\WpAffiliateLinks\AdminInit', 'admin_init' ), 10 );
		}

		add_action( 'rest_api_init', array( '\HungryFlamingo\WpAffiliateLinks\AdminRestEndpoints', 'admin_rest_init' ), 10 );

		add_action( 'admin_init', array( '\HungryFlamingo\WpAffiliateLinks\PluginInit', 'register_settings' ), 10 );
		add_action( 'rest_api_init', array( '\HungryFlamingo\WpAffiliateLinks\PluginInit', 'register_settings' ), 10 );

		add_filter( 'block_categories_all', array( '\HungryFlamingo\WpAffiliateLinks\PluginInit', 'register_block_categories' ), 10 );
		add_action( 'init', array( '\HungryFlamingo\WpAffiliateLinks\PluginInit', 'register_blocks' ), 10 );
	}

	public static function register_settings() {

		register_setting( Utils::$plugin_slug_filtered, Utils::$plugin_slug_filtered . '_affiliates_json' );
	}

	public static function register_block_categories( $categories ) {
		$categories[] = array(
			'slug'  => 'hungry-flamingo',
			'title' => 'Hungry Flamingo',
			'icon'  => '',
		);

		return $categories;
	}

	public static function register_blocks() {
		register_block_type(
			Utils::$blocks_base_path . 'affiliate-ad-block',
			array(
				'render_callback' => array( '\HungryFlamingo\WpAffiliateLinks\PluginInit', 'render_block_affiliate_ad_block' ),
				'attributes'      => array(
					'align'            => array(
						'type'    => 'string',
						'default' => '',
					),
					'href'             => array(
						'type'    => 'string',
						'default' => '',
					),
					'linkText'         => array(
						'type'    => 'string',
						'default' => '',
					),
					'icon'             => array(
						'type'    => 'string',
						'default' => '',
					),
					'active'           => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'adDisclosureTerm' => array(
						'type'    => 'string',
						'default' => '',
					),
					'affiliatesJson'   => array(
						'type'    => 'string',
						'default' => wp_json_encode(
							json_decode(
								get_option(
									Utils::$plugin_slug_filtered . '_affiliates_json',
								),
								true
							),
							JSON_FORCE_OBJECT
						),
					),
					'menuPageUrl'      => array(
						'type'    => 'string',
						'default' => esc_url( get_admin_url() . '/admin.php?page=' . Utils::$plugin_slug ),
					),
				),
			)
		);
	}

	public static function render_block_affiliate_ad_block( $attributes, $rendered ) {

		return $rendered;
	}


}
