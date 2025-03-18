<?php
/**
 * Manages the External Calendar Embeds Feature.
 *
 * @since TBD
 *
 * @package TEC\Events\Calendar_Embeds
 */

namespace TEC\Events\Calendar_Embeds;

use TEC\Common\Contracts\Container;
use TEC\Common\Contracts\Provider\Controller as Controller_Contract;
use Tribe\Events\Views\V2\Assets as Event_Assets;
use TEC\Common\StellarWP\Assets\Asset;

/**
 * Class Controller
 *
 * @since TBD

 * @package TEC\Events\Calendar_Embeds
 */
class Frontend extends Controller_Contract {

	/**
	 * The template.
	 *
	 * @since TBD
	 *
	 * @var Template
	 */
	private Template $template;

	/**
	 * Page constructor.
	 *
	 * @since TBD
	 *
	 * @param Container $container  The container.
	 * @param Template  $template   The template.
	 */
	public function __construct( Container $container, Template $template ) {
		parent::__construct( $container );

		$this->template = $template;
	}

	/**
	 * Registers the filters and actions hooks added by the controller.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	public function do_register(): void {
		$this->register_assets();
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_filter( 'embed_template', [ $this, 'overwrite_embed_template' ] );
		add_filter( 'the_content', [ $this, 'overwrite_content' ] );
	}

	/**
	 * Removes the filters and actions hooks added by the controller.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	public function unregister(): void {
		remove_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		remove_filter( 'embed_template', [ $this, 'overwrite_embed_template' ] );
		remove_filter( 'the_content', [ $this, 'overwrite_content' ] );
	}

	/**
	 * Enqueues the scripts and styles for the calendar embeds.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	public function enqueue_scripts(): void {
		if ( ! is_singular( Calendar_Embeds::POSTTYPE ) ) {
			return;
		}

		tribe_asset_enqueue_group( Event_Assets::$group_key );

		/**
		 * Fires when the calendar embeds scripts are enqueued.
		 *
		 * Applicable to frontend and only singular screen.
		 *
		 * @since TBD
		 */
		do_action( 'tec_events_calendar_embeds_enqueue_scripts' );
	}

	/**
	 * Overwrites the content of the calendar embeds.
	 *
	 * @since TBD
	 *
	 * @param string $content The content.
	 *
	 * @return string
	 */
	public function overwrite_content( string $content ): string {
		if ( ! is_singular( Calendar_Embeds::POSTTYPE ) ) {
			return $content;
		}

		$calendar_embed_id = get_the_ID();

		return $this->template->template(
			'content',
			[
				'calendar_embed_id' => $calendar_embed_id,
				'event_categories'  => Calendar_Embeds::get_event_categories( $calendar_embed_id ),
				'event_tags'        => Calendar_Embeds::get_tags( $calendar_embed_id ),
			],
			false
		);
	}

	/**
	 * Overwrites the embed template for the calendar embeds.
	 *
	 * @since TBD
	 *
	 * @param string $template The template.
	 *
	 * @return string
	 */
	public function overwrite_embed_template( string $template ): string {
		if ( ! is_embed() ) {
			return $template;
		}

		if ( ! is_singular( Calendar_Embeds::POSTTYPE ) ) {
			return $template;
		}

		return $this->template->get_template_file( 'embed' );
	}

	/**
	 * Register assets for the Calendar Embeds singular Frontend page.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	protected function register_assets(): void {
		Asset::add(
			'tec-events-calendar-embeds-frontend-script',
			'js/calendar-embeds/page.js'
		)
			->add_to_group_path( 'tec-events-resources' )
			->enqueue_on( 'tec_events_calendar_embeds_enqueue_scripts' )
			->set_dependencies( 'jquery', 'tribe-events-views-v2-manager' )
			->in_footer()
			->register();

		Asset::add(
			'tec-events-calendar-embeds-frontend-style',
			'css/calendar-embeds/page.css'
		)
			->add_to_group_path( 'tec-events-resources' )
			->enqueue_on( 'tec_events_calendar_embeds_enqueue_scripts' )
			->register();
	}
}
