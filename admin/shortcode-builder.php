<div class="wrap">
	<h2><?php _e('Shortcode Builder', 'radykal'); ?></h2>
	<div id="sf-shortcode-type">
		<h4><?php _e('Shortcode Type', 'radykal'); ?></h4>
		<label><input type="radio" name="sf_type" value="listing" checked="checked" /> <?php _e('FAQ Listing', 'radykal'); ?></label>
		<label><input type="radio" name="sf_type" value="search" /> <?php _e('FAQ Search Box', 'radykal'); ?></label>
		<label><input type="radio" name="sf_type" value="popular" /> <?php _e('FAQ Most Popular', 'radykal'); ?></label>
	</div>
	<br /><br />
	<form id="sf-shortcode-form">
		<table class="form-table">
			<tbody>
				<tr valign="top"  class="sf-listing sf-search">
					<th scope="row"><?php _e('Categories', 'radykal'); ?></th>
					<td>
						<select id="sf-categories" data-placeholder="<?php _e('Choose categories', 'radykal'); ?>" multiple>
							<?php
							$categories = get_terms( 'faq_category' );
							foreach($categories as $category) {
								echo '<option value="'.$category->term_id.'">'.$category->name.'</option>';
							}
							?>
						</select>
						<p class="description"><?php _e('Leave empty, if you would like to select all categories!', 'radykal'); ?></p>
					</td>
				</tr>
				<tr valign="top" class="sf-listing">
					<th scope="row"><?php _e('Layout', 'radykal'); ?></th>
					<td>
						<select id="sf-layout">
							<option value="simple"><?php _e('Simple', 'radykal'); ?></option>
							<option value="boxed"><?php _e('Boxed', 'radykal'); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top" class="sf-listing">
					<th scope="row"><?php _e('Open Icon', 'radykal'); ?></th>
					<td>
						<select id="sf-open-icon">
							<option value=""><?php _e('None', 'radykal'); ?></option>
							<option value="plus"><?php _e('Plus', 'radykal'); ?></option>
							<option value="plus-circle"><?php _e('Plus Circle', 'radykal'); ?></option>
							<option value="plus-square"><?php _e('Plus Square', 'radykal'); ?></option>
							<option value="plus-square-o"><?php _e('Plus Square 2', 'radykal'); ?></option>
							<option value="search-plus"><?php _e('Search Plus', 'radykal'); ?></option>
							<option value="angle-right"><?php _e('Angle Right', 'radykal'); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top" class="sf-listing">
					<th scope="row"><?php _e('Close Icon', 'radykal'); ?></th>
					<td>
						<select id="sf-close-icon">
							<option value=""><?php _e('None', 'radykal'); ?></option>
							<option value="minus"><?php _e('Minus', 'radykal'); ?></option>
							<option value="minus-circle"><?php _e('Minus Circle', 'radykal'); ?></option>
							<option value="minus-square"><?php _e('Minus Square', 'radykal'); ?></option>
							<option value="minus-square-o"><?php _e('Minus Square 2', 'radykal'); ?></option>
							<option value="search-minus"><?php _e('Search Minus', 'radykal'); ?></option>
							<option value="angle-down"><?php _e('Angle Down', 'radykal'); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top" class="sf-listing">
					<th scope="row"><?php _e('Headline Tag for title', 'radykal'); ?></th>
					<td>
						<select id="sf-headline-tag">
							<option value="h1"><?php _e('Headline 1', 'radykal'); ?></option>
							<option value="h2"><?php _e('Headline 2', 'radykal'); ?></option>
							<option value="h3"><?php _e('Headline 3', 'radykal'); ?></option>
							<option value="h4"><?php _e('Headline 4', 'radykal'); ?></option>
							<option value="h5"><?php _e('Headline 5', 'radykal'); ?></option>
							<option value="h6"><?php _e('Headline 6', 'radykal'); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top" class="sf-listing">
					<th scope="row"><?php _e('Effect', 'radykal'); ?></th>
					<td>
						<select id="sf-effect">
							<option value="accordion"><?php _e('Accordion', 'radykal'); ?></option>
							<option value="tooltip"><?php _e('Tooltip', 'radykal'); ?></option>
							<option value="none"><?php _e('None', 'radykal'); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top" class="sf-listing">
					<th scope="row"><?php _e('Enable Permalink', 'radykal'); ?></th>
					<td>
						<input type="checkbox" id="sf-permalink" checked="checked" />
					</td>
				</tr>
				<tr valign="top" class="sf-listing">
					<th scope="row"><?php _e('Enable Deeplinking', 'radykal'); ?></th>
					<td>
						<input type="checkbox" id="sf-deeplinking" checked="checked" />
					</td>
				</tr>
				<tr valign="top" class="sf-listing">
					<th scope="row"><?php _e('Voting', 'radykal'); ?></th>
					<td>
						<label>
							<input type="checkbox" id="sf-voting" checked="checked" />
							<?php _e('Can the users rate the FAQ?', 'radykal'); ?>
						</label>
					</td>
				</tr>
				<tr valign="top" class="sf-listing">
					<th scope="row"><?php _e('Enable excerpt', 'radykal'); ?></th>
					<td>
						<label>
							<input type="checkbox" id="sf-excerpt" />
							<?php _e('Shows the excerpt instead the full content text.', 'radykal'); ?>
						</label>
					</td>
				</tr>
				<tr valign="top" class="sf-listing">
					<th scope="row"><?php _e('Category Style', 'radykal'); ?></th>
					<td>
						<input type="text" id="sf-cat-style" class="sf-text-input" placeholder="<?php _e('Add some CSS styles, e.g.: background: #fff;', 'radykal'); ?>"/>
					</td>
				</tr>
				<tr valign="top" class="sf-listing">
					<th scope="row"><?php _e('Title Style', 'radykal'); ?></th>
					<td>
						<input type="text" id="sf-title-style" class="sf-text-input" placeholder="<?php _e('Add some CSS styles, e.g.: background: #fff;', 'radykal'); ?>"/>
					</td>
				</tr>
				<tr valign="top" class="sf-listing">
					<th scope="row"><?php _e('Content Style', 'radykal'); ?></th>
					<td>
						<input type="text" id="sf-content-style" class="sf-text-input" placeholder="<?php _e('Add some CSS styles, e.g.: background: #fff;', 'radykal'); ?>"/>
					</td>
				</tr>
				<tr valign="top" class="sf-search">
					<th scope="row"><?php _e('Browse to FAQ', 'radykal'); ?></th>
					<td>
						<label>
							<input type="checkbox" id="sf-browse-to-faq" />
							<?php _e('Will browse to the FAQ page.', 'radykal'); ?>
						</label>
					</td>
				</tr>
				<tr valign="top" class="sf-search">
					<th scope="row"><?php _e('Subtract Scrolling', 'radykal'); ?></th>
					<td>
						<label>
							<input type="number" id="sf-subtract-scrolling" min="0" step="1" placeholder="0" />
							<p class="description"><?php _e('When the page moves to the requested FAQ in the same page, this will be subtracted from the scrolling. Useful if your site has a fixed header.', 'radykal'); ?></p>
						</label>
					</td>
				</tr>
				<tr valign="top" class="sf-popular">
					<th scope="row"><?php _e('Amount', 'radykal'); ?></th>
					<td>
						<input type="number" min="-1" step="1" id="sf-most-popular-amount" value="5"/>
						<p class="description"><?php _e('Set to -1 to add all FAQs.', 'radykal'); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	<br /><br />
	<h4><?php _e('Copy the shortcode', 'radykal'); ?>:</h4>
	<textarea id="sf-shortcode" cols="70" rows="3"></textarea>
