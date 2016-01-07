<?php foreach ($field_options as $value => $description): ?>
	<label title="<?php echo $description; ?>">
    	<input type="radio" name="<?php echo $field_name; ?>" value="<?php echo $value; ?>" <?php checked( $field_value == $value ); ?> />
		<?php echo $description; ?>
	</label>
	<br />
<?php endforeach; ?>
