window.addEvent('domready', function() {

  if($('show_ampel')){
   
   sajax_do_call( 'getRevision', [wgArticleId], 
    function (result2) {       
        
        
        var revision = result2.responseText;

        if(revision != ""){
            //revision =  result2.responseText;
            $('revision').innerHTML = revision;
        } else {
             alert('sdsd'+revision);
            $('revision').innerHTML = 'Fehler';
        //alert('fehler123:keine Revision gefunden');
        }//if
    
    sajax_do_call( 'databaseRead', [wgArticleId,wgTitle,wgCurRevisionId,wgCanonicalNamespace], 
        function (result3) { 
        
        //alert(result3.responseText);
        vars = new Array(wgTitle,wgArticleId,wgCurRevisionId,wgCanonicalNamespace);
              
        if(result3.responseText == ""){
            
            $('ampel_rot').addEvent('click', function() {
            sajax_do_call( 'apiSend2', ['erstellt',revision,vars], 
            function (result) {
             labeling('erstellt',result.responseText);
             colorize('ampel_rot');
            });
           }.bind(this));
        
        } else {
        
        //alert(result.responseText);
        info = result3.responseText.split("!");
        
          //alert("e"+info[0]);
          if (info[0] == 1){
            labeling('erstellt',info[1]);
            colorize('ampel_rot');
            $('ampel_gelb').addEvent('click', function() {
              sajax_do_call( 'apiSend2', ['geprueft',revision,vars], 
              function (result) {
              //alert(result.responseText);
              if(result.responseText == 0){
                alert('Sie haben diesen Artikel erstellt und sind nicht berechtigt, die Prüfung oder Freigabe zu übernehmen.');
              } else if (result.responseText == 1) {
                alert('Sie müssen eingeloggt sein um die Prüfung zu übernehmen.');
              }else{
                labeling('geprueft',result.responseText);
                decolorize('ampel_rot');
                colorize('ampel_gelb');
              }
               
              });
             }.bind(this));
             
          }else if (info[0] == 2){
          
            labeling('erstellt',info[1]);
            labeling('geprueft',info[2]);
            colorize('ampel_gelb');
              $('ampel_gruen').addEvent('click', function() {
              sajax_do_call( 'apiSend2', ['freigegeben',revision,vars], 
              function (result) {
                //alert(result.responseText);
                if(result.responseText == 0){
                  alert('Sie haben diesen Artikel geprüft oder erstellt und sind nicht berechtigt, die Freigabe zu übernehmen.');
                }else if(result.responseText == 1) {
                  alert('Sie müssen eingeloggt sein um die Freigabe zu übernehmen.');
                }else{
                  labeling('freigegeben',result.responseText);
                  decolorize('ampel_gelb');
                  colorize('ampel_gruen');
               }
              });
             }.bind(this));
             
          }else if (info[0] == 3){
          
            labeling('erstellt',info[1]);
            labeling('geprueft',info[2]);
            labeling('freigegeben',info[3]);
            colorize('ampel_gruen');
            
          } else {//alert("e"+info[0]);
          }
        
        }//else
     
        }//function
    );   
    }); //revision
  }//if ampel
  

});

function labeling(label,label_nam){
  if(label_nam){
  label_nam = label_nam.split("|");
  }else{
  label_nam =  $(label).innerHTML.split("|");
  }
  $(label+'_name').innerHTML = label_nam[0];
  
  if(label_nam[1]){
    $(label+'_datum').innerHTML = label_nam[1];
  } else {$(label+'_datum').innerHTML ="-";}

  if(label_nam[2]){
    $(label+'_funktion').innerHTML = label_nam[2];
  } else {$(label+'_funktion').innerHTML ="nicht definiert";}

}

function colorize(ampel){
  $(ampel).removeClass('ampel_grau');
  $(ampel).addClass(ampel);
}

function decolorize(ampel){
  $(ampel).removeClass(ampel);
  $(ampel).addClass('ampel_grau');
}