/**
 * FancyUpload.Attach - Flash meets Ajax for powerful and elegant uploads.
 *
 * @version		3.0 rc1
 *
 * @license		MIT License
 *
 * @author		Harald Kirschner <mail [at] digitarald [dot] de>
 * @author    Martin Schwindl  <madd1n [at] gmx [dot] de>
 * @copyright	Authors
 */

if (!window.FancyUpload3) var FancyUpload3 = {};

FancyUpload3.Attach = new Class({

	Extends: Swiff.Uploader,
	
	options: {
		queued: true, //damit die schlange nicht einfach abgearbeitet wird ...
		instantStart: false//false //--- ms damit der upload nicht sofort beginnt 
	},

	initialize: function(list, selects, options) {
		this.list = $(list);
		this.selects = $(selects) ? $$($(selects)) : $$(selects);
		
    		
		options.target = this.selects[0];
		options.fileClass = options.fileClass || FancyUpload3.Attach.File;
		
		this.parent(options);

		/**
		 * Button state
		 */
		var self = this;
		
		this.selects.addEvents({
			click: function() {
				return false;
			},
			mouseenter: function() {
   
				this.addClass('hover');
				self.reposition(); //die groeße des buttons wird neu berechner -> swiff.uploader.js
			},
			mouseleave: function() {
				this.removeClass('hover');
				this.blur();
			},
			mousedown: function() {
				//this.focus(); sonst muss man zwei mal klicken
				self.reposition();  //die groeße des buttons wird neu berechner -> swiff.uploader.js
			}
		});
		
		///if (this.selects.length == 2) {
		
			//this.selects[1].setStyle('display', 'none');
			//this.addEvents({
			//	'selectSuccess': this.onSelectSuccess,
			//	'fileRemove': this.onFileRemove
			//});
		//}
	},
	
	onSelectSuccess: function() {
		if (this.fileList.length > 0) {
			this.selects[0].setStyle('display', 'none');
			this.selects[1].setStyle('display', 'inline');
			
			this.target = this.selects[1];
			this.reposition();
		}
	},
	
	onFileRemove: function() {
		if (this.fileList.length == 0) {
			this.selects[0].setStyle('display', 'inline');
			this.selects[1].setStyle('display', 'none');
			this.target = this.selects[0];
			this.reposition();
		}
	},
	
	start: function() {
	
		if (Browser.Platform.linux && window.confirm(MooTools.lang.get('FancyUpload', 'linuxWarning'))) return this;
		return this.parent();
	}
	
});

