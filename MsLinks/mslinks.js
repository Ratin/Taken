
var button = {
        "imageFile": "extensions/MsLinks/images/L-Link-S.gif",  //image to be shown on the button (may be a full URL too), 22x22 pixels
        "speedTip": "Direktlink",    //text shown in a tooltip when hovering the mouse over the button
        "tagOpen": "{{#l:",        //the text to use to mark the beginning of the block
        "tagClose": "\}\}",      //the text to use to mark the end of the block (if any)
        "sampleText": "Dateiname.ext"   //the sample text to place inside the block
};
mwCustomEditButtons.push(button);


window.addEvent('domready', function() {
  
  //datenbank erstellen
  //sajax_do_call( 'wfMsLinksDB',[],function (result2) { alert(result2.responseText); });
  //datenbank erstellen ende
  
  color_list = new Array('#00cc00','#fffc00','#ff0000');


  $$('.status_file').each(function(el,anz){
  
      file_name = el.innerHTML;    
      el.empty();
      

       
      sajax_do_call( 'databaseRead',[file_name],function (result) { 
      
      info = result.responseText.split("|");
      //alert(this.file_name);

      for(i=1;i<4;i++){
          
          radio = new Element('input', {name: info[0],'class':i,type: 'radio'}).inject(el, 'before');
          radio.setStyle('background-color', color_list[i-1]);
                   
          if(info[1]==i){
          radio.checked = true;
          }

              radio.addEvent('change', function() {
                
                sajax_do_call( 'databaseSave', [this.className,this.name], 
                function (result) {
                  
                  alert("Neuer Status \""+result.responseText+"\" wurde gespeichert");
                   

                 

                });
              
              }.bind(radio));
          
          } //for

        
      });
  
  });
     
     

});
