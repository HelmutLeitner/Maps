<?php

namespace Maps;

use ValueParsers\StringValueParser;
use ValueParsers\Result;
use DataValues\GeoCoordinateValue;
use MapsBaseStrokableElement;

/**
 * ValueParser that parses the string representation of a map element.
 *
 * TODO: this is a temporary holder for legacy code. A better design
 * should be found.
 *
 * @deprecated
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
abstract class ElementParser extends StringValueParser {

	// TODO: use options
	protected $metaDataSeparator = '~';

	protected $supportGeocoding = true;

	/**
	 * @since 3.0
	 *
	 * @param string[] $coordinateStrings
	 *
	 * @return GeoCoordinateValue[]
	 */
	protected function parseCoordinates( array $coordinateStrings ) {
		$coordinates = array();
		$coordinateParser = new \ValueParsers\GeoCoordinateParser( new \ValueParsers\ParserOptions() );

		$supportsGeocoding = $this->supportGeocoding && \Maps\Geocoders::canGeocode();

		foreach ( $coordinateStrings as $coordinateString ) {
			if ( $supportsGeocoding ) {
				$coordinate = \Maps\Geocoders::attemptToGeocode( $coordinateString );

				if ( $coordinate === false ) {
					// TODO
				}
				else {
					$coordinates[] = $coordinate;
				}
			}
			else {
				$parseResult = $coordinateParser->parse( $coordinateString );

				if ( $parseResult->isValid() ) {
					$coordinates[] = $parseResult->getValue();
				}
				else {
					// TODO
				}
			}
		}

		return $coordinates;
	}

	/**
	 * This method requires that parameters are positionally correct,
	 * 1. Link (one parameter) or bubble data (two parameters)
	 * 2. Stroke data (three parameters)
	 * 3. Fill data (two parameters)
	 * e.g ...title~text~strokeColor~strokeOpacity~strokeWeight~fillColor~fillOpacity
	 *
	 * @since 3.0
	 *
	 * @param array $params
	 * @param MapsBaseStrokableElement &$element
	 */
	protected function handleCommonParams( array &$params, MapsBaseStrokableElement &$element ) {
		//Handle bubble and link parameters

		//create link data
		$linkOrTitle = array_shift( $params );
		if ( $link = $this->isLinkParameter( $linkOrTitle ) ) {
			$this->setLinkFromParameter( $element , $link );
		} else {
			//create bubble data
			$this->setBubbleDataFromParameter( $element , $params , $linkOrTitle );
		}


		//handle stroke parameters
		if ( $color = array_shift( $params ) ) {
			$element->setStrokeColor( $color );
		}

		if ( $opacity = array_shift( $params ) ) {
			$element->setStrokeOpacity( $opacity );
		}

		if ( $weight = array_shift( $params ) ) {
			$element->setStrokeWeight( $weight );
		}

		//handle fill parameters
		if ( $element instanceof \iFillableMapElement ) {
			if ( $fillColor = array_shift( $params ) ) {
				$element->setFillColor( $fillColor );
			}

			if ( $fillOpacity = array_shift( $params ) ) {
				$element->setFillOpacity( $fillOpacity );
			}
		}

		//handle hover parameter
		if ( $element instanceof \iHoverableMapElement ) {
			if ( $visibleOnHover = array_shift( $params ) ) {
				$element->setOnlyVisibleOnHover( filter_var( $visibleOnHover , FILTER_VALIDATE_BOOLEAN ) );
			}
		}
	}

	private function setBubbleDataFromParameter( Line &$line , &$params , $title ) {
		if ( $title ) {
			$line->setTitle( $title );
		}
		if ( $text = array_shift( $params ) ) {
			$line->setText( $text );
		}
	}

	private function setLinkFromParameter( Line &$line , $link ) {
		if ( filter_var( $link , FILTER_VALIDATE_URL , FILTER_FLAG_SCHEME_REQUIRED ) ) {
			$line->setLink( $link );
		} else {
			$title = \Title::newFromText( $link );
			$line->setLink( $title->getFullURL() );
		}
	}

	/**
	 * Checks if a string is prefixed with link:
	 * @static
	 * @param $link
	 * @return bool|string
	 * @since 2.0
	 */
	private function isLinkParameter( $link ) {
		if ( strpos( $link , 'link:' ) === 0 ) {
			return substr( $link , 5 );
		}

		return false;
	}

}
