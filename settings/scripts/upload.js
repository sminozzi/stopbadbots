;(function($){

	$(document).ready(function()
	{
			var $upload_buttons = $('.button-upload');
			var custom_uploader;

			$upload_buttons.click(function(e){

				e.preventDefault();

				var $this = $(this), 
				fieldId = '#' + $this.data('field');

				if(custom_uploader) {
					custom_uploader.open();
					return;
				}

				custom_uploader = wp.media.frames.file_frame = wp.media({
					title: 'Choose Image',
					button: {
						text: 'Choose Image',
					},
					multiple: false
				});

				custom_uploader.on('select', function(){
					attachement = custom_uploader.state().get('selection').first().toJSON();
					$(fieldId).val(attachement.url);
				});

				custom_uploader.open();
				
			});

	});


})(jQuery);