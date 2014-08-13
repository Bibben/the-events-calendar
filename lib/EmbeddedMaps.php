<?php
defined( 'ABSPATH' ) or exit( '-1' );

/**
 * Adds support for direct manipulation of venue co-ordinates via the venue editor.
 */
class TribeEventsPro_EmbeddedMaps {
	/**
	 * Script handle for the embedded maps script.
	 */
	const MAP_HANDLE = 'tribe_events_pro_embedded_map';

	/**
	 * Venue latitude, if known.
	 *
	 * @var string
	 */
	protected $lat;

	/**
	 * Venue longitude, if known.
	 *
	 * @var string
	 */
	protected $lng;

	/**
	 * Give each set of long/lat coordinates an index, potentially allowing for
	 * multiple embedded maps per page.
	 *
	 * @var array
	 */
	protected $embedded_maps = array();

	/**
	 * Used to track if the embedded maps script (and Google Maps API) have been
	 * enqueued.
	 *
	 * @var bool
	 */
	protected $map_script_enqueued = false;



	/**
	 * Sets things up to replace the embedded maps generated by the core plugin with ones
	 * which use stored coordinates (longitude/latitude) for positioning rather than the
	 * street address.
	 *
	 * If this is undesirable it can be turned off using a filter hook:
	 *
	 *     add_filter( 'tribe_events_pro_replace_embedded_maps', '__return_false' );
	 *embedded
	 */
	public function __construct() {
		if ( apply_filters( 'tribe_events_pro_replace_embedded_maps', true ) ) {
			add_filter( 'tribe_get_embedded_map', array( $this, 'single_post_map' ) );
		}
	}

	/**
	 * Replaces the embedded map on single post/venues with one that uses lat/long rather
	 * than the street address.
	 *
	 * @param $map
	 * @return mixed
	 */
	public function single_post_map( $map ) {
		$post_id = get_the_ID();

		// If it's neither a venue nor an event, bail
		if ( ! ( tribe_is_venue( $post_id ) || tribe_is_event( $post_id ) ) ) {
			return $map;
		}

		// Try to load the coordinates - it's possible none will be set
		$venue_id = tribe_get_venue_id( $post_id );
		$this->lat = get_post_meta( $venue_id, TribeEventsGeoLoc::LAT, true );
		$this->lng = get_post_meta( $venue_id, TribeEventsGeoLoc::LNG, true );

		// No valid coordinates? Bail out - the core-generated, address-based map can be used instead
		if ( ! is_numeric( $this->lat ) || $this->lat < -90 || $this->lat > 90 ) {
			return $map;
		}
		elseif ( ! is_numeric( $this->lng ) || $this->lng < -180 || $this->lng > 180 ) {
			return $map;
		}

		// Add coordinate-based map
		return $this->create_map();
	}

	/**
	 * Adds embedded map markup and sets up supporting scripts/script data.
	 *
	 * @return string
	 */
	protected function create_map() {
		$this->embedded_maps[] = array( $this->lat, $this->lng );

		end( $this->embedded_maps );
		$index = key( $this->embedded_maps );

		ob_start();
		tribe_get_template_part( 'pro/map/embedded', null, array( 'index' => $index ) );
		$html = ob_get_clean();

		$this->setup_script();
		return $html;
	}

	protected function setup_script() {
		if ( ! $this->map_script_enqueued ) {
			$this->enqueue_map_scripts();
		}

		wp_localize_script( self::MAP_HANDLE, 'tribeEventsProSingleMap', array(
			'markers' => $this->embedded_maps,
			'zoom' => apply_filters( 'tribe_events_pro_single_map_zoom_level', (int) tribe_get_option( 'embedGoogleMapsZoom', 8 ) )
		));
	}

	/**
	 * Sets up Event Calendar PRO's map handling script in the footer.
	 * Enqueuing of the Google Maps API script is handled within core.
	 */
	protected function enqueue_map_scripts() {
		$resources_url = trailingslashit( TribeEventsPro::instance()->pluginUrl ) . 'resources/';
		$url = Tribe_Template_Factory::getMinFile( $resources_url . 'embedded-map.js', true );
		wp_enqueue_script( self::MAP_HANDLE, $url, array( 'jquery' ), false, true );

		$this->map_script_enqueued = true;
	}
}