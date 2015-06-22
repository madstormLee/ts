// model
var Entities = {
	top: 0,
	init: function() {
		$('#entities .entity').draggable({handle: 'dt'});
	},
	get: function ( el ) {
		jQuery.ajax({
			url: el.attr('href'),
			success: function( result ) {
				var dom = $(result).css({
					top: Entities.top + 'px',
					left: '50%'
				}).draggable({handle: 'dt'});
				$('#entities').append( dom );
				Entities.top = Entities.top - dom.height();
			}
		});
	},
	save: function( el ) {
		jQuery.ajax({
			type: 'post',
			url: el.attr('action'),
			data: {
				title: $('#title').val(),
				entities: $('#entities').html()
			},
			success: function( result ) {
				if ( result != 1 ) {
					alert( 'something goes wrong!' );
					return false;
				}
				alert( 'saved.' );
			}
		});
	}
};


// controller
$( function() {
	Entities.init();
	$('#tableList').click( function( ev ) {
		ev.preventDefault();
		Entities.get( $(ev.target) );
	});
	$('#ErdWrite').submit( function( ev ) {
		ev.preventDefault();
		Entities.save( $(ev.target) );
	});
});
