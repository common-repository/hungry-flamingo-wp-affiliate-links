<?php
namespace HungryFlamingo\WpAffiliateLinks;

// @codeCoverageIgnoreStart
defined( 'ABSPATH' ) || die(); // Avoid direct file access
// @codeCoverageIgnoreEnd


/**
 * Class for the plugin's admin REST API functionality
 */
class AdminRestEndpoints {

	/**
	 * Initialization of admin REST API functionality for plugin
	 */
	public static function admin_rest_init() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		} else {
			self::register_rest_routes();
		}
	}

	/**
	 * REST API permission callback. Only allowing admins.
	 */
	public static function permission_callback() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * REST API Callback for get-affiliate-links endpoint
	 */
	public static function get_affiliate_links( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		} else {

			$error = false;

			/** Retrieving the REST API data sent */
			$body_json = $request->get_param( 'data' );
			$body      = json_decode( $body_json, true );

			/** Retrieving the current affiliates stored in the DB */
			$affiliates_json = get_option( Utils::$plugin_slug_filtered . '_affiliates_json' );

			/** Check if any affiliate data stored already */
			if ( false !== $affiliates_json && 1 < strlen( $affiliates_json ) ) {
				$response_data = $affiliates_json;
			} else {
				$response_data = null;
			}

			/** Build response data */
			if ( false === $error ) {
				$response = array(
					'success' => true,
					'msg'     => '',
					'data'    => $response_data,
				);
			} else {
				$response = array(
					'success' => false,
					'msg'     => $error,
					'data'    => null,
				);
			}

			return rest_ensure_response( $response );
		}
	}

	/**
	 * REST API Callback for add-affiliate-link endpoint
	 */
	public static function add_affiliate_link( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		} else {

			$error = false;

			/** Retrieving the REST API data sent */
			$body_json = $request->get_param( 'data' );
			$body      = json_decode( $body_json, true );

			/** Parsing the URL as we only want the hostname */
			$url = wp_parse_url( esc_url_raw( $body['affiliate_url'] ) );

			/** Array for the new affiliate */
			$new_affiliate = array(
				'url'   => $url['host'],
				'icon'  => sanitize_text_field( $body['affiliate_icon'] ),
				'title' => sanitize_text_field( $body['affiliate_title'] ),
			);

			/** Retrieving the current affiliates stored in the DB */
			$affiliates_json = get_option( Utils::$plugin_slug_filtered . '_affiliates_json' );

			/** Check if any affiliate data stored already */
			if ( false !== $affiliates_json && 1 < strlen( $affiliates_json ) ) {
				/** Logic for adding affiliate data */
				$affiliates       = json_decode( $affiliates_json, true );
				$affiliate_exists = false;
				/** Check if affiliate URL has already been stored before */
				foreach ( $affiliates as $affiliate ) {
					if ( $affiliate['url'] === $new_affiliate['url'] ) {
						$affiliate_exists = true;
						$error            = 'affiliate already exists';
						continue;
					}
				}

				/** Add new affiliate if not exists already */
				if ( false === $error ) {
					$affiliates[] = $new_affiliate;
				}
			} else {
				/** Logic for brand new (first entry) affiliate data */
				$affiliates[] = $new_affiliate;

			}

			/** Update options if no error occured before */
			if ( false === $error ) {
				$error = ! update_option( Utils::$plugin_slug_filtered . '_affiliates_json', wp_json_encode( $affiliates, JSON_FORCE_OBJECT ) );
			}

			/** Build response data */
			if ( false === $error ) {
				$response = array(
					'success' => true,
					'msg'     => '',
					'data'    => 'asdfasdf',
				);
			} else {
				$response = array(
					'success' => false,
					'msg'     => $error,
					'data'    => 'asdfasdf',
				);
			}

			return rest_ensure_response( $response );
		}
	}

	/**
	 * REST API Callback for update-affiliate-link endpoint
	 */
	public static function update_affiliate_link( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		} else {

			$error = 'Update error';

			/** Retrieving the REST API data sent */
			$body_json = $request->get_param( 'data' );
			$body      = json_decode( $body_json, true );

			/** Parsing the URL as we only want the hostname */
			$url     = wp_parse_url( esc_url_raw( $body['affiliate_url'] ) );
			$old_url = wp_parse_url( esc_url_raw( $body['affiliate_old_url'] ) );
			/** Array for the new affiliate */
			$new_affiliate = array(
				'url'   => $url['host'],
				'icon'  => sanitize_text_field( $body['affiliate_icon'] ),
				'title' => sanitize_text_field( $body['affiliate_title'] ),
			);

			/** Retrieving the current affiliates stored in the DB */
			$affiliates_json = get_option( Utils::$plugin_slug_filtered . '_affiliates_json' );

			/** Check if any affiliate data stored already */
			if ( false !== $affiliates_json && 1 < strlen( $affiliates_json ) ) {
				/** Logic for adding affiliate data */
				$affiliates       = json_decode( $affiliates_json, true );
				$affiliate_exists = false;
				/** Check if affiliate URL has already been stored before */
				foreach ( $affiliates as $index => $affiliate ) {
					if ( $affiliate['url'] === $old_url['host'] ) {

						$affiliates[ $index ] = array(
							'url'   => $new_affiliate['url'],
							'icon'  => $new_affiliate['icon'],
							'title' => $new_affiliate['title'],
						);

						$error = false;
						continue;
					}
				}
			} else {
				/** Logic for brand new (first entry) affiliate data */
				$affiliates = array( $new_affiliate );
				$error      = false;

			}

			/** Update options if no error occured before and affiliate found */
			if ( false === $error ) {
				if ( ! update_option( Utils::$plugin_slug_filtered . '_affiliates_json', wp_json_encode( $affiliates, JSON_FORCE_OBJECT ) ) ) {
					$error = 'Nothing to update';
				}
			}

			/** Build response data */
			if ( false === $error ) {
				$response = array(
					'success' => true,
					'msg'     => 'Affiliate updated',
					'data'    => '',
				);
			} else {
				$response = array(
					'success' => false,
					'msg'     => $error,
					'data'    => '',
				);
			}

			return rest_ensure_response( $response );
		}
	}

	/**
	 * REST API Callback for delete-affiliate-link endpoint
	 */
	public static function delete_affiliate_link( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		} else {

			$error = 'Delete error';

			/** Retrieving the REST API data sent */
			$body_json = $request->get_param( 'data' );
			$body      = json_decode( $body_json, true );

			/** Parsing the URL as we only want the hostname */
			$url     = wp_parse_url( esc_url_raw( $body['affiliate_url'] ) );
			$old_url = wp_parse_url( esc_url_raw( $body['affiliate_old_url'] ) );
			/** Array for the new affiliate */
			$new_affiliate = array(
				'url'   => $url['host'],
				'icon'  => sanitize_text_field( $body['affiliate_icon'] ),
				'title' => sanitize_text_field( $body['affiliate_title'] ),
			);

			/** Retrieving the current affiliates stored in the DB */
			$affiliates_json = get_option( Utils::$plugin_slug_filtered . '_affiliates_json' );

			/** Check if any affiliate data stored already */
			if ( false !== $affiliates_json && 1 < strlen( $affiliates_json ) ) {
				/** Logic for adding affiliate data */
				$affiliates       = json_decode( $affiliates_json, true );
				$affiliate_exists = false;
				/** Check if affiliate URL has already been stored before */
				foreach ( $affiliates as $index => $affiliate ) {
					if ( $affiliate['url'] === $old_url['host'] ) {

						unset( $affiliates[ $index ] );
						$error = false;
						continue;
					}
				}
			}

			/** Update options if no error occured before and affiliate found */
			if ( false === $error ) {
				/** Reindex array */
				$affiliates = array_values( $affiliates );
				if ( ! update_option( Utils::$plugin_slug_filtered . '_affiliates_json', wp_json_encode( $affiliates, JSON_FORCE_OBJECT ) ) ) {
					$error = 'Nothing to delete';
				}
			}

			/** Build response data */
			if ( false === $error ) {
				$response = array(
					'success' => true,
					'msg'     => 'Affiliate deleted',
					'data'    => '',
				);
			} else {
				$response = array(
					'success' => false,
					'msg'     => $error,
					'data'    => '',
				);
			}

			return rest_ensure_response( $response );
		}
	}

	/**
	 * Registering routes to the REST API
	 */
	private static function register_rest_routes() {

		/** Register route for get-affiliate-links */
		register_rest_route(
			'hungry-flamingo/v1/' . Utils::$plugin_slug,
			'get-affiliate-links',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( '\HungryFlamingo\WpAffiliateLinks\AdminRestEndpoints', 'get_affiliate_links' ),
				'permission_callback' => array( '\HungryFlamingo\WpAffiliateLinks\AdminRestEndpoints', 'permission_callback' ),
			)
		);

		/** Register route for add-affiliate-link */
		register_rest_route(
			'hungry-flamingo/v1/' . Utils::$plugin_slug,
			'add-affiliate-link',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( '\HungryFlamingo\WpAffiliateLinks\AdminRestEndpoints', 'add_affiliate_link' ),
				'permission_callback' => array( '\HungryFlamingo\WpAffiliateLinks\AdminRestEndpoints', 'permission_callback' ),
			)
		);

		/** Register route for update-affiliate-link */
		register_rest_route(
			'hungry-flamingo/v1/' . Utils::$plugin_slug,
			'update-affiliate-link',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( '\HungryFlamingo\WpAffiliateLinks\AdminRestEndpoints', 'update_affiliate_link' ),
				'permission_callback' => array( '\HungryFlamingo\WpAffiliateLinks\AdminRestEndpoints', 'permission_callback' ),
			)
		);

		/** Register route for delete-affiliate-link */
		register_rest_route(
			'hungry-flamingo/v1/' . Utils::$plugin_slug,
			'delete-affiliate-link',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( '\HungryFlamingo\WpAffiliateLinks\AdminRestEndpoints', 'delete_affiliate_link' ),
				'permission_callback' => array( '\HungryFlamingo\WpAffiliateLinks\AdminRestEndpoints', 'permission_callback' ),
			)
		);
	}
}
