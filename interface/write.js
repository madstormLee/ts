document.observe('dom:loaded', function() {
	new InterfaceWrite;
});

var InterfaceWrite = Class.create({
	initialize: function() {
		this.previewContainer = new PreviewContainer;
		$('addContents').observe( 'click', this.onAddContents.bind(this));
		$('addPosition').observe( 'click', this.onAddPosition.bind(this));
		$$('header .mainMenu a').invoke('observe', 'click', this.onClick.bind(this));
		this.form = $('InterfaceWrite');
	},
	onClick: function( ev ) {
		var el = ev.element();
		if ( el.id == 'saveFile' ) {
			ev.stop();
			var url = this.form.action;
			new Ajax.Request( url, {
				parameters: this.form.serialize(),
				onComplete: this.onSaved.bind(this)
			});
		}
	},
	onSaved: function( transport ) {
		var result = transport.responseText;
		if ( result == 0 ) {
			alert( '뭐가 잘못 되었음.' );
		} else {
			// alert( result );
			alert( '저장되었습니다.' );
		}
	},
	onAddContents: function( ev ) {
		ev.stop();
		var el = ev.element();
		var li = new Element('li').update("<input type='text' class='contents' name='contents[]' value='' />");

		el.up('dt').next('dd').down('ol').insert( li );
	},
	onAddPosition: function( ev ) {
		ev.stop();
		alert('보기에서 위치를 선택하세요.');
	}
});


