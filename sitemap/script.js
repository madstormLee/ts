$( function() {
	$('#sitemap dl dt .more').click( function( ev ) {
		$(ev.target).up('dt').next('dd').toggle();
		return false;
	});
});