</div>
<script type="text/javascript">

	jQuery(document).ready(function() {

		var $form = jQuery('#sf-shortcode-form'),
			shortcodeStr = '';

		$form.find('select').chosen({width: '300px'});

		$form.on('change keyup', function() {

			var shortcode_type = jQuery('[name="sf_type"]:checked').val();

			$form.find('tr').hide();
			$form.find('tr.sf-'+shortcode_type).show();

			if(shortcode_type == 'listing') {

				shortcodeStr = '[superior_faq ';

				//layout
				var layoutStr = $form.find('#sf-layout').val();
				if(layoutStr != '') {
					shortcodeStr += ' layout="'+layoutStr+'"';
				}

				//open icon
				var openIconStr = $form.find('#sf-open-icon').val();
				if(openIconStr != '') {
					shortcodeStr += ' open_icon="'+openIconStr+'"';
				}

				//close icon
				var closeIonStr = $form.find('#sf-close-icon').val();
				if(closeIonStr != '') {
					shortcodeStr += ' close_icon="'+closeIonStr+'"';
				}

				//headline tag
				var headlineTagStr = $form.find('#sf-headline-tag').val();
				if(headlineTagStr != '') {
					shortcodeStr += ' headline_tag="'+headlineTagStr+'"';
				}

				//effect
				shortcodeStr += ' effect="'+$form.find('#sf-effect').val()+'"';

				//permalink
				shortcodeStr += ' permalink="'+($form.find('#sf-permalink').is(':checked') ? 'yes' : 'no')+'"';

				//deeplinking
				shortcodeStr += ' deeplinking="'+($form.find('#sf-deeplinking').is(':checked') ? 'yes' : 'no')+'"';

				//excerpt
				shortcodeStr += ' excerpt="'+($form.find('#sf-excerpt').is(':checked') ? 'yes' : 'no')+'"';

				//voting
				shortcodeStr += ' voting="'+($form.find('#sf-voting').is(':checked') ? 'yes' : 'no')+'"';

				//category style
				shortcodeStr += ' category_style="'+($form.find('#sf-cat-style').val())+'"';

				//title style
				shortcodeStr += ' title_style="'+($form.find('#sf-title-style').val())+'"';

				//content style
				shortcodeStr += ' content_style="'+($form.find('#sf-content-style').val())+'"';

			}
			else if(shortcode_type == 'search') {

				shortcodeStr = '[superior_faq_search ';

				//browse to faq
				shortcodeStr += ' browse_to_faq="'+($form.find('#sf-browse-to-faq').is(':checked') ? 'yes' : 'no')+'"';

				//subtract scrolling
				shortcodeStr += ' subtract_scrolling="'+$form.find('#sf-subtract-scrolling').val()+'"';

			}
			else {

				shortcodeStr = '[superior_faq_popular ';

				//amount
				shortcodeStr += ' amount="'+($form.find('#sf-most-popular-amount').val())+'"';

			}

			if(shortcode_type != 'popular') {
				//categories
				var categoriesStr = '';
				$form.find('#sf-categories :selected').each(function(i, item){
				  categoriesStr += item.value+',';
				});

				if(categoriesStr != '') {
					shortcodeStr += ' categories="'+categoriesStr.substring(0, categoriesStr.length-1)+'"';
				}
			}

			shortcodeStr += ']';

			jQuery('#sf-shortcode').val(shortcodeStr);

		});

		jQuery('[name="sf_type"]').change(function() {

			$form.change();

		}).change();

		jQuery('#sf-shortcode').focus(function() {
		    var $this = jQuery(this).select();

		    // Work around Chrome's little problem
		    $this.mouseup(function() {
		        // Prevent further mouseup intervention
		        $this.unbind("mouseup");
		        return false;
		    });
		});

	});

</script>