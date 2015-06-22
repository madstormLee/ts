// controllers
var Diagram = function() {
	$('.diagram .unit').draggable({handle: 'dt'});
	$('form.diagram').submit( this.save );

	this.save = function( ev ) {
		ev.preventDefault();
		var el = $(ev.target);
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
	};

	this.add = function( el ) {
		$('section.units').append( el );
	};
};

var Models = function() {
	this.top = 3;
	this.get = function ( el ) {
		jQuery.ajax({
			url: el.attr('href'),
			success: function( result ) {
				var dom = $(result).css({
					top: Components.top + 'px',
					left: '50%'
				}).draggable({handle: 'dt'});
				$('#entities').append( dom );
				Components.top = Components.top - dom.height();
			}
		});
	};
};

var Controllers = {
	top: 0,
	init: function() {
		$('#controllers').click( function( ev ) {
			ev.preventDefault();
		});
	},
	get: function ( el ) {
		jQuery.ajax({
			url: el.attr('href'),
			success: function( result ) {
				var dom = $(result).css({
					top: Components.top + 'px',
					left: '50%'
				}).draggable({handle: 'dt'});
				$('#entities').append( dom );
				Components.top = Components.top - dom.height();
			}
		});
	}
};

var Components = {
	init: function() {
		$('#components').click( function( ev ) {
			ev.preventDefault();
			var el = $(ev.target);
			if ( el.hasClass('btnMove') ) {
				Components.get( el );
			}
		});
	},
	get: function ( el ) {
		var dom = el.up('dl');
		dom.css({
			top: Components.top + 'px',
			left: '50%'
		}).draggable({handle: 'dt'});

		$('section.units').append( dom );
		Components.top = Components.top - dom.height();
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


// initialize 
$( function() {
	var diagram = new Diagram;
	Controllers.init();
	// Components.init();

	var models = new Models;

	$('#models').click( function( ev ) {
		ev.preventDefault();
		diagram.add( $(ev.target).up('dl') );
	});
});
