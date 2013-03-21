<?php

namespace Maps;

use ValueParsers\Result;
use DataValues\GeoCoordinateValue;

/**
 * ValueParser that parses the string representation of a circle.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @since 3.0
 *
 * @file
 * @ingroup Maps
 * @ingroup ValueParser
 *
 * @licence GNU GPL v2+
 * @author Kim Eik
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CircleParser extends ElementParser {

	/**
	 * @see StringValueParser::stringParse
	 *
	 * @since 3.0
	 *
	 * @param string $value
	 *
	 * @return Result
	 */
	public function stringParse( $value ) {
		$parts = explode( $this->metaDataSeparator , $value );

		$firstArg = explode( ':', array_shift( $parts ) );

		if ( count( $firstArg ) !== 2 ) {
			return $this->newErrorResult( 'Need both a circle centre and radius' );
		}

		$coordinate = $this->parseCoordinates( array( $firstArg[0] ) );
		$coordinate = $coordinate[0];

		// TODO: validate that radius is float

		$circle = new Circle(
			$coordinate,
			(float)$firstArg[1]
		);

		$this->handleCommonParams( $parts, $circle );

		return Result::newSuccess( $circle );
	}

}
