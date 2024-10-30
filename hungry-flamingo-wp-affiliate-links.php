<?php
/*
 * Plugin Name: Hungry Flamingo WP Affiliate Links
 * Plugin URI: https://hungryflamingo.com/wordpress/plugins/hungry-flamingo-wp-affiliate-links/
 * Description: Get advanced functionality for affiliate links and finally master affiliate-based monetization of your blog or website. Add beautiful and recognizable affiliate links. Embedded in your content and in blocks.
 * Version: 1.0.1
 * Author: Hungry Flamingo
 * Author URI:  https://hungryflamingo.com
 * License: GPL v3 or later
 * Copyright: Hungry Flamingo <https://hungryflamingo.com>
 * Text Domain: hungry-flamingo-wp-affiliate-links
 * Domain Path: /languages
 */

/** Hungry Flamingo WP Affiliate Links plugin for WordPress.

Copyright (C) 2022 by Hungry Flamingo <https://hungryflamingo.com/legal-notice>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <https://www.gnu.org/licenses/>.
 **/

#namespace HungryFlamingo\WpAffiliateLinks;

// @codeCoverageIgnoreStart
defined( 'ABSPATH' ) || die(); // Avoid direct file access
// @codeCoverageIgnoreEnd


/** Load plugin src and deps via Composer */
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';


/** Initialize plugin utils */
$hf_wpal_utils = new \HungryFlamingo\WpAffiliateLinks\Utils(
	'Hungry Flamingo WP Affiliate Links',
	'hungry-flamingo-wp-affiliate-links',
	'1.0.1',
	plugin_dir_path( __FILE__ ),
	plugin_dir_url( __FILE__ )
);

\HungryFlamingo\WpAffiliateLinks\PluginInit::plugin_init();
