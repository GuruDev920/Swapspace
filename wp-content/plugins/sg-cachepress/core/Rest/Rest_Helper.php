<?php
namespace SiteGround_Optimizer\Rest;

/**
 * Rest Helper class that process all rest requests and provide json output for react app.
 */
abstract class Rest_Helper {
	/**
	 * Checks if the `option_key` paramether exists in rest data.
	 *
	 * @since  5.0.0
	 *
	 * @param  object $request Request data.
	 * @param  string $key     The option key.
	 * @param  bool   $bail    Whether to send json error or to return a response.
	 *
	 * @return string          The option value.
	 */
	public function validate_and_get_option_value( $request, $key, $bail = true ) {
		$data = json_decode( $request->get_body(), true );

		// Bail if the option key is not set.
		if ( ! isset( $data[ $key ] ) ) {
			return true === $bail ? wp_send_json_error() : false;
		}

		return $data[ $key ];
	}
}
