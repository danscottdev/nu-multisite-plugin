<?php
/**
 *  Template file for admin page
 */

$slug = $this->page_slug; // Should be "global-variables".

?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php if ( isset( $_GET['updated'] ) ) : ?>
		<div id="message" class="updated notice is-dismissible">
			<p>Options Saved</p>
		</div>
	<?php endif; ?>

	<form action="edit.php?action=<?php echo esc_attr( $slug ); ?>-update" method="POST">
		<?php
			settings_fields( $slug . '-page' );
			do_settings_sections( $slug . '-page' );
			submit_button();
		?>
	</form>

</div>