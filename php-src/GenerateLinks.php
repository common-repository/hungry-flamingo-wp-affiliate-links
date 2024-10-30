<?php
namespace HungryFlamingo\WpAffiliateLinks;

use DOMDocument;

// @codeCoverageIgnoreStart
defined( 'ABSPATH' ) || die(); // Avoid direct file access
// @codeCoverageIgnoreEnd


/**
 * Generate affiliate links across the content
 */
class GenerateLinks {

	/**
	 * Automatically adding affiliate link logic to certain links.
	 * Adds styles, rel, icon, and ad notice.
	 * @param string $content - the WordPress content string.
	 * @return string $content - the processed content with added affiliate link logic.
	 */
	public function hungry_flamingo_auto_add_affiliate_links( $content ) {

		$affiliates_json = get_option( Utils::$plugin_slug_filtered . '_affiliates_json' );
		$affiliates      = json_decode( $affiliates_json, true );

		if ( is_array( $affiliates ) && count( $affiliates ) !== 0 ) {
			$dom = new DOMDocument();
			$dom->loadHTML( '<?xml encoding="UTF-8">' . $content );

			$hyperlinks = $dom->getElementsByTagName( 'a' );
			/** Loop over all hyperlinks */
			foreach ( $hyperlinks as $hyperlink ) {

				/** Loop over all link targets */
				foreach ( $affiliates as $affiliate => $link_target ) {

					/** Process hyperlink if directed to one of affiliate link target urls */
					if ( 1 === preg_match( '/.*\n*' . $link_target['url'] . '.*\n*/', $hyperlink->getAttribute( 'href' ) ) ) {

						/** Form proper rel attribute */
						$rel = explode( ' ', $hyperlink->getAttribute( 'rel' ) );
						array_push( $rel, 'sponsored', 'nofollow', 'noopener', 'noreferrer' );
						$rel = array_unique( $rel );
						$rel = implode( ' ', $rel );
						$hyperlink->setAttribute( 'rel', $rel );

						/** Make hyperlink open in new tab */
						$hyperlink->setAttribute( 'target', '_blank' );

						/** Check if hyperlink is block from plugin */
						$class = explode( ' ', $hyperlink->getAttribute( 'class' ) );
						if ( false === array_search( 'hungry-flamingo-wp-affiliate-links-affiliate-ad-block', $class ) ) {

							/** Add hungry-flamingo-wp-affiliate-link class to hyperlink */
							array_push( $class, 'hungry-flamingo-wp-affiliate-link' );
							$class = array_unique( $class );
							$class = implode( ' ', $class );
							$hyperlink->setAttribute( 'class', $class );

							if ( isset( $link_target['icon'] ) && strlen( $link_target['icon'] ) > 1 ) {
								/** Append affiliate target icon */
								$icon = $dom->createElement( 'img' );
								$icon->setAttribute( 'src', $link_target['icon'] );
								$icon->setAttribute( 'alt', $link_target['title'] );
								$icon->setAttribute( 'width', '40px' );
								$hyperlink->appendChild( $icon );
							}

							/** Append term to disclose ad character of this hyperlink */
							$ad = $dom->createElement( 'span', __( 'Werbung', 'hungry-flamingo-wp-affiliate-links' ) );
							$ad->setAttribute( 'class', 'hungry-flamingo-wp-affiliate-link-ad-term' );
							$hyperlink->appendChild( $ad );
						}
					}
				}
			}

			$content = htmlspecialchars_decode( $dom->SaveHTML() );
		}

		return $content;
	}

}
