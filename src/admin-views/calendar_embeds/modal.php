<?php
/**
 * Modal for calendar embeds.
 *
 * @since TBD
 *
 * @version TBD
 *
 * @var int    $post_id   The post ID.
 * @var string $permalink The permalink.
 */

$snippet = '<iframe src="' . esc_url( $permalink ) . 'embed" width="100%" height="600" frameborder="0"></iframe>';

?>
<div id="tec_events_calendar_embeds_snippet_<?php echo esc_attr( $post_id ); ?>" class="hidden">
	<div>
		<div class="tec-events-calendar-embeds-snippet-modal-text">
			<?php esc_html_e( 'Copy and paste this code to embed the calendar on your website:', 'the-events-calendar' ); ?>
		</div>
		<textarea
			id="tec_events_calendar_embeds_snippet_code_<?php echo esc_attr( $post_id ); ?>"
			class="tec-events-calendar-embeds-snippet-modal-textarea"
			rows="3"
			readonly="readonly"><?php echo esc_html( $snippet ); ?></textarea>
		<button
			class="button button-primary tec-events-calendar-embeds-snippet-modal-copy-button"
			data-clipboard-action="copy"
			data-clipboard-target="#tec_events_calendar_embeds_snippet_code_<?php echo esc_attr( $post_id ); ?>"
		>
			<?php esc_html_e( 'Copy Embed Snippet', 'the-events-calendar' ); ?>
		</button>
	</div>
</div>
<a
	name="Embed Snippet"
	href="/?TB_inline&width=370&height=200&inlineId=tec_events_calendar_embeds_snippet_<?php echo esc_attr( $post_id ); ?>"
	class="thickbox button"
>
	Get Embed Snippet
</a>
