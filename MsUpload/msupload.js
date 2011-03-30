
function loadMsUpload(){
  
  sajax_do_call( 'wfMsUploadRender', [], 
  function (result) {
  
  info = result.responseText.split('|'); 
  //upload div anlegen
  upload_div = new Element('div', { id: 'upload_div'}).inject($('toolbar'), 'bottom');
  upload_div.innerHTML = info[0];

  userName = info[1];
  autoKat = info[2];
  autoIndex = info[3];
  
  var categories = new Array();
  
  
  function ajax_check(file,check,firsttime,ausgabe){
        
        
        //if(!ausgabe){ //wenn funktion über change aufgerufen wird
        //ausgabe = $('warning-' + file.id);  
        //}
        ausgabe.innerHTML = "<img src='extensions/MsUpload/images/anim.gif'>";

        sajax_do_call( 'wfMsUploadCheck', [file.extension],
        function (response) {
        if (response.responseText == 1){
          sajax_do_call( 'SpecialUpload::ajaxGetExistsWarning', [check], 
        			function (result) {
        				warning = result.responseText;

        				if ( warning == '' || warning == '&nbsp;' ) {
        				ausgabe.innerHTML = "Datei kann hochgeladen werden";

        				} else {
                // errorhandling
                warning = warning.substring(8);
                warning = warning.substring(0,warning.length-(warning.length-40));
                
                  if (warning == "<p>Eine Datei mit diesem Namen wurde sch" || warning == "Eine Datei mit diesem Namen wurde schon ") {
                    ausgabe.innerHTML = "Eine Datei mit diesem Namen wurde schon einmal hochgeladen und zwischenzeitlich wieder gelöscht.";
                  } else if(warning == "Eine Datei mit diesem Namen existiert be" || warning == "e Datei mit diesem Namen existiert berei") {
                    ausgabe.innerHTML =  "Eine Datei mit diesem Namen existiert bereits. Beim Hochladen wird die alte Datei überschrieben.";
                  } else if(warning == " Dateiname beginnt mit <b>„IMG“</b>. Die") {
                    ausgabe.innerHTML =  "Datei kann hochgeladen werden";
                  } else {
                    ausgabe.innerHTML = warning;
                  }
                // errorhandling
             
                }
       				
                //add_kat(file,startlink,false);
                //startlink.innerHTML = startlink_text;
                //startlink.addEvent('click', function() {
                //file.start();	 
                //cat =  "[[Kategorie:" + categories[file.id].join("]][[Kategorie:") + "]]";
                //new Element('span', { id: 'categories-'+file.id, value: cat}).inject(startlink_element, 'bottom');
        		    //this.fileStart(file);
        		    //ids = file.id;
        		    //}.bind(up));
        		    
        				
        			});
        			
        			if(firsttime!=false){ //nur beim ersten mal
              build(file); // alles aufbauen
              new Element('input', {type:'hidden',value:wgPageName, name: 'kat_hidden'}).inject($('upload-form'), 'bottom');
              }
        			
        			
        	 } else {

            new Element('li', {
				    //html: file.name +' Es sind nur folgende Dateitypen erlaubt: '+response.responseText,
				    'class': 'file-invalid',
				    events: {
						click: function() {
							this.destroy();
						}}
			      }).adopt(
					   new Element('span', {html: '<i>'+file.name +'</i> Es sind nur folgende Dateitypen erlaubt: '+response.responseText})
				    ).inject(file.ui.element, 'before');
			
				    file.remove();

           }//else
           });
  }		
  
  function build(file){
   
      //fileindexer
      if(autoIndex){
        new Element('input', {name:'fi['+file.id+']', 'class':'check_index',type: 'checkbox', 'checked': true}).inject(file.ui.title, 'after');
    	  new Element('span', {'class':'check_span',html: 'Index erstellen'}).inject(file.ui.title, 'after');
      }
      
      //autokat
      if(autoKat){
        if(wgNamespaceNumber==14){
          new Element('input', {name:'kat['+file.id+']', 'class':'check_index',type: 'checkbox', 'checked': true}).inject(file.ui.title, 'after');
    	    new Element('span', {'class':'check_span',html: wgPageName}).inject(file.ui.title, 'after');
    	    
        }
      } 
      
    	name_hidden = new Element('input', {type:'hidden',value:file.name, name: 'name_hidden[]',id: 'hidden-'+file.id}).inject(file.ui.title, 'before');
    		 
    		 
      change = new Element('a', {id: 'change-'+file.id,text: 'Datei umbenennen'}).inject(file.ui.title, 'after');
    	change.addEvent('click', function() {

    		  this.set('styles', {'display': 'none'});
          file.ui.title.set('styles', {'display': 'none'});
    		  
          input_change = new Element('input', {'class':'input_change',size:file.name.length,id: 'input_change-'+file.id, value:file.name}).inject(file.ui.title, 'before');
    		  input_change.addEvent('change', function() {
              
              $('hidden-'+file.id).value = file.name+"|"+this.value;
              ajax_check(file,this.value,false,$('warning-' + file.id));
    		  
    		  }.bind(input_change));

    		}.bind(change));
  
  
  } 
   
    
	function add_kat(file,startlink,dropdown){
	
	      if(dropdown==false){
        
        new Element('input', {id: 'cat-'+file.id, value:''}).inject(startlink, 'before');

        } else {
    	      sel = new Element('select', {id: 'sel-'+file.id, 'class':'sel'}).inject(startlink, 'before');
            
            sel.options[sel.options.length] = new Option('Kategorie', 0);
            sel.onchange= function(){
            
            var asel = this.options[this.selectedIndex].value;
            //categories[file.id].push(categories[asel]); //Kategorie zum array hinzufügen
            
            new Element('span', {html: categories[asel]}).inject(this, 'after');
            new Element('input', {type: 'checkbox', 'checked': true}).inject(this, 'after');
            sel.selectedIndex = 0;
    
            
            };
            
            for (var i = 0; i < categories.length; ++i){
                sel.options[sel.options.length] = new Option(categories[i], i);
            }
            
        }
	     /*
	      sajax_do_call( 'wfMsUploadKat', [], 
        function (result) {

        info = result.responseText;
        var kat = info.split('|'); 
        
        sel = new Element('select', {id: 'sel-'+file.id, 'class':'sel'}).inject(file.ui.element, 'top');
        
        sel = $('sel-' + file.id);
        sel.options[sel.options.length] = new Option('Kategorie', 0);
        sel.onchange= function(){
        
        var asel = this.options[this.selectedIndex].value;
        
        categories[file.id].push(kat[asel]); //Kategorie zum array hinzufügen
        
        new Element('span', {html: kat[asel]}).inject(this, 'after');
        new Element('input', {type: 'checkbox', 'checked': true}).inject(this, 'after');
        sel.selectedIndex = 0;

        
        };
        
        for (var i = 0; i < kat.length; ++i){
            sel.options[sel.options.length] = new Option(kat[i], i);
        }
        
        
        
        });
	      */

	
	}
	
	/* Uploader instance */
	var ids = 0;
	 
	var up = new FancyUpload3.Attach('upload_list', '#show_upload, #demo-attach-2', {
		path: 'extensions/MsUpload/source/Swiff.Uploader.swf',
    url: 'extensions/MsUpload/msupload_api.php?user='+userName,
		fileSizeMax: 100 * 1024 * 1024, //100MB
    //allowDuplicates: true;        
		verbose: true,

    //beim laden alle kategorien abfragen
    onBeforeStart: function() {
	     up.setOptions({
		   data: $('upload-form').toQueryString()
	     });
    },

    onLoad:function() {
    
   $('upload_all').addEvent('click', function() {
				up.start(); // start upload  
				return false;
			});
	



      /*
      sajax_do_call( 'wfMsUploadKat', [], 
        function (result) {
        info = result.responseText;
        categories = info.split('|');
        });
       */ 
    },
  
    
		onSelectFail: function(files) {
			files.each(function(file) {
				new Element('li', {
					'class': 'file-invalid',
					events: {
						click: function() {
							this.destroy();
						}
					}
				}).adopt(
					new Element('span', {html: file.validationErrorMessage || file.validationError})
				).inject(this.list, 'bottom');
			}, this);	
		},
		
		
		onBeforeFileStart: function(file) {
      //wenn der automatische upload stoppen soll alles unter onFileStart
       if(ids != file.id ) {
          this.fileStop(file);
        } else {
          //starten
        }  
    },

    onFileStart: function(file) {
         //damit der upload fortschritt angezeigt wird
         bar = $('bar-' + file.id);
         bar.set('styles', {'display': 'inline'});
         $('change-'+file.id).destroy(); //umbenennen löschen
         
         if ($('input_change-' + file.id)){
         if(input_change = $('input_change-' + file.id).value){
           $('input_change-' + file.id).destroy(); 
           file.ui.title.set('styles', {'display': 'inline'});
           file.ui.title.innerHTML = input_change;
         } 
         }
        
        
    },
    
		
		onSelectSuccess: function(files) {
		
		  //fehlerhafte löschen
		  $$('#upload_list .file-invalid').each(function(invalid){
        invalid.destroy();
      });
      //fehlerhafte löschen
      
      $('upload_all').innerHTML = "Hier klicken um alle Dateien hochzuladen";

      files.each(function(file) {
				
				
				
        bar = $('bar-' + file.id);
        bar.set('styles', {'display': 'none'});
      
        warning = new Element('span', {id: 'warning-'+file.id, 'class':'warning'}).inject(file.ui.element, 'bottom');
        warning.innerHTML = "<img src='extensions/MsUpload/images/anim.gif'>";

        ajax_check(file,file.name,true,warning); 		
        
			}, this);	
		
		},       
		//--------
		onFileComplete:function(file,response){
		
		//this.fileStop('file-'+file.id);
		//file.stop();
      
    
    
		},
		
    onFileProgress:function(file){
     

    },
    

		onFileSuccess: function(file,response) {
		

		},
 
		onFileError: function(file) {
			file.ui.cancel.set('html', 'Retry').removeEvents().addEvent('click', function() {
				file.requeue();
				return false;
			});
 
			new Element('span', {
				html: file.errorMessage,
				'class': 'file-error'
			}).inject(file.ui.cancel, 'after');
		},
 
		onFileRequeue: function(file) {
			file.ui.element.getElement('.file-error').destroy();
 
			file.ui.cancel.set('html', 'Cancel').removeEvents().addEvent('click', function() {
				file.remove();
				return false;
			});
 
			this.start();
		}
 
	});
 
  
}); //function
}


function create_button(beschr,bild,funktion){	

	var toolbar = document.getElementById("toolbar");
	if(!toolbar){
		return false
	}
	var button = document.createElement("a");
	button.href = "#";
  button.title = "Dateien hochladen";
  button.id = "show_upload";
	//button.onclick = function () {
	//funktion();
	//return false;
	//};
 
	var add_image=document.createElement('img');
  add_image.src= bild;
	toolbar.appendChild(button);
	button.appendChild(add_image);
  loadMsUpload();
  
	return true
} 