/*global ajaxurl*/
;(function( $ ) {
	$(document).ready(function() {

		let droppedFiles = false;

		$('#pluginzip, #themezip').on('change', function(e) {
			$('.sg-upload-result').html('');
			$('#install-button-submit').removeClass( 'sg-button--disabled' );
			showFiles(e.target.files);
		});

		$('.wp-upload-form')
			.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
				// preventing the unwanted behaviours
				e.preventDefault();
				e.stopPropagation();
			})
			.on('drop change', function(e) {
				droppedFiles = e.originalEvent.dataTransfer.files; // the files that were droppedß	
				showFiles( e.originalEvent.dataTransfer.files );
				$('.sg-upload-result').html('');
				$('#install-button-submit').removeClass( 'sg-button--disabled' );
			})
			.on('submit', function(e) {
				e.preventDefault()
				$('.sg-upload-result').text('Installing...');

				let formData = new FormData(this)

				if ( typeof( droppedFiles ) !== undefined ) {
					formData.append('pluginzip', droppedFiles[0]);
					formData.append('themezip', droppedFiles[0]);
				}

				$.post({
					type : "POST",
					url : $(this).attr('action'),
					data : formData,
					contentType: false,
					processData: false,
					success: function( response ) {
						let result = $('<div />').append(response).find('.wrap').html();
						$('.sg-upload-result').html(result);
					},
					error: function( response ) {
						$('.sg-upload-result').html('Something went wrong!');
					}
				})	
			})
	})

	function showFiles (files) {
		$('.sg-upload-label').text( files[0].name )
	}
})( jQuery )