FancyUpload3.Attach.File = new Class({

	Extends: Swiff.Uploader.File,

	render: function() {
		
		if (this.invalid) {
			if (this.validationError) {
				var msg = MooTools.lang.get('FancyUpload', 'validationErrors')[this.validationError] || this.validationError;
				this.validationErrorMessage = msg.substitute({
					name: this.name,
					size: Swiff.Uploader.formatUnit(this.size, 'b'),
					fileSizeMin: Swiff.Uploader.formatUnit(this.base.options.fileSizeMin || 0, 'b'),
					fileSizeMax: Swiff.Uploader.formatUnit(this.base.options.fileSizeMax || 0, 'b'),
					fileListMax: this.base.options.fileListMax || 0,
					fileListSizeMax: Swiff.Uploader.formatUnit(this.base.options.fileListSizeMax || 0, 'b')
				});
			}
			this.remove();
			return;
		}
		
		this.addEvents({
			'open': this.onOpen,
			'remove': this.onRemove,
			'requeue': this.onRequeue,
			'progress': this.onProgress,
			'stop': this.onStop,
			'complete': this.onComplete,
			'error': this.onError
		});
		
		this.ui = {};
		
		this.ui.element = new Element('li', {'class': 'file', id: 'file-' + this.id});
		this.ui.title = new Element('span', {'class': 'file-title', text: this.name});

		this.ui.size = new Element('span', {'class': 'file-size', text: Swiff.Uploader.formatUnit(this.size, 'b')});
		
		this.ui.cancel = new Element('img', {'class': 'file-cancel', src: 'extensions/MsUpload/images/cross-circle.png', title: 'Upload Abbrechen'});
		this.ui.cancel.addEvent('click', function() {
			this.remove();
			return false;
		}.bind(this));
		
		
		
		this.ui.element.adopt(
			this.ui.title,
			this.ui.size,
			this.ui.cancel
		).inject(this.base.list).highlight();
		
		var progress = new Element('img', {id: 'bar-' + this.id, 'class': 'file-progress', src: 'extensions/MsUpload/images/progress-bar/bar.gif'}).inject(this.ui.size, 'after');
		this.ui.progress = new Fx.ProgressBar(progress, {
			fit: true
		}).set(0);
					
		this.base.reposition();

		return this.parent();
	},

	onOpen: function() {
		this.ui.element.addClass('file-uploading');
		if (this.ui.progress) this.ui.progress.set(0);
	},

	onRemove: function() {
		//this.ui = this.ui.element.destroy();
		this.ui = this.ui.element.fade('out').retrieve('tween').chain(Element.destroy.bind(Element, this.ui.element)); //loeschen
	},

	onProgress: function() {
		if (this.ui.progress) this.ui.progress.start(this.progress.percentLoaded);
	},

	onStop: function() {
		//alert('onStop stop');
    //this.remove();
	},
	
	onComplete: function() {
		this.ui.element.removeClass('file-uploading');


    warning = $('warning-' + this.id);

		if (this.response.error) {
		
			var msg = MooTools.lang.get('FancyUpload', 'errors')[this.response.error] || '{error} #{code}';
			this.errorMessage = msg.substitute($extend({name: this.name}, this.response));
			
			this.base.fireEvent('fileError', [this, this.response, this.errorMessage]);
			this.fireEvent('error', [this, this.response, this.errorMessage]);
			return;
		} else {
      
     
     
      //alert(this.extension_check);
			//new Element('span', {text:this.response.text}).inject($('file-' + this.id), 'bottom');
      //alert(this.response.text);
      //alert(this.base.options.pictureExtensions); so könnten variablen übergeben werden
      //---------------------------------
      
      
      //file = this.response.text.split('|');
      //if(!file[1]){
      //new Element('span', {text:this.response.text}).inject($('file-' + this.id), 'bottom');
      //} 

        bar = $('bar-' + this.id);
        bar.set('styles', {'display': 'none'});
              
        li = $('file-' + this.id);
        

        if(neu=$('neu-' + this.id)){ neu.destroy();}
        warning.destroy();
        
        if(!vorlage_insert) {return 'MsInsert muss vorhanden sein!';}
  
                insert_link_a = new Element('a', {id:'insert_link_'+ this.id,text:unescape('als Link einf\u00Fcgen'),href: '#'}).inject(li, 'bottom');
                insert_link_a.addEvent('click', function() {
                vorlage_insert('{{#l:'+this.ui.title.innerHTML+'}}','','');
                // insert link
        		    }.bind(this));
        		  

              if (this.extension_check == "pic"){
        		  
        		    gallery =  $('upload_gallery');
        		    gallery.value += this.ui.title.innerHTML+'|';
        		    
        		    gallery_imgs = gallery.value.split('|');
        		    

        		     if(gallery_imgs.length > 2 ){
        		     
                   if(!$('gallery_link')){  //link schon gesetzt ?
                    
            		      gallery_link = new Element('a', {id: 'gallery_link',text: 'Bilder als Gallery einfügen'}).inject(gallery, 'after');
            		      new Element('span', {text:' | '}).inject(gallery, 'after');
            		    
            		    
            		   } else {
                    gallery_link = $('gallery_link');
                   }
                 
                 
                      gallery_text = "";
                      for(i=0;i<gallery_imgs.length-1;i++){
                      gallery_text +=  "Image:"+gallery_imgs[i]+"\n";
                      }
              		  
                      gallery_link.removeEvents('click'); //damit die alten events entfernt werden
                      gallery_link.addEvent('click', function() { 
                      
                      vorlage_insert(gallery_text,'<gallery>\n\n','\n</gallery>\n'); 

                      
                      });

                      
                } //if

            		  
        		    
        		    new Element('span', {text:' | '}).inject(li, 'bottom');
                insert_bild_a = new Element('a', {id:'insert_bild_'+ this.id,text:unescape('als Bild einf\u00Fcgen'),href: '#'}).inject(li, 'bottom');
                insert_bild_a.addEvent('click', function() {  
                  vorlage_insert('[[Image:'+this.ui.title.innerHTML+'|400px]]','','');
                  // insert link
                }.bind(this));
                
        		  } else if (this.extension_check == "mov") { //pic  
        		  
        		    new Element('span', {text:' | '}).inject(li, 'bottom');
        		    insert_mov_a = new Element('a', {id:'insert_mov_'+ this.id,text:unescape('als Movie einf\u00Fcgen'),href: '#'}).inject(li, 'bottom');
                insert_mov_a.addEvent('click', function() {  
                  vorlage_insert('[[Media:'+this.ui.title.innerHTML+']]','','');
                  // insert link
                }.bind(this));
        		  
        		  } //movie
              li.highlight('#e6efc2');
              li.addClass('file-ok');  
        //---------------------------------
        } //if error
		

	},
  onTest: function() {
  
		alert('yeah');	
		
	},
	
	onError: function() {
		this.ui.element.addClass('file-failed');		
	}

});

//Avoiding MooTools.lang dependency
(function() {
	
	var phrases = {
		'fileName': '{name}',
		'cancel': 'Cancel',
		'cancelTitle': 'Click to cancel and remove this entry.',
		'validationErrors': {
			'duplicate': 'Die Datei <em>{name}</em> haben Sie bereits hinzugefügt.',
			'sizeLimitMin': 'File <em>{name}</em> (<em>{size}</em>) is too small, the minimal file size is {fileSizeMin}.',
			'sizeLimitMax': 'File <em>{name}</em> (<em>{size}</em>) is too big, the maximal file size is <em>{fileSizeMax}</em>.',
			'fileListMax': 'File <em>{name}</em> could not be added, amount of <em>{fileListMax} files</em> exceeded.',
			'fileListSizeMax': 'File <em>{name}</em> (<em>{size}</em>) is too big, overall filesize of <em>{fileListSizeMax}</em> exceeded.'
		},
		'errors': {
			'httpStatus': 'Server returned HTTP-Status #{code}',
			'securityError': 'Security error occured ({text})',
			'ioError': 'Error caused a send or load operation to fail ({text})'
		},
		'linuxWarning': 'Warning: Due to a misbehaviour of Adobe Flash Player on Linux,\nthe browser will probably freeze during the upload process.\nDo you want to start the upload anyway?'
	};
	
	if (MooTools.lang) {
		MooTools.lang.set('en-US', 'FancyUpload', phrases);
	} else {
		MooTools.lang = {
			get: function(from, key) {
				return phrases[key];
			}
		};
	}
	
})();

