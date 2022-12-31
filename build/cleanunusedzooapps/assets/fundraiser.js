jQuery(document).ready(function($) {
        
	document.ajaxSubmitForm = ajaxSubmitForm;
	let responseAttr = {};

	function ajaxSubmitForm(form_id, trigger_id, attr)
	{
		// e.preventDefault();
		var form          = $(form_id),
		    submit_button = $('button[type="submit"]', form);
		
		responseAttr = attr;
		submit_button.click();
	}
	
	$(".ajax-submission-form").on('submit', function(e)
	{

		e.preventDefault();
		var form = $(this);
		
		$('.form_trigger', form).addClass('uk-disabled');
		$('.form_trigger [uk-spinner]', form).removeClass('uk-hidden');

		// Get content from editor iframe
		// if (form.find('textarea'))
		// {
		// 	form.find('textarea').each((index, textarea) => {
		// 		var textareaIframe = $('#' + $(textarea).attr('id') + '_ifr');
		// 		if (textareaIframe.length) $(textarea).val(
		// 			textareaIframe.contents().find("body").html()
		// 		);
		// 		// escape(textareaIframe.contents().find("body").html())
		// 	});
		// }
		
		var actionUrl = form.attr('action')
			formData = form.serializeArray(),
			cleanData = [];

		console.log(formData);
		formData.forEach(function(value, index) {
			if (value.value != '') cleanData.push(value);
		});

		console.log('formData', cleanData);
		
		$.ajax({
			type: "POST",
			url: actionUrl,
			data: cleanData, // serializes the form's elements.
			success: async function(data)
			{

				$('.form_trigger', form).removeClass('uk-disabled');
				$('.form_trigger [uk-spinner]', form).addClass('uk-hidden');
				// $('.form_trigger', form).prepend('<span class="confirmed" uk-icon="icon: check"></span>');
                
                document.ajaxSubmitDone($(form), responseAttr);
                
                // setTimeout( function(){ 
                //     $('.form_trigger .confirmed', form).remove();
                    // $('button[type="submit"] .confirmed', form).addClass('uk-animation-fade uk-animation-reverse');
                    // setTimeout( function(){ 
                    //     $('button[type="submit"] .confirmed', form).remove();
                    // }, 500 );
                // }, 1000 );
				
			}
		});
		
	});
    $.each($('.render-zoo-layout'), async function() {
            
        var item_id = $(this).data('item-id'),
            layout = $(this).data('layout'),
            renderedLayout = await GlobalFundraiserSendAjax('renderLayout', {
                item_id,
                layout
            });
        
        if (renderedLayout.success)
        {
            $(this).html(renderedLayout.data[0]);
        }

    });
    
});