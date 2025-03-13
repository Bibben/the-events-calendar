<?php
/**
 * Calendar Embeds Admin Singular Page.
 *
 * @since TBD
 *
 * @package TEC\Events\Calendar_Embeds\Admin
 */

namespace TEC\Events\Calendar_Embeds\Admin;

use TEC\Common\Contracts\Container;
use TEC\Common\Contracts\Provider\Controller as Controller_Contract;
use TEC\Common\StellarWP\Assets\Asset;
use TEC\Events\Calendar_Embeds\Calendar_Embeds;
use TEC\Events\Calendar_Embeds\Template;
use Tribe__Events__Main as TEC;
use WP_Post;

/**
 * Class Singular_Page
 *
 * @since TBD
 *
 * @package TEC\Events\Calendar_Embeds\Admin
 */
class Singular_Page extends Controller_Contract {
	use Restore_Menu_Trait;

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
		add_filter( 'submenu_file', [ $this, 'keep_parent_menu_open' ], 5 );
		add_action( 'adminmenu', [ $this, 'restore_menu_globals' ] );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ], 10, 2 );
		add_filter( 'tec_events_calendar_embeds_iframe', [ $this, 'replace_iframe_markup' ], 10, 2 );
		add_action( 'post_submitbox_minor_actions', [ $this, 'add_copy_embed_button' ] );
	}

	/**
	 * Removes the filters and actions hooks added by the controller.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	public function unregister(): void {
		remove_filter( 'submenu_file', [ $this, 'keep_parent_menu_open' ], 5 );
		remove_action( 'adminmenu', [ $this, 'restore_menu_globals' ] );
		remove_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		remove_filter( 'tec_events_calendar_embeds_iframe', [ $this, 'replace_iframe_markup' ] );
		remove_action( 'post_submitbox_minor_actions', [ $this, 'add_copy_embed_button' ] );
	}

	/**
	 * Adds the copy embed button to the post submitbox.
	 *
	 * @since TBD
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return void
	 */
	public function add_copy_embed_button( WP_Post $post ): void {
		$this->template->template(
			'copy-embed-button-in-metabox',
			[
				'post_id' => $post->ID,
			]
		);
	}

	/**
	 * Replaces the iframe markup with a placeholder if the embed is not saved.
	 *
	 * @since TBD
	 *
	 * @param string  $iframe The iframe markup.
	 * @param WP_Post $embed  The embed post object.
	 *
	 * @return string
	 */
	public function replace_iframe_markup( string $iframe, WP_Post $embed ): string {
		if ( ! self::is_on_page() ) {
			return $iframe;
		}

		if ( 'auto-draft' !== $embed->post_status ) {
			return $iframe;
		}

		return '<p><strong>' . esc_html__( 'Please save the embed to see the preview.', 'the-events-calendar' ) . '</strong></p>';
	}

	/**
	 * Adds the metaboxes to the order post type.
	 *
	 * @since TBD
	 *
	 * @param string  $post_type The post type.
	 * @param WP_Post $post The post object.
	 *
	 * @return void
	 */
	public function add_meta_boxes( $post_type, $post ): void {
		if ( Calendar_Embeds::POSTTYPE !== $post_type ) {
			return;
		}

		add_meta_box(
			'tec-events-calendar-embeds-preview',
			__( 'Embed Preview', 'the-events-calendar' ),
			[ $this, 'render_embed_preview' ],
			$post_type,
			'normal',
			'high'
		);

		// Removes not editable slug metabox to avoid confusion.
		remove_meta_box( 'slugdiv', $post_type, 'normal' );
	}

	/**
	 * Renders the preview of the embed metabox.
	 *
	 * @since TBD
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return void
	 */
	public function render_embed_preview( WP_Post $post ): void {
		// phpcs:ignore StellarWP.XSS.EscapeOutput.OutputNotEscaped, WordPress.Security.EscapeOutput.OutputNotEscaped
		echo Calendar_Embeds::get_iframe( $post->ID );
	}

	/**
	 * Gets the URL for a Calendar Embed.
	 *
	 * @since TBD
	 *
	 * @param int $id The embed id.
	 *
	 * @return string
	 */
	public function get_url( int $id ): string {
		return add_query_arg(
			[
				'post'   => $id,
				'action' => 'edit',
			],
			admin_url( 'post.php' )
		);
	}

	/**
	 * Keep parent menu open when adding and editing calendar embeds.
	 *
	 * @since TBD
	 *
	 * @param string $submenu_file The current submenu file.
	 *
	 * @return ?string
	 */
	public function keep_parent_menu_open( ?string $submenu_file ): ?string {
		global $parent_file;

		if ( 'edit.php?post_type=' . Calendar_Embeds::POSTTYPE !== $parent_file ) {
			return $submenu_file;
		}

		self::$stored_globals = [
			'parent_file'  => $parent_file,
			'submenu_file' => $submenu_file,
		];

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$parent_file = 'edit.php?post_type=' . TEC::POSTTYPE;

		return 'edit.php?post_type=' . Calendar_Embeds::POSTTYPE;
	}

	/**
	 * Check if the current screen is the Calendar Embeds page.
	 *
	 * @since TBD
	 *
	 * @return bool
	 */
	public static function is_on_page(): bool {
		global $pagenow, $post_type;

		return Calendar_Embeds::POSTTYPE === $post_type && ( 'post-new.php' === $pagenow || 'post.php' === $pagenow );
	}
}
