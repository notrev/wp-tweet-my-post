<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<form method="post" action="options.php">
		<?php
			settings_fields( $this->plugin_settings_slug );
			do_settings_sections( $this->plugin_slug );
			submit_button();
		?>
	</form>
</div>
