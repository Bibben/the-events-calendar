<?php
/**
 * The main service provider for the version 2 of the Views.
 *
 * @package TEC\Events\Menus
 * @since   TBD
 */

namespace TEC\Events\Menus;

/**
 * Class Service_Provider
 *
 * @since   TBD
 *
 * @package TEC\Events\Menus
 */
class Service_Provider extends \tad_DI52_ServiceProvider {
	public $menus = [
		'TEC_Menu',
		//'Home',
		//'Troubleshooting',
		'Venue',
		'Organizer',
	];

	public function register() {
		$this->register_menus();
		$this->build_menus();
	}

	public function register_menus() {
		foreach ( $this->menus as $menu ) {
			$menu_class = $this->get_class_object_from_name( $menu );
			tribe_singleton( $menu_class::class, $menu_class::class );
		}
	}

	public function build_menus() {
		foreach ( $this->menus as $menu ) {
			$menu_class = $this->get_class_object_from_name( $menu );
			tribe( $menu_class::class )->build();
		}
	}

	private function get_class_object_from_name( $name ) {
		$name = __NAMESPACE__ . '\\' . $name;
		return new $name;
	}

}