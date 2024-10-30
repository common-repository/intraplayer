<?php
if(isset($_POST['psettings']) && !empty($_POST['psettings']))
{
	$psettings = $_POST['psettings'];
	
	if($api_key = isset($psettings['api_key']) ? $psettings['api_key'] : $this->process_options('get', array('api_key')))
	{
		$embed_data = $this->curl('GET', 'embed_data', array('api_key' => $api_key, 'iframe_design' => $psettings['iframe_design'], 'iframe_size' => $psettings['iframe_size']));
		if(!empty($embed_data) && $embed_data['id'])
			$psettings['embed_data'] = $embed_data;
		else 
			unset($psettings['api_key']);
	}
	
	$this->process_options('update', $psettings);
	
	$this->redirect();
}

if(isset($_POST['esettings']) && !empty($_POST['esettings']))
{
	$esettings = $_POST['esettings'];
	$embed_data = $this->process_options('get', array('embed_data'));
	
	if(!empty($_FILES['s_bgimage']) && @is_uploaded_file($_FILES['s_bgimage']['tmp_name']))
		$files['s_bgimage'] = $_FILES['s_bgimage']; /*'@'.$_FILES['s_bgimage']['tmp_name'].';type='.$_FILES['s_bgimage']['type'];*/
	
	$result = $this->curl('PUT', 'embed_settings', array('client_id' => $embed_data['id'], 'settings' => $esettings), $files);
	if(!empty($result) && $result['ok'])
	{
		if(isset($result['s_bgimage']) && isset($result['s_bgimage_url']))
		{
			$esettings['s_bgimage'] = $result['s_bgimage'];
			$esettings['s_bgimage_url'] = $result['s_bgimage_url'];
		}
		
		$embed_data['settings'] = $esettings;
		$this->process_options('update', array('embed_data' => $embed_data));
	}
	
	$this->redirect();
}

$options = $this->process_options('get', array('list_iframe_settings', 'api_key', 'embed_data', 'iframe_design', 'iframe_size'));
$categories = $options['embed_data']['id'] ? $this->curl('GET', 'list_categories', array('client_id' => $options['embed_data']['id'])) : array();
?>

