<?php

namespace Maps;
use ValueParsers\StringValueParser;
use ValueParsers\Result;
use DataValues\GeoCoordinateValue;

/**
 * ValueParser that parses the string representation of a line.
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
class LineParser extends ElementParser {

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

		$line = new Line( $this->parseCoordinates(
			explode( ':' , array_shift( $parts ) )
		) );

		$this->handleCommonParams( $parts, $line );

		return Result::newSuccess( $line );
	}

}
