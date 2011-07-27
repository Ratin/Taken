window.addEvent('domready', function() {

     var lx = 0;
     
     $$('#mw-imagepage-section-filehistory tr').each(function(el){
     
        lx++;
        if(lx==2){
          
          //alert(el.innerHTML);    
         
          edit_div = new Element('div', {'class':'msc_div'}).inject(el, 'bottom');
          edit_comment = new Element('span', {'class':'msc_button',title:'Kommentar bearbeiten'}).inject(edit_div, 'bottom');
         //edit_comment.innerHTML = ' <img src="./extensions/MsComment/edit-comment.png">';
         
          edit_comment.addEvents({
         
          mouseover: function() {
             this.addClass('msc_over');
          },
          mouseout: function() {
             this.removeClass('msc_over');
          },
          
          click: function() {
           
              if($('msc_input')){ //speichern
              
                  comment = $('msc_input').value;
                  sajax_do_call( 'SaveComment', [wgTitle,comment],function (result) {
                        result = result.responseText;
                        hinweis_comment = new Element('div', {'class':'msc_input',text:'Der neue Kommentar wurde gespeichert!'}).inject(edit_comment, 'after'); 
                                         
                  }); 
              
              } else { //speichern
              
                  sajax_do_call( 'GetComment', [wgTitle],function (result) {
      
                        result = result.responseText;
                        input_comment = new Element('textarea', {'rows':'4','cols':'25',id:'msc_input','class':'msc_input',value:result}).inject(edit_comment, 'before');
                        edit_comment.title = "Kommentar speichern";

                  }); 
              
              }

            	
            	
          },
        
          });
          
        }
        //alert(lx);
     });
     
});