<div class="wrap ip-content">
	<?php if(!empty($this->errors)): ?>
		<h2></h2>
		<?php foreach($this->errors as $err): ?>
			<div class="error"><p>Error: <?php echo $err; ?></p></div>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php screen_icon('themes'); ?>
	<h2><?php echo INTRAPLAYER_PLUGIN_NAME; ?> - Plugin Settings</h2>
	<form id="ip-plugin-settings" method="post" action="">
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Plugin API KEY:</th>
				<td>
					<?php if($options['api_key']): ?>
						<a href="javascript://" class="api-key"><?php echo $options['api_key']; ?></a>
						<input type="text" name="psettings[api_key]" value="<?php echo $options['api_key']; ?>" disabled="disabled" class="regular-text" style="display:none" />
					<?php else: ?>		
						<input type="text" name="psettings[api_key]" value="<?php echo $options['api_key']; ?>" class="regular-text" />
					<?php endif; ?>		
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Iframe Design:</th>
				<td>
					<?php foreach($options['list_iframe_settings']['designs'] as $kd => $d): ?>
						<label><input type="radio" name="psettings[iframe_design]" value="<?php echo $kd; ?>" <?php echo $kd == $options['iframe_design'] ? 'checked="checked"' : ''; ?> /> <?php echo $d; ?></label>
						&nbsp;&nbsp;
					<?php endforeach; ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Iframe Size:</th>
				<td>
					<?php foreach($options['list_iframe_settings']['sizes'] as $ks => $s): ?>
						<label><input type="radio" name="psettings[iframe_size]" value="<?php echo $ks; ?>" <?php echo $ks == $options['iframe_size'] ? 'checked="checked"' : ''; ?> /> <?php echo $s; ?></label>
						&nbsp;&nbsp;
					<?php endforeach; ?>
				</td>
			</tr>
		</table>
		<div class="submit"><input type="submit" class="button-primary" value="<?php echo __('Save'); ?>" /></div>
	</form>
	
	<?php if($options['embed_data']['id']): ?>
		<?php if($options['embed_data']['type'] == 'client'): ?>
			<?php $esettings = $options['embed_data']['settings']; ?>
		
			<link type="text/css" rel="stylesheet" href="<?php echo INTRAPLAYER_PLUGIN_DIR_URL; ?>css/jquery.miniColors.css" media="all" />
			<script type="text/javascript" src="<?php echo INTRAPLAYER_PLUGIN_DIR_URL; ?>js/jquery.miniColors.js"></script>
		
			<?php screen_icon('edit'); ?>
			<h2><?php echo INTRAPLAYER_PLUGIN_NAME; ?> - Embed Settings</h2>
			<form id="ip-embed-settings" method="post" action="" enctype="multipart/form-data">
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Border:</th>
						<td style="height:45px">
							<?php $s_border = explode(' ', $esettings['s_border'], 3); ?>
							<input type="hidden" name="esettings[s_border]" value="<?php echo $esettings['s_border']; ?>" />
							
							<input id="ip-border-check" type="checkbox" value="1" <?php if($esettings['s_border']): ?>checked="checked"<?php endif; ?> class="left" />
							<div id="ip-border-content" class="left mleft5" <?php if($esettings['s_border'] == ''): ?>style="display:none"<?php endif; ?>>
								<select id="ip-border-width" class="left" style="width:70px">
									<?php foreach($options['list_iframe_settings']['border_widths'] as $kbw => $bw): ?>
										<option value="<?php echo $bw; ?>px" <?php if($s_border[0] == $bw): ?>selected="selected"<?php endif; ?>><?php echo $bw; ?>px</option>
									<?php endforeach; ?>
								</select>
								<input type="hidden" id="ip-border-style" value="solid" />
								<input type="hidden" id="ip-border-color" value="<?php echo $s_border[2]; ?>" class="color-picker miniColors" maxlength="7" />
							</div>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">Background Image:</th>
						<td>
							<?php if($esettings['s_bgimage']): ?>
								<div class="image-box left">
									<input type="hidden" name="esettings[s_bgimage]" value="<?php echo $esettings['s_bgimage']; ?>" />
									<div class="rel">
										<div class="abs delete"></div>
									</div>
									<img src="<?php echo $esettings['s_bgimage_url']; ?>" width="295" alt="Loading Background Image ..." title="Background Image" class="left" />
								</div>
								<br class="clear" />
							<?php endif; ?>
							
							<input type="file" size="40" name="s_bgimage" value="" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">Background Color:</th>
						<td>
							<input type="hidden" name="esettings[s_bgcolor]" class="color-picker miniColors" maxlength="7" value="<?php echo $esettings['s_bgcolor']; ?>" />
						</td>
					</tr>
					<?php if($categories): ?>
						<tr valign="top">
							<th scope="row">Featured Categories:</th>
							<td>
								<input type="hidden" name="esettings[featured_categories]" value="<?php echo $esettings['featured_categories']; ?>" />
								
								<div id="ip-categories">
									<div class="category-item clearfix" val="" style="display:none">
										<div class="title left"></div>
										<div class="delete right"></div>
									</div>
									<?php if($featured_categories = explode(',', $esettings['featured_categories'])): ?>
										<?php foreach($featured_categories as $kfc => $fc): ?>
											<?php if(isset($categories[$fc])): ?>
												<div class="category-item clearfix" val="<?php echo $fc; ?>">
													<div class="title left"><?php echo $categories[$fc]['title']; ?></div>
													<div class="delete right"></div>
												</div>
											<?php endif; ?>	
										<?php endforeach; ?>
									<?php endif; ?>	
								</div>
								
								<select name="categories" class="regular-text" style="width:200px">
									<?php foreach($categories as $kc => $c): ?>
										<option value="<?php echo $c['id']; ?>"><?php echo $c['title']?></option>
									<?php endforeach; ?>
								</select>
								&nbsp;
								<a href="javascript://" class="add-category-link">add category</a>
							</td>
						</tr>
					<?php endif; ?>	
				</table>
				<div class="submit"><input type="submit" class="button-primary" value="<?php echo __('Save'); ?>" /></div>
			</form>
		<?php endif; ?>	
		
		<?php screen_icon('link-manager'); ?>
		<h2><?php echo INTRAPLAYER_PLUGIN_NAME; ?> - Insert Code</h2>
		<textarea name="textarea" class="large-text code" type="textarea" cols="50" rows="2" readonly="readonly" style="margin-top:10px">[<?php echo INTRAPLAYER_PLUGIN_PREFIX; ?>embed_code]</textarea>
	<?php endif; ?>	
</div>