<?php

namespace Maps;

use Maps\Location;
use DataValues\GeoCoordinateValue;
use InvalidArgumentException;

/**
 * Class representing a circle.
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
 * @ingroup Maps
 *
 * @licence GNU GPL v2+
 * @author Kim Eik < kim@heldig.org >
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Circle extends \MapsBaseFillableElement {

	/**
	 * The center of the circle.
	 *
	 * @since 3.0
	 *
	 * @var GeoCoordinateValue
	 */
	protected $circleCentre;

	/**
	 * The circles radius in metres.
	 *
	 * @since 3.0
	 *
	 * @var float|int
	 */
	protected $circleRadius;

	/**
	 * Constructor.
	 *
	 * @since 3.0
	 *
	 * @param GeoCoordinateValue $circleCentre
	 * @param float|int $circleRadius
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( GeoCoordinateValue $circleCentre , $circleRadius ) {
		parent::__construct();

		$this->setCircleCentre( $circleCentre );

		if ( !is_int( $circleRadius ) && !is_float( $circleRadius ) ) {
			throw new InvalidArgumentException( 'The $circleRadius needs to be a float or int' );
		}

		$this->setCircleRadius( $circleRadius );
	}

	/**
	 * Returns the centre of the circle.
	 *
	 * @since 3.0
	 *
	 * @return GeoCoordinateValue
	 */
	public function getCircleCentre() {
		return $this->circleCentre;
	}

	/**
	 * Sets the centre of the circle.
	 *
	 * @since 3.0
	 *
	 * @param GeoCoordinateValue $circleCentre
	 */
	public function setCircleCentre( GeoCoordinateValue $circleCentre ) {
		$this->circleCentre = $circleCentre;
	}

	/**
	 * Returns the circles radius in metres.
	 *
	 * @since 3.0
	 *
	 * @return float|int
	 */
	public function getCircleRadius() {
		return $this->circleRadius;
	}

	/**
	 * Sets the circles radius in metres.
	 *
	 * @since 3.0
	 *
	 * @param float|int $circleRadius
	 */
	public function setCircleRadius( $circleRadius ) {
		$this->circleRadius = $circleRadius;
	}

	/**
	 * @since 3.0
	 *
	 * @param string $defText
	 * @param string $defTitle
	 *
	 * @return array
	 */
	public function getJSONObject( $defText = '' , $defTitle = '' ) {
		$parentArray = parent::getJSONObject( $defText , $defTitle );

		$array = array(
			'centre' => array(
				'lon' => $this->getCircleCentre()->getLongitude(),
				'lat' => $this->getCircleCentre()->getLatitude()
			) ,
			'radius' => intval( $this->getCircleRadius() ), // FIXME: retain precision
		);

		return array_merge( $parentArray, $array );
	}

}
