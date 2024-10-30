<?php
namespace HungryFlamingo\WpAffiliateLinks;

// @codeCoverageIgnoreStart
defined( 'ABSPATH' ) || die(); // Avoid direct file access
// @codeCoverageIgnoreEnd

/**
 * Plugin utils for general plugin management and configuration
 */
class Utils {

	public static $plugin_name;
	public static $plugin_slug;
	public static $plugin_slug_filtered;
	public static $plugin_version;
	public static $plugin_path;
	public static $plugin_url;
	public static $blocks_base_path;
	public static $blocks_base_url;

	public function __construct( $name, $slug, $version, $path, $url ) {
		self::$plugin_name          = $name;
		self::$plugin_slug          = $slug;
		self::$plugin_slug_filtered = str_replace( '-', '_', $slug );
		self::$plugin_version       = $version;
		self::$plugin_path          = $path;
		self::$plugin_url           = $url;
		self::$blocks_base_path     = $path . 'assets/blocks/';
		self::$blocks_base_url      = $url . 'assets/blocks/';
	}


}
