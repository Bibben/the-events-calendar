<?php


class Tribe__Events__Plugin_Register extends Tribe__Abstract_Plugin_Register {

	protected $main_class         = 'Tribe__Events__Main';
	protected $dependencies = array(
		'addon-dependencies' => array(
			'Tribe__Events__Pro__Main'       => '4.5.6',
			'Tribe__Events__Community__Main' => '4.4.14',
		),
	);

	public function __construct() {
		$this->base_dir = TRIBE_EVENTS_FILE;
		$this->version  = Tribe__Events__Main::VERSION;

		$this->register_plugin();
	}
}