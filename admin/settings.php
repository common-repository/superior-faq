<div class="wrap">
	<h2><?php _e('Settings', 'radykal'); ?></h2>
	<form method="post" id="sf-settings-form">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e('Permalink Label', 'radykal'); ?></th>
					<td>
						<input type="text" name="permalink_label" value="<?php echo esc_attr($settings['permalink_label']); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Back to FAQs Label', 'radykal'); ?></th>
					<td>
						<input type="text" name="back_to_faqs_label" value="<?php echo esc_attr($settings['back_to_faqs_label']); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Search Placeholder', 'radykal'); ?></th>
					<td>
						<input type="text" name="search_placeholder" value="<?php echo esc_attr($settings['search_placeholder']); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('General Parent Page', 'radykal'); ?></th>
					<td>
						<?php
						$dropdown = wp_dropdown_pages( array(
								'name' => 'general_parent_page',
								'selected' => $settings['general_parent_page'],
								'echo' => false
							)
						);

						$index = strpos($dropdown, '<option');
						echo substr_replace($dropdown, '<option value="-1">'.__('None', 'radykal').'</option>', $index, 0);
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Template for FAQs posts', 'radykal'); ?></th>
					<td>
						<select name="template" data-placeholder="<?php _e( 'Choose a template', 'radykal' ); ?>">
							<option value="single.php" <?php selected($settings['template'], 'single.php'); ?> ><?php _e('Single', 'radykal'); ?></option>
							<option value="page.php" <?php selected($settings['template'], 'page.php'); ?>><?php _e('Page', 'radykal'); ?></option>
							<?php
							$templates = get_page_templates();
							foreach ( $templates as $template_name => $template_filename ) {
								echo '<option value="'. esc_attr($template_filename).'" '.selected($settings['template'], $template_filename).'>'.$template_name.'</option>';
							}
							?>
						</select>

					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Enable voting on single FAQ posts', 'radykal'); ?></th>
					<td>
						<input type="checkbox" name="voting_single" value="1" <?php checked($settings['voting_single'], 1); ?> />
					</td>
				</tr>
			</tbody>
		</table>
		<p><?php submit_button(__('Save Settings', 'radykal'), 'primary', 'submit', false); ?></p>
	</form>
</div>
<script type="text/javascript">

	jQuery(document).ready(function() {

		jQuery('#sf-settings-form select').chosen({width: '300px'});

	});

</script>