/********************** PreviewContainer ************************/
var PreviewContainer = Class.create({
	no : 0,
	initialize: function() {
		this.tool = new Tool( this );
		this.state = new State( this );
		this.action = new Action( this );
		this.getPreview = new GetPreview( this );
		this.container = $('previewContainer');
		this.container.observe('mouseover', this.onMouseover.bind(this));
		this.container.observe('mouseout', this.onMouseout.bind(this));
		this.container.observe('click', this.onClick.bind(this));
	},
	onMouseover: function( ev ) {
		ev.element().addClassName('current');
	},
	onMouseout: function( ev ) {
		ev.element().removeClassName('current');
	},
	onClick: function( ev ) {
		this.state.needSaveOn();
		var mode = this.tool.getMode();
		ev.stop();
		var el = ev.element();
		if ( mode == 'select' ) {
			ev.element().toggleClassName('selected');
		} else if ( mode == 'marker' ){
			if ( el.hasClassName( 'marker' ) ) {
				el.remove();
			} else {
				this.createSelectArea.bind( this )( el );
			}
		} else if ( mode == 'removeUnit' ) {
			this.removeUnit.bind(this)( el );
		} else if ( mode == 'editHtml' ) {
			this.editHtml.bind(this)( el );
		}
	},
	createSelectArea: function( el ) {
		var pos = el.positionedOffset();
		var marker = new Element('div', { 'class': 'marker' }).setStyle({
			'left': pos.left - 5 + 'px',
			'top': pos.top - 4 + 'px',
			'width': el.getWidth() + 4 + 'px',
			'height': el.getHeight() + 2 + 'px'
		});
		var no = new Element('span', { 'class': 'number' }).update( ++this.no );
		marker.insert( no );
		this.container.insert( marker );
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
	onObtain: function( transport ) {
		var result = transport.responseText; 
		if ( result == 0 ) {
			alert('가져오기 오류가 발생하였습니다.');
		} else {
			this.container.update( result );
			this.state.needSaveOn();
			alert( '내용을 가져왔습니다.' );
		}
	},
	onSaved: function( transport ) {
		var result = transport.responseText; 
		if ( result == 0 ) {
			alert('저장중 오류가 발생하였습니다.');
		} else {
			this.container.update( result );
			alert( '변경 사항이 저장되었습니다.' );
		}
	},
	onEdited: function( ev ) {
		var el = ev.element();
		this.currentElement.update( el.value );
	},
	toggleMarker: function() {
		this.container.select('.marker').invoke('toggle');
	},
	cancelSelect: function() {
		this.container.select('.selected').invoke('removeClassName','selected');
	},
	removeSelected: function() {
		this.container.select('.selected').invoke('remove');
	},
	getContent: function() {
		return this.container.innerHTML;
	},
	update: function( html ) {
		this.container.update( html );
	}
});

/********************** GetPreview ************************/
var GetPreview = Class.create({
	initialize: function( previewContainer ) {
		this.previewContainer = previewContainer;
		this.form = $('getPreview');
		this.form.observe('submit', this.onSubmit.bind(this));
		$('getDefaultPreview').observe('click', this.getDefaultPreview.bind(this));
	},
	getDefaultPreview: function( ev ) {
		ev.stop();
		var url = ev.element().href;
		new Ajax.Request( url, {
			onComplete: this.previewContainer.onObtain.bind(this.previewContainer)
		});
	},
	onSubmit: function ( ev ) {
		ev.stop();
		// var url = this.form.action;
		var url = $('siteUrl').value;
		new Ajax.Request( url, {
			parameters: {
				'siteUrl' : $('siteUrl').value
			},
			onComplete: this.previewContainer.onObtain.bind(this.previewContainer)
		});
	}
});
/********************** Tool ************************/
var Tool = Class.create({
	mode: 'select',
	initialize: function( previewContainer ) {
		this.previewContainer = previewContainer;
		$('tool').observe('click', this.onClick.bind(this));
	},
	getMode: function() {
		return this.mode;
	},
	setMode: function( mode ) {
		this.mode = mode;
	},
	onClick: function( ev ) {
		$$('#tool a').invoke('removeClassName', 'on' );
		ev.stop();
		ev.element().addClassName('on');
		this.mode = ev.element().id;
	}
});

/********************** State ************************/
var State = Class.create({
	initialize: function( previewContainer ) {
		this.previewContainer = previewContainer;
		$('state').observe('click', this.onClick.bind(this));
	},
	getMode: function() {
		return this.mode;
	},
	setMode: function( mode ) {
		this.mode = mode;
	},
	needSaveOn: function() {
		$('savePreview').addClassName('on');
	},
	needSaveOff: function() {
		$('savePreview').removeClassName('on');
	},
	needSave: function() {
		return $('savePreview').hasClassName('on');
	},
	onClick: function( ev ) {
		ev.stop();
		var el = ev.element();
		if ( el.id == 'markerVisible' ) {
			el.toggleClassName('on');
			this.previewContainer.toggleMarker();
		}
		if ( el.id == 'savePreview' ) {
			if ( ! this.needSave() ) {
				alert('저장하실 내용이 없습니다.');
				return false;
			}
			ev.stop();
			var url = el.href;
			var content = this.previewContainer.getContent();
			new Ajax.Request( url, {
				parameters: {
					'content': encodeURIComponent( content )
				},
				onComplete: this.onSaved.bind(this)
			});
		}
	},
	onSaved: function( transport ) {
		var result = transport.responseText;
		if ( result == 0 ) {
			alert( '저장 중 오류가 발생하였습니다.' );
		} else {
			this.needSaveOff();
			this.previewContainer.update( result );
			alert( '변경 사항이 저장되었습니다.' );
		}
	}
});

/********************** Action ************************/
var Action = Class.create({
	initialize: function( previewContainer ) {
		this.previewContainer = previewContainer;
		$('action').observe('click', this.onClick.bind(this));
	},
	getMode: function() {
		return this.mode;
	},
	setMode: function( mode ) {
		this.mode = mode;
	},
	onClick: function( ev ) {
		ev.stop();
		var el = ev.element();
		if ( el.id == 'cancelSelect' ) {
			this.previewContainer.cancelSelect();
		} else if ( el.id == 'removeSelected' ) {
			this.previewContainer.removeSelected();
		}
	}
});
