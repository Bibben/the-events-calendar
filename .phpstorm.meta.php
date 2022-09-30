<?php

namespace PHPSTORM_META {
	$map = [
		''                                              => '@',
		'events.editor.meta'                            => \Tribe__Events__Editor__Meta::class,
		'events.editor.settings'                        => \Tribe__Events__Editor__Settings::class,
		'events.editor.i18n'                            => \Tribe__Events__Editor__I18n::class,
		'events.editor.template'                        => \Tribe__Events__Editor__Template::class,
		'events.editor.template.overwrite'              => \Tribe__Events__Editor__Template__Overwrite::class,
		'events.editor.configuration'                   => \Tribe__Events__Editor__Configuration::class,
		'events.editor.blocks.classic-event-details'    => \Tribe__Events__Editor__Blocks__Classic_Event_Details::class,
		'events.editor.blocks.event-datetime'           => \Tribe__Events__Editor__Blocks__Event_Datetime::class,
		'events.editor.blocks.event-venue'              => \Tribe__Events__Editor__Blocks__Event_Venue::class,
		'events.editor.blocks.event-organizer'          => \Tribe__Events__Editor__Blocks__Event_Organizer::class,
		'events.editor.blocks.event-links'              => \Tribe__Events__Editor__Blocks__Event_Links::class,
		'events.editor.blocks.event-price'              => \Tribe__Events__Editor__Blocks__Event_Price::class,
		'events.editor.blocks.event-category'           => \Tribe__Events__Editor__Blocks__Event_Category::class,
		'events.editor.blocks.event-tags'               => \Tribe__Events__Editor__Blocks__Event_Tags::class,
		'events.editor.blocks.event-website'            => \Tribe__Events__Editor__Blocks__Event_Website::class,
		'events.editor.blocks.featured-image'           => \Tribe__Events__Editor__Blocks__Featured_Image::class,
		'events-aggregator.record-items'                => \Tribe__Events__Aggregator__Record__Items::class,
		'events-aggregator.processes.import-events'     => \Tribe__Events__Aggregator__Processes__Import_Events::class,
		'events-aggregator.queue-control'               => \Tribe__Events__Aggregator__Processes__Queue_Control::class,
		'events.integrations.freemius'                  => \Tribe__Events__Integrations__Freemius::class,
		'tec.integrations.wpml.meta'                    => \Tribe__Events__Integrations__WPML__Meta::class,
		'events-aggregator.rest-api.v1.endpoints.batch' => \Tribe__Events__Aggregator__REST__V1__Endpoints__Batch::class,
		'events-aggregator.rest-api.v1.endpoints.state' => \Tribe__Events__Aggregator__REST__V1__Endpoints__State::class,
		'tec.main'                                      => \Tribe__Events__Main::class,
		'tec.i18n'                                      => \Tribe\Events\I18n::class,
		'tec.cost-utils'                                => \Tribe__Events__Cost_Utils::class,
		'tec.front-page-view'                           => \Tribe__Events__Front_Page_View::class,
		'tec.admin.event-meta-box'                      => \Tribe__Events__Admin__Event_Meta_Box::class,
		'tec.featured_events'                           => \Tribe__Events__Featured_Events::class,
		'tec.featured_events.query_helper'              => \Tribe__Events__Featured_Events__Query_Helper::class,
		'tec.featured_events.permalinks_helper'         => \Tribe__Events__Featured_Events__Permalinks_Helpe::class,
		'events-aggregator.main'                        => \Tribe__Events__Aggregator::class,
		'events-aggregator.service'                     => \Tribe__Events__Aggregator__Service::class,
		'events-aggregator.settings'                    => \Tribe__Events__Aggregator__Settings::class,
		'events-aggregator.records'                     => \Tribe__Events__Aggregator__Records::class,
		'tec.shortcodes.event-details'                  => \Tribe__Events__Shortcode__Event_Details::class,
		'tec.ignored-events'                            => \Tribe__Events__Ignored_Events::class,
		'tec.assets'                                    => \Tribe__Events__Assets::class,
		'tec.iCal'                                      => \Tribe__Events__iCal::class,
		'tec.rest-v1.main'                              => \Tribe__Events__REST__V1__Main::class,
		'tec.integrations.twenty-seventeen'             => \Tribe__Events__Integrations__Twenty_Seventeen::class,
		'tec.linked-posts'                              => \Tribe__Events__Linked_Posts::class,
		'tec.linked-posts.venue'                        => \Tribe__Events__Venue::class,
		'tec.linked-posts.organizer'                    => \Tribe__Events__Organizer::class,
		'tec.adjacent-events'                           => \Tribe__Events__Adjacent_Events::class,
		'tec.event-cleaner'                             => \Tribe__Events__Event_Cleaner::class,
		'tec.gutenberg'                                 => \Tribe__Events__Gutenberg::class,
		'tec.admin.notice.timezones'                    => \Tribe__Events__Admin__Notice__Timezones::class,
		'tec.admin.notice.marketing'                    => \Tribe__Events__Admin__Notice__Marketing::class,
		'tec.privacy'                                   => \Tribe__Events__Privacy::class,
		'events.rewrite'                                => \Tribe__Events__Rewrite::class,
		'tec.customizer.general-theme'                  => \Tribe__Events__Customizer__General_Theme::class,
		'tec.customizer.global-elements'                => \Tribe__Events__Customizer__Global_Elements::class,
		'tec.customizer.day-list-view'                  => \Tribe__Events__Customizer__Day_List_View::class,
		'tec.customizer.month-week-view'                => \Tribe__Events__Customizer__Month_Week_View::class,
		'tec.customizer.single-event'                   => \Tribe__Events__Customizer__Single_Event::class,
		'tec.customizer.widget'                         => \Tribe__Events__Customizer__Widget::class,
		'tec.rest-v1.messages'                          => \Tribe__Events__REST__V1__Messages::class,
		'tec.rest-v1.ea-messages'                       => \Tribe__Events__REST__V1__EA_Messages::class,
		'tec.rest-v1.headers-base'                      => \Tribe__Events__REST__V1__Headers__Base::class,
		'tec.rest-v1.settings'                          => \Tribe__Events__REST__V1__Settings::class,
		'tec.rest-v1.system'                            => \Tribe__Events__REST__V1__System::class,
		'tec.rest-v1.validator'                         => \Tribe__Events__REST__V1__Validator__Base::class,
		'tec.rest-v1.repository'                        => \Tribe__Events__REST__V1__Post_Repository::class,
		'tec.rest-v1.endpoints.single-venue'            => \Tribe__Events__REST__V1__Endpoints__Single_Venue::class,
		'tec.rest-v1.endpoints.single-organizer'        => \Tribe__Events__REST__V1__Endpoints__Single_Organizer::class,
		'tec.json-ld.event'                             => \Tribe__Events__JSON_LD__Event::class,
		'tec.json-ld.venue'                             => \Tribe__Events__JSON_LD__Venue::class,
		'tec.json-ld.organizer'                         => \Tribe__Events__JSON_LD__Organizer::class,
		'tec.rest-v1.headers'                           => \Tribe__REST__Headers__Headers_Interface::class,
		'tec.rest-v1.endpoints.documentation'           => \Tribe__Events__REST__V1__Endpoints__Swagger_Documentation::class,
		'tec.rest-v1.endpoints.archive-event'           => \Tribe__Events__REST__V1__Endpoints__Archive_Event::class,
		'tec.rest-v1.endpoints.single-event'            => \Tribe__Events__REST__V1__Endpoints__Single_Event::class,
		'tec.rest-v1.endpoints.single-event-slug'       => \Tribe__Events__REST__V1__Endpoints__Single_Event_Slug::class,
		'tec.rest-v1.endpoints.single-venue'            => \Tribe__Events__REST__V1__Endpoints__Single_Venue::class,
		'tec.rest-v1.endpoints.single-venue-slug'       => \Tribe__Events__REST__V1__Endpoints__Single_Venue_Slug::class,
		'tec.rest-v1.endpoints.single-organizer'        => \Tribe__Events__REST__V1__Endpoints__Single_Organizer::class,
		'tec.rest-v1.endpoints.single-organizer-slug'   => \Tribe__Events__REST__V1__Endpoints__Single_Organizer_Slug::class,
		'tec.rest-v1.endpoints.archive-venue'           => \Tribe__Events__REST__V1__Endpoints__Archive_Venue::class,
		'tec.rest-v1.endpoints.archive-organizer'       => \Tribe__Events__REST__V1__Endpoints__Archive_Organizer::class,
		'tec.rest-v1.endpoints.archive-category'        => \Tribe__Events__REST__V1__Endpoints__Archive_Category::class,
		'tec.rest-v1.endpoints.archive-category'        => \Tribe__Events__REST__V1__Endpoints__Archive_Tag::class,
	];

	// Allow PhpStorm IDE to resolve return types when calling tribe( Object_Type::class ) or tribe( `Object_Type` )
	override( \tribe(), map( $map ) );
	override( \tad_DI52_ServiceProvider::make( 0 ), map( $map ) );
	override( \tad_DI52_Container::make( 0 ), map( $map ) );
}
