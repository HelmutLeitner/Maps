<?php

namespace Maps\Test;

use ValueParsers\Result;
use Maps\Circle;
use DataValues\GeoCoordinateValue;

/**
 * Unit tests for the Maps\CircleParser class.
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
 * @ingroup MapsTest
 *
 * @group ValueParsers
 * @group Maps
 * @group CircleParserTest
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CircleParserTest extends ElementParserTest {

	/**
	 * @see ValueParserTestBase::parseProvider
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	public function parseProvider() {
		$argLists = array();

		$valid = array();

		$valid[] = array(
			array( 49.83798245308486, 2.724609375 ),
			'0'
		);

		$valid[] = array(
			array( 52.05249047600102, 8.26171875 ),
			'42'
		);

		$valid[] = array(
			array( -52.05249047600102, -8.26171875 ),
			'9001'
		);

		$valid[] = array(
			array( 52.05249047600102, -8.26171875 ),
			'326844.605'
		);

		foreach ( $valid as $values ) {
			$input = implode( ',', $values[0] ) . ':' . $values[1];
			$output = new Circle(
				new \DataValues\GeoCoordinateValue( $values[0][0], $values[0][1] ),
				$values[1]
			);

			$argLists[] = array( $input, Result::newSuccess( $output ) );
		}

		return array_merge( $argLists, parent::parseProvider() );
	}

	/**
	 * @see ValueParserTestBase::getParserClass
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	protected function getParserClass() {
		return 'Maps\CircleParser';
	}

}
