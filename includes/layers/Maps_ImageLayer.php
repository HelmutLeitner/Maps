<?php

/**
 * Class for describing image layers.
 *
 * @since 0.7.2
 * 
 * @file Maps_ImageLayer.php
 * @ingroup Maps
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Daniel Werner
 */
class MapsImageLayer extends MapsLayer {

	/**
	 * Registers the layer.
	 * 
	 * @since 0.7.2
	 */
	public static function register() {
		MapsLayerTypes::registerLayerType( 'image', __CLASS__, 'openlayers' );
		return true;
	}		
	
	/**
	 * @see MapsLayer::getParameterDefinitions
	 * 
	 * @since 0.7.2
	 * 
	 * @return array
	 */
	protected function getParameterDefinitions() {
		$params = parent::getParameterDefinitions();
		
		// map extent for extents bound object:		
		$params['topextent'] = new Parameter( 'topextent', Parameter::TYPE_FLOAT );
		$params['topextent']->addAliases( 'upperbound', 'topbound' );
		
		$params['rightextent'] = new Parameter( 'rightextent', Parameter::TYPE_FLOAT );
		$params['rightextent']->addAliases( 'rightbound' );
		
		$params['bottomextent'] = new Parameter( 'bottomextent', Parameter::TYPE_FLOAT );
		$params['bottomextent']->addAliases( 'lowerbound', 'bottombound' );

		$params['leftextent'] = new Parameter( 'leftextent', Parameter::TYPE_FLOAT );
		$params['leftextent']->addAliases( 'leftbound' );

		// image-source information:
		$params['source'] = new Parameter( 'source' );
		$params['source']->addCriteria( new CriterionIsImage() );
		$params['source']->addManipulations( new MapsParamFile() );
		
		$params['width' ] = new Parameter( 'width',  Parameter::TYPE_FLOAT );
		$params['height'] = new Parameter( 'height', Parameter::TYPE_FLOAT );

		return $params;
	}
	
	/**
	 * @see MapsLayer::getPropertyHtmlRepresentation
	 *
	 * @since dw1
	 *
	 * @return array
	 */
	protected function getPropertyHtmlRepresentation( $name, &$parser ) {
		$value = $this->properties[ $name ];

		switch( $name ) {
			case 'source':
				$value = $this->originalPropertyValues['source']; // get original, non-modified value

				$title = Title::newFromText( $value, NS_FILE );

				// if title has invalid characters or doesn't exist and has url-style
				if( $title === null
					|| ( !$title->exists() && preg_match( '|^.+\://.+\..+$|', $value ) )
				) {
					// url link:
					$value = $parser->recursiveTagParse( "[$value $value]" );
				} else {
					// wikilink (can be red link to non-existant file):
					$imgName = $title->getPrefixedText();
					$value = $parser->recursiveTagParse( "[[$imgName|thumb|[[:$imgName]]|left]]" );
				}
				return $value; // html already

			default:
				// if we don't have any special handling here, leave it to base class:
				return parent::getPropertyHtmlRepresentation( $name, $parser );
		}
		return htmlspecialchars( $value );;
	}

	/**
	 * @see MapsLayer::doPropertiesHtmlTransform
	 *
	 * @since dw1
	 *
	 * @return array
	 */
	protected function doPropertiesHtmlTransform( &$properties ) {
		parent::doPropertiesHtmlTransform( $properties );

		$sp = '&#x202F;'; // non-breaking thin space

		// image-size:
		$properties['image-size'] = "<b>width:</b> {$properties['width']}{$sp}pixel, <b>height:</b> {$properties['height']}{$sp}pixel";
		unset( $properties['width'], $properties['height'] );

		// extent:
		$unit = $properties['units'];
		$properties['extent'] =
			"<b>left:</b> {$properties['leftextent']}{$sp}$unit, " .
			"<b>bottom:</b> {$properties['bottomextent']}{$sp}$unit, " .
			"<b>right:</b> {$properties['rightextent']}{$sp}$unit, " .
			"<b>top:</b> {$properties['topextent']}{$sp}$unit";
		unset( $properties['leftextent'], $properties['bottomextent'], $properties['rightextent'], $properties['topextent'] );
	}

	/**
	 * @see MapsLayer::getJavaScriptDefinition
	 * 
	 * @since 0.7.2
	 * 
	 * @return string
	 */
	public function getJavaScriptDefinition() {
		$this->validate();

		// do image layer options:

		$options = array(
			'isImage' => true,
			'units' => $this->properties['units'],
		);

		if( $this->properties['zoomlevels'] !== false ) {
			$options['numZoomLevels'] = $this->properties['zoomlevels'];
		}
		if( $this->properties['minscale'] !== false ) {
			$options['minScale'] = $this->properties['minscale'];
		}
		if( $this->properties['maxscale'] !== false ) {
			$options['maxScale'] = $this->properties['maxscale'];
		}
		
		$options = Xml::encodeJsVar( (object)$options ); //js-encode all options


		// for non-option params, get JavaScript-encoded config values:
		foreach( $this->properties as $name => $value ) {
			${ $name } = MapsMapper::encodeJsVar( $value );
		}
		
		return <<<EOT
	new OpenLayers.Layer.Image(
		$label,
		$source,
		new OpenLayers.Bounds($leftextent, $bottomextent, $rightextent, $topextent),
		new OpenLayers.Size($width, $height),
		{$options}
	)
EOT;
	}
	
}
