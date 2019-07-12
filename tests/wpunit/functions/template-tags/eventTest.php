<?php

namespace TEC\Test\functions\template_tags;

use Codeception\TestCase\WPTestCase;
use PHPUnit\Framework\AssertionFailedError;
use Tribe\Events\Test\Factories\Event;
use Tribe\Events\Test\Factories\Organizer;
use Tribe\Events\Test\Factories\Venue;
use Tribe__Events__Timezones as Timezones;

class generalTest extends WPTestCase {
	public function setUp() {
		parent::setUp();
		static::factory()->event     = new Event();
		static::factory()->organizer = new Organizer();
		static::factory()->venue     = new Venue();
	}

	/**
	 * Test tribe_get_event returns null for non-existing event
	 */
	public
	function test_tribe_get_event_returns_null_for_non_existing_event() {
		// Sanity check: let's make sure this does not exist.
		$this->assertNull( get_post( 23 ) );

		$this->assertNull( tribe_get_event( 23 ) );
	}

	/**
	 * Test tribe_get_event allows filtering the post before any request is made
	 */
	public function test_tribe_get_event_allows_filtering_the_post_before_any_request_is_made() {
		$event = static::factory()->event->create_and_get();

		$count = $this->queries()->countQueries();

		// Delete the cache to make sure a new fetch would be triggered by `get_post` calls.
		wp_cache_delete( $event->ID, 'posts' );

		add_filter( 'tribe_get_event_before', static function () use ( $event ) {
			return $event;
		} );

		// Pass the ID to force a `get_post` call if not filtered.
		tribe_get_event( $event->ID );

		$this->assertEquals( $count, $this->queries()->countQueries() );
	}

	/**
	 * Test tribe_get_event returns a WP_Post object
	 */
	public function test_tribe_get_event_returns_a_wp_post_object() {
		$event = static::factory()->event->create_and_get();

		$result = tribe_get_event( $event );

		$this->assertInstanceOf( \WP_Post::class, $result );
	}

	/**
	 * Test tribe_get_event attaches a default set of properties to the post
	 */
	public function test_tribe_get_event_attaches_a_default_set_of_properties_to_the_post() {
		$event_id = static::factory()->event->create();

		$event = tribe_get_event( $event_id );

		$expected = [
			'start_date'     => get_post_meta( $event_id, '_EventStartDate', true ),
			'start_date_utc' => get_post_meta( $event_id, '_EventStartDateUTC', true ),
			'end_date'       => get_post_meta( $event_id, '_EventEndDate', true ),
			'end_date_utc'   => get_post_meta( $event_id, '_EventEndDateUTC', true ),
			'timezone'       => Timezones::get_event_timezone_string( $event_id ),
			'duration'       => get_post_meta( $event_id, '_EventDuration', true ),
			'all_day'        => false,
		];

		foreach ( $expected as $key => $value ) {
			$isset_message = "Property '{$key}'' is not set on the event object.";
			$this->assertTrue( isset( $event->{$key} ), $isset_message );
			$value_message = "Property '{$key}' has wrong value.";
			$this->assertEquals( $value, $event->{$key}, $value_message );
		}
	}

	/**
	 * Test tribe_get_event multiday property
	 */
	public function test_tribe_get_event_multiday_property() {
		$non_multiday_event        = static::factory()->event->create();
		$three_days_multiday_event = static::factory()->event->create( [
			'when'     => '2018-01-01 09:00:00',
			'duration' => 2 * DAY_IN_SECONDS
		] );
		$six_days_multiday_event   = static::factory()->event->create( [
			'when'     => '2018-01-01 09:00:00',
			'duration' => 5 * DAY_IN_SECONDS
		] );

		$this->assertFalse( false, tribe_get_event( $non_multiday_event )->multiday );
		$this->assertEquals( 3, tribe_get_event( $three_days_multiday_event )->multiday );
		$this->assertEquals( 6, tribe_get_event( $six_days_multiday_event )->multiday );
	}

	/**
	 * Test tribe_get_event multiday prop w diff. cutoff
	 */
	public function test_tribe_get_event_multiday_prop_w_diff_cutoff() {
		tribe_update_option( 'multiDayCutoff', '02:00' );

		$to_11_pm    = static::factory()->event->create( [
			'when'     => '2019-01-01 22:00:00',
			'duration' => 1 * HOUR_IN_SECONDS
		] );
		$to_midnight = static::factory()->event->create( [
			'when'     => '2019-01-01 22:00:00',
			'duration' => 2 * HOUR_IN_SECONDS
		] );
		$to_1_am     = static::factory()->event->create( [
			'when'     => '2019-01-01 22:00:00',
			'duration' => 3 * HOUR_IN_SECONDS
		] );
		$to_2_am     = static::factory()->event->create( [
			'when'     => '2019-01-01 22:00:00',
			'duration' => 4 * HOUR_IN_SECONDS
		] );
		$to_6_am     = static::factory()->event->create( [
			'when'     => '2019-01-01 22:00:00',
			'duration' => 8 * HOUR_IN_SECONDS
		] );
		// Three days: [22, 02], [02, 02], [02,04].
		$to_4_am_two_days_after = static::factory()->event->create( [
			'when'     => '2019-01-01 22:00:00',
			'duration' => DAY_IN_SECONDS + 6 * HOUR_IN_SECONDS
		] );

		foreach (
			[
				$to_11_pm,
				$to_midnight,
				$to_1_am,
				$to_2_am,
			] as $id
		) {
			$this->assertFalse( false, tribe_get_event( $id )->multiday );
		}
		$this->assertEquals( 2, tribe_get_event( $to_6_am )->multiday );
		$this->assertEquals( 3, tribe_get_event( $to_4_am_two_days_after )->multiday );

		tribe_update_option( 'multiDayCutoff', '01:00' );

		foreach (
			[
				$to_11_pm,
				$to_midnight,
				$to_1_am,
			] as $id
		) {
			$this->assertFalse( false, tribe_get_event( $id )->multiday );
		}
		$this->assertEquals( 2, tribe_get_event( $to_2_am )->multiday );
		$this->assertEquals( 2, tribe_get_event( $to_6_am )->multiday );
		$this->assertEquals( 3, tribe_get_event( $to_4_am_two_days_after )->multiday );
	}


