<?php

use TEC\Events\Custom_Tables\V1\Migration\Reports\Event_Report;
use TEC\Events\Custom_Tables\V1\Migration\String_Dictionary;

/**
 * @var array  $category   The path to the template directory.
 * @var boolean $has_upcoming         Whether to add upcoming events paginate button.
 * @var boolean $has_past             Whether to add past events paginate button.
 * @var int     $past_start_page      What page to start at for pagination requests.
 * @var int     $upcoming_start_page  What page to start at for pagination requests.
 */
?>
<div class="tec-ct1-upgrade-events-category-container">
	<span>
		<strong><?php echo esc_html( $category['label'] ); ?></strong>
	</span>
	<div class="tec-ct1-upgrade-events-container tec-ct1-upgrade-events-category-<?php echo esc_attr( $category['key'] ); ?>">
		<?php $this->template( 'migration/partials/event-items' ); ?>
	</div>
	<?php
	if ( $has_past || $has_upcoming ) {
		?>
		<div class="tec-ct1-upgrade-events-pagination-buttons-container">
			<?php
			if ( $has_past ) {
				?>
				<a
					href="#"
					data-events-paginate-category="<?php echo esc_attr( $category['key'] ); ?>"
					data-events-paginate="1"
					data-events-paginate-start-page="<?php echo $past_start_page; ?>"
				>Show past events</a>
				<?php
			}
			if ( $has_past && $has_upcoming ) {
				?>
				<span class='tec-ct1-upgrade-migration-pagination-separator'> | </span>
				<?php
			}
			if ( $has_upcoming ) {
				?>
				<a
					href="#"
					data-events-paginate-category="<?php echo esc_attr( $category['key'] ); ?>"
					data-events-paginate-upcoming="1"
					data-events-paginate-start-page="<?php echo $upcoming_start_page; ?>"
					data-events-paginate="1"
				>Show more upcoming events</a>
				<?php
			}
			?>
		</div>
		<?php
	}
	?>
</div>