<?php
/**
 * Ingredient-helpers.php
 *
 * @package yummy-recipes
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'yummy_convert_fraction_to_decimal' ) ) {
	/**
	 * Converts number with fractions to decimal number.
	 *
	 * @param  string $fraction Number to convert.
	 * @return float
	 */
	function yummy_convert_fraction_to_decimal( $fraction, $decimals = 3 ) {

		$charmap = yummy_get_fractions();

		foreach ( $charmap as $key => $value ) {
			if ( false !== strpos( $fraction, $key ) ) {
				$fraction = str_replace( $key, ' ' . $value, $fraction );
			}
		}

		// Remove double spaces.
		$fraction = preg_replace( '/\s+/', ' ', $fraction );

		if ( false !== strpos( $fraction, '/' ) ) {
			if ( false === strpos( $fraction, ' ' ) ) {
				$numbers = explode( '/', $fraction );

				return round( trim( $numbers[0] ) / trim( $numbers[1] ), $decimals );
			} elseif ( false !== strpos( $fraction, ' ' ) ) {
				$number_1 = explode( ' ', $fraction )[0];
				$number_2 = str_replace( $number_1 . ' ', '', $fraction );
				$number_2 = explode( '/', $number_2 );

				if ( ! is_numeric( $number_1 ) ) {
					$number_1 = 0;
				}

				return trim( $number_1 ) + round( trim( $number_2[0] ) / trim( $number_2[1] ), $decimals );
			}
		}

		return $fraction;
	}
}

if ( ! function_exists( 'yummy_get_numeric_values' ) ) {
	/**
	 * Converts ingredient amount to a numeric value.
	 *
	 * @param string $amount      Amount.
	 * @param string $amount_type Amount type.
	 *
	 * @return mixed
	 */
	function yummy_get_numeric_values( $amount, $amount_type ) {

		if ( empty( $amount ) ) {
			return false;
		}

		$amount = trim( $amount );

		$amount = str_replace( ',', '.', $amount );

		if ( 'fraction1' === $amount_type || 'fraction2' === $amount_type || 'fraction3' === $amount_type ) {
			$amount = yummy_convert_fraction_to_decimal( $amount );
		}

		if ( is_numeric( $amount ) ) {
			return $amount;
		}

		// If the are two separate numbers in the value. For example: 1 to 2, 1 or 2. 1-2, 1 - 2.
		if ( strlen( $amount ) > 2 && false === strpos( $amount, '/' ) ) {
			preg_match_all( '!\d+(?:\.\d+)?!', $amount, $matches );

			if ( ! empty( $matches ) && ! empty( $matches[0] ) && count( $matches[0] ) > 1 ) {
				return implode( '|', $matches[0] );
			}
		}

		return false;
	}
}

