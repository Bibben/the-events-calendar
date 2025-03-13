<?php declare(strict_types=1);

namespace TEC\Events\Calendar_Embeds;

use TEC\Common\Tests\Provider\Controller_Test_Case;
use tad\Codeception\SnapshotAssertions\SnapshotAssertions;
use Tribe\Events\Test\Traits\ECE_Maker;
use Tribe__Events__Main as TEC;

class Calendar_Embeds_Test extends Controller_Test_Case {

	use ECE_Maker;
	use SnapshotAssertions;

	protected string $controller_class = Calendar_Embeds::class;

	/**
	 * @test
	 */
	public function it_should_register_post_type(): void {
		global $wp_post_types;

		$this->assertArrayHasKey( Calendar_Embeds::POSTTYPE, $wp_post_types );
		$this->assertEquals( 'Calendar Embeds', $wp_post_types[ Calendar_Embeds::POSTTYPE ]->labels->name );
		$this->assertFalse( $wp_post_types[ Calendar_Embeds::POSTTYPE ]->public );
		$this->assertTrue( $wp_post_types[ Calendar_Embeds::POSTTYPE ]->publicly_queryable );
		$this->assertTrue( $wp_post_types[ Calendar_Embeds::POSTTYPE ]->show_ui );
	}

	/**
	 * @test
	 */
	public function it_should_modify_term_count_on_term_list_pages_only(): void {
		$this->make_controller()->register();
		$args       = [
			'start_date' => '2058-01-01 09:00:00',
			'end_date'   => '2058-01-01 11:00:00',
			'timezone'   => 'Europe/Paris',
			'title'      => 'A test event',
		];
		$post = tribe_events()->set_args( $args )->create()->ID;
		wp_update_post( [ 'ID' => $post, 'post_status' => 'publish' ] );

		wp_set_post_tags( $post, [ 'tag1', 'tag2' ] );

		$term_ids = [];
		$term_ids[] = $this->factory->term->create( [ 'slug' => 'cat1', 'taxonomy' => TEC::TAXONOMY ] );
		$term_ids[] = $this->factory->term->create( [ 'slug' => 'cat2', 'taxonomy' => TEC::TAXONOMY ] );

		wp_set_post_terms( $post, $term_ids, TEC::TAXONOMY, true );

		$this->assertEquals( [ 'tag1', 'tag2' ], wp_list_pluck( get_the_terms( $post, 'post_tag' ), 'slug' ) );
		$this->assertEquals( [ 'cat1', 'cat2' ], wp_list_pluck( get_the_terms( $post, TEC::TAXONOMY ), 'slug' ) );

		$terms = get_terms( [ 'taxonomy' => 'post_tag', 'hide_empty' => false ] );
		$cats  = get_terms( [ 'taxonomy' => TEC::TAXONOMY, 'hide_empty' => false ] );

		$this->assertCount( 2, $terms );
		$this->assertCount( 2, $cats );

		foreach ( $terms as $term ) {
			$this->assertEquals( 1, $term->count );
		}

		foreach ( $cats as $cat ) {
			$this->assertEquals( 1, $cat->count );
		}

		$ece_id = $this->create_ece();
		$this->add_tags_to_ece( $ece_id, [ 'tag1' ] );
		$this->add_categories_to_ece( $ece_id, [ 'cat1' ] );

		$terms = get_terms( 'post_tag' );
		$cats  = get_terms( TEC::TAXONOMY );

		$this->assertCount( 2, $terms );
		$this->assertCount( 2, $cats );

		$this->assertEquals( 2, $terms['0']->count );
		$this->assertEquals( 1, $terms['1']->count );
		$this->assertEquals( 2, $cats['0']->count );
		$this->assertEquals( 1, $cats['1']->count );

		set_current_screen( 'edit-post_tag' );

		$terms = get_terms( 'post_tag' );
		$cats  = get_terms( TEC::TAXONOMY );

		$this->assertCount( 2, $terms );
		$this->assertCount( 2, $cats );

		foreach ( $terms as $term ) {
			$this->assertEquals( 1, $term->count );
		}
		foreach ( $cats as $cat ) {
			$this->assertEquals( 1, $cat->count );
		}
	}

	/**
	 * @test
	 */
	public function it_should_disable_slug_changes(): void {
		$this->make_controller()->register();

		$ece_id = $this->create_ece( [ 'post_name' => 'my-slug' ] );

		$ece = get_post( $ece_id );

		$this->assertNotEquals( 'my-slug', $ece->post_name );

		$slug = $ece->post_name;

		wp_update_post( [ 'ID' => $ece_id, 'post_name' => 'new-slug' ] );

		$ece = get_post( $ece_id );

		$this->assertEquals( $slug, $ece->post_name );
	}

	/**
	 * @test
	 */
	public function it_should_retrieve_terms(): void {
		$ece_id = $this->create_ece();

		$tags       = Calendar_Embeds::get_tags( $ece_id );
		$categories = Calendar_Embeds::get_event_categories( $ece_id );

		$this->assertEquals( [], $tags );
		$this->assertEquals( [], $categories );

		$this->add_tags_to_ece( $ece_id, [ 'tag1', 'tag2' ] );
		$this->add_categories_to_ece( $ece_id, [ 'cat1', 'cat2' ] );

		$tags       = Calendar_Embeds::get_tags( $ece_id );
		$categories = Calendar_Embeds::get_event_categories( $ece_id );

		$this->assertEquals( [ 'tag1', 'tag2' ], wp_list_pluck( $tags, 'slug' ) );
		$this->assertEquals( [ 'cat1', 'cat2' ], wp_list_pluck( $categories, 'slug' ) );
	}

	/**
	 * @test
	 */
	public function it_should_retrieve_iframes_markup(): void {
		$ece_id = $this->create_ece( [ 'post_title' => 'ECE' ] );

		$markup = Calendar_Embeds::get_iframe( $ece_id );

		$this->assertMatchesHtmlSnapshot( str_replace( (string) $ece_id, '{ECE_ID}', $markup ) );
	}

	/**
	 * @test
	 */
	public function it_should_retrieve_iframes_markup_not_published(): void {
		$ece_id = $this->create_ece( [ 'post_title' => 'ECE' ] );

		wp_update_post( [ 'ID' => $ece_id, 'post_status' => 'draft' ] );

		$markup = Calendar_Embeds::get_iframe( $ece_id );

		$this->assertMatchesHtmlSnapshot( str_replace( (string) $ece_id, '{ECE_ID}', $markup ) );
	}
}
