// models
var Component = {
	top: 0,
	init: function() {
		$('.diagram .unit').draggable({handle: 'dt'});
	},
	get: function ( el ) {
		var dom = el.up('dl');
		dom.css({
			top: Component.top + 'px',
			left: '50%'
		}).draggable({handle: 'dt'});

		$('section.units').append( dom );
		Component.top = Component.top - dom.height();

		/* actually this is right.
		jQuery.ajax({
			url: el.attr('href'),
			success: function( result ) {
				var dom = $(result).css({
					top: Component.top + 'px',
					left: '50%'
				}).draggable({handle: 'dt'});
				$('#entities').append( dom );
				Component.top = Component.top - dom.height();
			}
		});
		*/
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


// controllers 
$( function() {
	$('#components').click( function( ev ) {
		ev.preventDefault();
		var el = $(ev.target);
		if ( el.hasClass('btnMove') ) {
			Component.get( el );
		}
	});
	$('form.diagram').submit( function( ev ) {
		ev.preventDefault();
		Component.save( $(ev.target) );
	});
});
