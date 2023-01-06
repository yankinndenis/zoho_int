(function( $ ) {
	$('body').on('click','#update_zoho',function(){
		console.log('test');
		var site_url = document.location.origin;
		$.ajax({
			type: 'POST',
			url: site_url + '/wp-admin/admin-ajax.php',
			data: {
				action: 'update_products_by_zoho'
			},
		error: function(error){
			alert('error');
			},
			dataType: "json",
			cache: false,
		success: function(data){

			} //endsuccess
		}); //endajax
	});
	$('body').on('click','#update_zoho',function(){
		var site_url = document.location.origin;
		$.ajax({
			type: 'POST',
			url: site_url + '/wp-admin/admin-ajax.php',
			data: {
				action: 'update_products_by_zoho_each'
			},
		error: function(error){
			alert('error');
			},
			dataType: "json",
			cache: false,
		success: function(data){

			} //endsuccess
		}); //endajax
	});

})( jQuery );
