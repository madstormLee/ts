document.observe('dom:loaded', function() {
	new PreviewContainer;
	new PreviewMenu;
});
var PreviewMenu = Class.create({
	initialize: function() {
		this.container = $('previewContainer');
		$$('header .mainMenu a').invoke('observe', 'click', this.onClick.bind(this));
	},
	onClick: function( ev ) {
		var el = ev.element();
		if ( el.id == 'saveFile' ) {
			ev.stop();
			var url = el.href;
			var content = this.container.innerHTML;
			var file = $('file').value;
			new Ajax.Request( url, {
				parameters: {
					'file': encodeURIComponent( file ),
					'content': encodeURIComponent( content )
				},
				onComplete: this.onSaved.bind(this)
			});
		}
	},
	onSaved: function( transport ) {
		var result = transport.responseText;
		if ( result == 0 ) {
			alert( '뭐가 잘못 되었음.' );
		} else {
			alert( '저장되었습니다.' );
			this.container.update( result );
		}
	}
});

var PreviewContainer = Class.create({
	initialize: function() {
		this.tools = new PreviewTools( this );
		$('previewContainer').observe('mouseover', this.onMouseover.bind(this));
		$('previewContainer').observe('mouseout', this.onMouseout.bind(this));
		$('previewContainer').observe('click', this.onClick.bind(this));
		$('saveFile').observe('click', this.onSave.bind(this));
	},
	onSave: function( ev ) {
	},
	onMouseover: function( ev ) {
		ev.element().addClassName('current');
	},
	onMouseout: function( ev ) {
		ev.element().removeClassName('current');
	},
	onClick: function( ev ) {
		var mode = this.tools.getMode();
		ev.stop();
		if ( mode == 'select' ) {
			ev.element().toggleClassName('selected');
		} else if ( mode == 'removeUnit' ) {
			ev.stop();
			var el = ev.element();
			this.removeUnit.bind(this)( el );
		} else if ( mode == 'editHtml' ) {
			var el = ev.element();
			this.editHtml.bind(this)( el );
		}
	},
	removeUnit: function( el ) {
		el.remove();
	},
	editHtml: function( el ) {
		this.currentElement = el;
		var input = new Element('input', {
			type: 'text',
			value: el.innerHTML
		});
		input.observe( 'change', this.onEdited.bind(this) );
		input.observe( 'blur', this.onEdited.bind(this) );
		el.update( input );
		input.select();
	},
	onEdited: function( ev ) {
		var el = ev.element();
		this.currentElement.update( el.value );
	}
});
var PreviewTools = Class.create({
	mode: 'select',
	initialize: function( previewContainer ) {
		this.previewContainer = previewContainer;
		$('tools').observe('click', this.onClick.bind(this));
	},
	getMode: function() {
		return this.mode;
	},
	setMode: function( mode ) {
		this.mode = mode;
	},
	onClick: function( ev ) {
		ev.stop();
		this.mode = ev.element().id;
	}
});
