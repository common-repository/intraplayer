if(typeof jQuery !== 'undefined')
{
	var Intraplayer = {};
	
	Intraplayer.init = function()
	{
		Intraplayer.log('Intraplayer Init');
		
		// api key
		jQuery('#ip-plugin-settings a.api-key').live('click', function(){
			jQuery(this).hide();
			jQuery('#ip-plugin-settings input[name="psettings[api_key]"]').removeAttr('disabled').show();
		});
		
		// color picker
		
		if('miniColors' in jQuery())
			jQuery('#ip-embed-settings .color-picker').miniColors({
				letterCase: 'lowercase',
				change: function(hex, rgb){
					if(jQuery(this).attr('id') == 'ip-border-color' && jQuery('#ip-border-check:checked').length)
						calcBorder(0);
				},
				open: function(hex, rgb){},
				close: function(hex, rgb){}
			});
		
		// border
		function calcBorder(clear)
		{
			jQuery('#ip-embed-settings input[name="esettings[s_border]"]').val(clear ? '' : jQuery('#ip-border-width').val() + ' ' + jQuery('#ip-border-style').val() + ' ' + jQuery('#ip-border-color').val());
		}
		
		jQuery('#ip-border-check').live('click', function(){
			if(this.checked)
			{
				jQuery('#ip-border-content').show();
				calcBorder(0);
			}
			else
			{
				jQuery('#ip-border-content').hide();
				calcBorder(1);
			}
		});
		
		jQuery('#ip-border-width').live('change', function(){
			calcBorder(0);
		});
		
		jQuery('#ip-border-color').live('change', function(){
			calcBorder(0);
		});
		
		// bgimage
		jQuery('#ip-embed-settings .image-box .delete').live('click', function(){
			if(confirm('Are you sure you want to delete this background image?'))
			{
				jQuery(this).parent().parent().remove();
			}
		});
		
		// delete featured category
		jQuery('#ip-categories .category-item .delete').live('click', function(){
			var parent = jQuery(this).parent();
			var val = parent.attr('val');
			
			var input = jQuery('#ip-embed-settings input[name="esettings[featured_categories]"]');
			var ivalue = input.val();
			var icategories = ivalue.length ? ivalue.split(',') : [];
			if(icategories.length)
			{
				for(var i = 0; i < icategories.length; i++)
				{
					if(icategories[i] == val)
					{
						icategories.splice(i, 1);
			            --i;
					}
				}
				
				input.val(icategories.length ? icategories.join(',') : '');
			}
			
			parent.remove();
		});
		
		// add featured category
		jQuery('#ip-embed-settings .add-category-link').live('click', function(){
			var val = jQuery('#ip-embed-settings select[name=categories]').val();
			var text = jQuery('#ip-embed-settings select[name=categories] option:selected').text();
			
			var input = jQuery('#ip-embed-settings input[name="esettings[featured_categories]"]');
			var ivalue = input.val();
			var icategories = ivalue.length ? ivalue.split(',') : [];
			if(icategories.length)
			{
				for(var i = 0; i < icategories.length; i++)
					if(icategories[i] == val)
						return;
			}
			
			icategories.push(val);	
			input.val(icategories.join(','));
			
			var clone = jQuery('#ip-categories .category-item:first').clone();
			clone.attr('val', val);
			clone.find('.title').text(text);
			clone.show();
			jQuery('#ip-categories').append(clone);
		});
	}
	
	Intraplayer.log = function()
	{
	    return 'console' in window ? Function.prototype.apply.call(console.log, console, arguments) : null;
	}
	
	jQuery(function(){
		Intraplayer.init();	
	});
}