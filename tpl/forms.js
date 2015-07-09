$(document).on('pageinit', '#SettingsPage', function(){  
	$(document).on('click', '#submit', function() { // catch the form's submit event
		// Send data to server through the Ajax call
		// action is functionality we want to call and outputJSON is our data
		$.ajax({url: 'action.php',
			data: { method : 'settings',
				redirectTo: $('#redirectTo').val(),
				redirect: $('#redirectSetting').val(),
				dnd: $('#dndSetting').val()
			},
		type: 'post',                   
		async: 'true',
		dataType: 'json',
		beforeSend: function() {
			// This callback function will trigger before data is sent
			$.mobile.loading('show', {theme:"e", text:"Please wait...", textonly:false, textVisible: true});
		},
		complete: function() {
			// This callback function will trigger on data sent/received complete
			$.mobile.loading('hide');
		},
		success: function (result) {
			$('#popup-saved').popup("open");
		},
		error: function (request,error) {
			// This callback function will trigger on unsuccessful action                
			alert('Network error has occurred please try again!');
		}
		});                   
		return false; // cancel original event to prevent form submitting
	});    
});
