jQuery(function($){

	// Click nút Upload
	$('body').on( 'click', '.caia-upl', function(e){
		e.preventDefault();
		var button = $(this);
		custom_uploader = wp.media({
			title: 'Thêm Logo',
			library : {
				type : 'image'
			},
			button: {
				text: 'Sử dụng ảnh'
			},
			multiple: false
		}).on('select', function() { 
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			button.html('<img src="' + attachment.url + '" style="width: 100px;height: 100px;object-fit: cover;"></br>');
			$('.caia-rmv').show();
			$('.caia-logo').val(attachment.id);
		}).open();	
	});
	// Click nút xoá
	$('body').on('click', '.caia-rmv', function(e){
		e.preventDefault();
		var button = $(this);
		$('.caia-logo').val('');
		button.hide();
		$('.caia-upl').html('Thêm ảnh');
	});

});