if ( ! function_exists( 'yummy_get_amount_type' ) ) {
	/**
	 * Checks if a string is numeric, numeric with comma, includes a fraction symbol etc.
	 *
	 * @param string $value Amount value.
	 *
	 * @return string|boolean
	 */
	function yummy_get_amount_type( $value ) {

		$value = trim( $value );

		// If the value is numeric.
		if ( is_numeric( $value ) ) {
			return 'numeric1';
		}

		// If the value is numeric after replacing comma with dot.
		if ( is_numeric( str_replace( ',', '.', $value ) ) ) {
			return 'numeric2';
		}

		// Get the string length.
		$strlen = mb_strlen( $value, 'UTF-8' );

		// Get the first character.
		$first = mb_substr( $value, 0, 1, 'UTF-8' );

		// Get the last character.
		$last = mb_substr( $value, $strlen - 1, null, 'UTF-8' );

		// If value starts or ends with other character than number.
		if ( $strlen > 2 && ( ! is_numeric( $first ) || ! is_numeric( $last ) ) ) {
			if ( ! yummy_is_fraction_symbol( $first ) && ! yummy_is_fraction_symbol( $last ) ) {
				return false;
			}
		}

		if ( $strlen > 2 ) {

			// Remove all numbers to check if the string contains other characters than numbers.
			$not_numbers = preg_replace( '/\d+/u', '', $value );

			// If the string contains only numbers, and one '/'.
			if ( ! empty( $not_numbers ) && strlen( $not_numbers ) === 1 && false !== strpos( $value, '/' ) ) {
				return 'fraction1';
			}

			// Get all numbers.
			preg_match_all( '!\d+(?:\.\d+)?!', $value, $matches );

			// If the are two separate numbers in the value. For example: 1 to 2, 1 or 2. 1-2, 1 - 2 etc.
			if ( ! empty( $not_numbers ) && ! empty( $matches ) && ! empty( $matches[0] ) && count( $matches[0] ) > 1 ) {
				if ( false === strpos( $value, '/' ) ) {
					return 'numeric3';
				}

				if ( false !== strpos( $value, '/' ) ) {
					$value = str_replace( '/', '', $value );
					$value = str_replace( ' ', '', $value );

					$not_numbers = preg_replace( '/\d+/u', '', $value );
					if ( empty( $not_numbers ) ) {
						return 'fraction3';
					}
				}
			}
		}

		$fractions = yummy_get_fractions();

		// Check if has a fraction symbol.
		foreach ( $fractions as $key => $values ) {
			if ( false !== strpos( $value, $key ) ) {

				// Remove the fraction symbol.
				$fraction_symbol_removed = str_replace( $key, '', $value );
				$fraction_symbol_removed = str_replace( ' ', '', $fraction_symbol_removed );

				// Remove all numbers to check if the string contains other characters than numbers.
				$not_numbers = preg_replace( '/\d+/u', '', $fraction_symbol_removed );

				// If there are only an optional whole number and the fraction symbol.
				if ( 0 === strlen( $not_numbers ) ) {
					return 'fraction2';
				}
			}
		}

		return false;
	}
}

if ( ! function_exists( 'yummy_get_fractions' ) ) {
	/**
	 * Returns an array of fractions.
	 *
	 * @return array
	 */
	function yummy_get_fractions() {

		$charmap = array(
			'\u00BC' => '1/4',
			'\u00BD' => '1/2',
			'\u00BE' => '3/4',
			'\u2150' => '1/7',
			'\u2151' => '1/9',
			'\u2152' => '1/10',
			'\u2153' => '1/3',
			'\u2154' => '2/3',
			'\u2155' => '1/5',
			'\u2156' => '2/5',
			'\u2157' => '3/5',
			'\u2158' => '4/5',
			'\u2159' => '1/6',
			'\u215A' => '5/6',
			'\u215B' => '1/8',
			'\u215C' => '3/8',
			'\u215D' => '5/8',
			'\u215E' => '7/8',
			'⅐'      => '1/7',
			'⅑'      => '1/9',
			'⅒'      => '1/10',
			'¼'      => '1/4',
			'½'      => '1/2',
			'¾'      => '3/4',
			'⅓'      => '1/3',
			'⅔'      => '2/3',
			'⅕'      => '1/5',
			'⅖'      => '2/5',
			'⅗'      => '3/5',
			'⅘'      => '4/5',
			'⅙'      => '1/6',
			'⅚'      => '5/6',
			'⅛'      => '1/8',
			'⅜'      => '3/8',
			'⅝'      => '5/8',
			'⅞'      => '7/8',
		);

		return $charmap;
	}
}

if ( ! function_exists( 'yummy_is_fraction_symbol' ) ) {
	/**
	 * Checks if a character is a fraction symbol.
	 *
	 * @param string $character Character.
	 *
	 * @return boolean
	 */
	function yummy_is_fraction_symbol( $character ) {
		$fractions = yummy_get_fractions();

		foreach ( $fractions as $key => $values ) {
			if ( $character === $key ) {
				return true;
			}
		}

		return false;
	}
}