	/**
	 * Test tribe_get_event all_day property
	 */
	public function test_tribe_get_event_all_day_property() {
		$all_day = static::factory()->event->create( [ 'meta_input' => [ '_EventAllDay' => true ] ] );

		$got = tribe_get_event( $all_day )->all_day;

		$this->assertTrue( $got );
	}

	/**
	 * Test tribe_get_event all_day prop with diff. durations and cutoffs
	 */
	public function test_tribe_get_event_all_day_prop_with_diff_durations_and_cutoffs() {
		tribe_update_option( 'multiDayCutoff', '00:00' );

		$all_day_one_day = static::factory()->event->create( [
			'meta_input' => [ '_EventAllDay' => true ]
		] );
		$all_day_3_days  = static::factory()->event->create( [
			'duration'   => 3 * DAY_IN_SECONDS,
			'meta_input' => [ '_EventAllDay' => true ]
		] );

		$all_day_one_day_event = tribe_get_event( $all_day_one_day );
		$all_day_3_days_event  = tribe_get_event( $all_day_3_days );

		$this->assertTrue( $all_day_one_day_event->all_day );
		$this->assertTrue( $all_day_3_days_event->all_day );
		$this->assertFalse( $all_day_one_day_event->multiday );
		$this->assertEquals( 3, $all_day_3_days_event->multiday );

		tribe_update_option( 'multiDayCutoff', '02:00' );

		$all_day_one_day_event = tribe_get_event( $all_day_one_day );
		$all_day_3_days_event  = tribe_get_event( $all_day_3_days );

		$this->assertTrue( $all_day_one_day_event->all_day );
		$this->assertTrue( $all_day_3_days_event->all_day );
		$this->assertFalse( $all_day_one_day_event->multiday );
		$this->assertEquals( 3, $all_day_3_days_event->multiday );
	}

	/**
	 * Test tribe_get_event featured prop
	 */
	public function test_tribe_get_event_featured_prop() {
		$not_featured = static::factory()->event->create();
		$featured     = static::factory()->event->create( [
			'meta_input' => [
				'_tribe_featured' => true,
			],
		] );

		$this->assertFalse( tribe_get_event( $not_featured )->featured );
		$this->assertTrue( tribe_get_event( $featured )->featured );
	}

	/**
	 * Test tribe_get_event organizer lazy fetch
	 */
	public function test_tribe_get_event_organizer_lazy_fetch() {
		$organizer_1       = static::factory()->organizer->create();
		$organizer_2       = static::factory()->organizer->create();
		$wo_organizer      = static::factory()->event->create();
		$w_organizer_1     = static::factory()->event->create( [ 'organizers' => [ $organizer_1 ] ] );
		$w_both_organizers = static::factory()->event->create( [ 'organizers' => [ $organizer_1, $organizer_2 ] ] );

		$fail = static function () {
			$str = '`tribe_get_event` should not call `tribe_get_organizer` unless the `organizer` property is accessed.';
			throw new AssertionFailedError( $str );
		};

		add_filter( 'tribe_get_organizer', $fail );

		$wo_organizer_event      = tribe_get_event( $wo_organizer );
		$w_organizer_1_event     = tribe_get_event( $w_organizer_1 );
		$w_both_organizers_event = tribe_get_event( $w_both_organizers );

		remove_filter( 'tribe_get_organizer', $fail );

		$this->assertEquals( [], $wo_organizer_event->organizers->all() );
		$this->assertEquals( [ tribe_get_organizer( $organizer_1 ) ], tribe_get_event( $w_organizer_1 )->organizers->all() );
		$this->assertEquals( [
			tribe_get_organizer( $organizer_1 ),
			tribe_get_organizer( $organizer_2 )
		], tribe_get_event( $w_both_organizers )->organizers->all() );
	}

	/**
	 * Test tribe_get_event starts_this_week property
	 */
	public function test_tribe_get_event_starts_this_week_property() {
		$monday_start_of_week    = 1;
		$wednesday_start_of_week = 3;
		$saturday_start_of_week  = 6;
		$wednesday               = '2019-07-10 09:00:00';
		$friday                  = '2019-07-12';

		update_option( 'start_of_week', $monday_start_of_week );

		$event = static::factory()->event->create( [
			'when'     => $wednesday,
			'duration' => 3 * DAY_IN_SECONDS,
		] );

		$got = tribe_get_event( $event );

		$this->assertNull( $got->starts_this_week );
		$this->assertNull( $got->ends_this_week );

		$got = tribe_get_event( $event, OBJECT, $friday );

		$this->assertTrue( $got->starts_this_week );
		$this->assertTrue( $got->ends_this_week );

		update_option( 'start_of_week', $wednesday_start_of_week );

		$got = tribe_get_event( $event, OBJECT, $friday );

		$this->assertTrue( $got->starts_this_week );
		$this->assertTrue( $got->ends_this_week );

		update_option( 'start_of_week', $saturday_start_of_week );

		$got = tribe_get_event( $event, OBJECT, $friday );

		$this->assertTrue( $got->starts_this_week );
		$this->assertFalse( $got->ends_this_week );
	}
}
