<?php

class MC4WP_Ecommerce_Tracker {

	/**
	 * Add hooks
	 */
	public function hook() {
		add_action( 'init', array( $this, 'listen' ) );
	}

	/**
	 * Listen for "mc_cid" and "mc_eid" in the URL.
	 */
	public function listen() {

		$keys = array( 'mc_cid', 'mc_eid' );
		$cookie_time = 14 * 24 * 60 * 60; // 14 days

		foreach( $keys as $key ) {
			$value = $this->get_url_value( $key );

			if( ! empty( $value ) ) {
				setcookie( $key, $value, time() + $cookie_time, '/' );
			}
		}
	}

	/**
	 * @return string
	 */
	public function get_campaign_id() {

		$value = $this->get_url_value( 'mc_cid' );

		if( empty( $value ) ) {
			$value = $this->get_cookie_value( 'mc_cid' );
		}

		return $value;
	}

	/**
	 * @return string
	 */
	public function get_email_id() {
		$value = $this->get_url_value( 'mc_eid' );

		if( empty( $value ) ) {
			$value = $this->get_cookie_value( 'mc_eid' );
		}

		return $value;
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	protected function get_url_value( $key ) {
		if( empty( $_GET[ $key ] ) ) {
			return '';
		}

		return sanitize_text_field( $_GET[ $key ] );
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	protected function get_cookie_value( $key ) {
		if( empty( $_COOKIE[ $key ] ) ) {
			return '';
		}

		return sanitize_text_field( $_COOKIE[ $key ] );
	}
}