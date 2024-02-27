<?php
/**
 * Helpers functions,
 *
 * @package BlueDolphin/Lms
 */

namespace BlueDolphin\Lms;

/**
 * Utility method to insert before specific key
 * in an associative array.
 *
 * @param string $key The key before to insert.
 * @param array  $item The array in which to insert the new key.
 * @param string $new_key The new key name.
 * @param mixed  $new_value The new key value.
 *
 * @return array|bool
 * @since   1.0.0
 * @access  public
 */
function array_insert_before( $key, $item, $new_key, $new_value ) {
	if ( array_key_exists( $key, $item ) ) {
		$new = array();
		foreach ( $item as $k => $value ) {
			if ( $k === $key ) {
				$new[ $new_key ] = $new_value;
			}
			$new[ $k ] = $value;
		}

		return $new;
	}

	return false;
}
