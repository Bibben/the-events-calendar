<?php declare(strict_types=1);

namespace TEC\Events\Calendar_Embeds\Admin;

use TEC\Common\Tests\Provider\Controller_Test_Case;
use TEC\Events\Calendar_Embeds\Calendar_Embeds;
use Tribe\Tests\Traits\With_Uopz;
use Tribe__Events__Main as TEC;
use TEC\Common\StellarWP\Assets\Assets;
use Tribe\Events\Test\Traits\ECE_Maker;
use tad\Codeception\SnapshotAssertions\SnapshotAssertions;

class Singular_Page_Test extends Controller_Test_Case {
	use With_Uopz;
	use SnapshotAssertions;
	use ECE_Maker;
	use SnapshotAssertions;


	protected string $controller_class = Singular_Page::class;

	protected static array $backups = [];

	/**
	 * @before
	 */
	public function setup_admin_context() {
		global $current_screen, $submenu, $parent_file, $pagenow, $post_type;
		self::$backups = [
			'current_screen' => $current_screen,
			'submenu'        => $submenu,
			'parent_file'    => $parent_file,
			'pagenow'        => $pagenow,
			'post_type'      => $post_type,
		];

		$post_type = Calendar_Embeds::POSTTYPE;
		$pagenow   = 'post.php';
		set_current_screen( 'edit-' . Calendar_Embeds::POSTTYPE );
		wp_set_current_user( self::factory()->user->create( [ 'role' => 'administrator' ] ) );
	}

	/**
	 * @after
	 */
	public function restore_backup() {
		global $current_screen, $submenu, $parent_file, $pagenow, $post_type;
		$current_screen = self::$backups['current_screen'];
		$submenu        = self::$backups['submenu'];
		$parent_file    = self::$backups['parent_file'];
		$pagenow        = self::$backups['pagenow'];
		$post_type      = self::$backups['post_type'];
		set_current_screen( $current_screen );
		wp_set_current_user( 0 );
	}

	/**
	 * @test
	 */
	public function it_should_replace_iframe_markup_for_auto_drafts(): void {
		$this->make_controller()->register();

		$ece_id = $this->create_ece( [ 'post_status' => 'publish' ] );

		$iframe = Calendar_Embeds::get_iframe( $ece_id );

		wp_update_post( [ 'ID' => $ece_id, 'post_status' => 'auto-draft' ] );

		$iframe .= PHP_EOL . '{SNAPSHOT_DIVIDER}' . PHP_EOL . Calendar_Embeds::get_iframe( $ece_id );

		$this->assertMatchesHtmlSnapshot( $iframe );
	}

	/**
	 * @test
	 */
	public function it_should_render_embed_preview(): void {
		$ece_id = $this->create_ece( [ 'post_status' => 'publish' ] );

		ob_start();
		$this->make_controller()->render_embed_preview( get_post( $ece_id ) );

		$this->assertMatchesHtmlSnapshot( ob_get_clean() );
	}

	/**
	 * @test
	 */
	public function it_should_get_edit_url(): void {
		$controller = $this->make_controller();
		$this->assertEquals( home_url( 'wp-admin/post.php?post=12&action=edit' ), $controller->get_url( 12 ) );
	}

	/**
	 * @test
	 * @dataProvider asset_data_provider
	 */
	public function it_should_locate_assets_where_expected( $slug, $path ) {
		$this->make_controller()->register();

		$this->assertTrue( Assets::init()->exists( $slug ) );

		// We use false, because in CI mode the assets are not build so min aren't available. Its enough to check that the non-min is as expected.
		$asset_url = Assets::init()->get( $slug )->get_url( false );
		$this->assertEquals( plugins_url( $path, TRIBE_EVENTS_FILE ), $asset_url );
	}

	public function asset_data_provider() {
		$assets = [
			'tec-events-calendar-embeds-single-script' => 'build/Calendar_Embeds/admin/single.js',
			'tec-events-calendar-embeds-single-style'  => 'build/Calendar_Embeds/admin/single.css',
		];

		foreach ( $assets as $slug => $path ) {
			yield $slug => [ $slug, $path ];
		}
	}

	/**
	 * @test
	 */
	public function it_should_overwrite_parent_file() {
		global $parent_file, $submenu_file;

		$this->make_controller()->register();

		// Test when parent file is not the Calendar Embeds post type.
		$parent_file = 'edit.php?post_type=some_other_post_type';
		$submenu_file = 'some_submenu_file';
		$result = apply_filters( 'submenu_file', $submenu_file );
		$this->assertEquals( 'some_submenu_file', $result );
		$this->assertEquals( 'edit.php?post_type=some_other_post_type', $parent_file );

		// Test when parent file is the Calendar Embeds post type.
		$parent_file = 'edit.php?post_type=' . Calendar_Embeds::POSTTYPE;
		$submenu_file = 'some_submenu_file';
		$result = apply_filters( 'submenu_file', $submenu_file );
		$this->assertEquals( 'edit.php?post_type=' . Calendar_Embeds::POSTTYPE, $result );
		$this->assertEquals( 'edit.php?post_type=' . TEC::POSTTYPE, $parent_file );
	}

	/**
	 * @test
	 */
	public function it_should_return_whether_we_are_on_page() {
		$this->assertTrue( Singular_Page::is_on_page() );

		global $post_type, $pagenow;

		$post_type = 'some_other_post_type';
		$this->assertFalse( Singular_Page::is_on_page() );

		$post_type = Calendar_Embeds::POSTTYPE;
		$pagenow   = 'some_other_page.php';

		$this->assertFalse( Singular_Page::is_on_page() );
	}
}
