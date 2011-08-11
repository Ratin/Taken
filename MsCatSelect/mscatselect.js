window.addEvent('domready', function() {

     $$('#msc_added .msc_checkbox').each(function(el){
     
          sortkey = el.value.split('|');
          if(sortkey[1]){
          sortkey_text = sortkey[1];
          } else {
          sortkey_text = wgTitle;
          }
          add_sortkey(el,sortkey[0],sortkey_text);
     
     });
     
});

function add_sortkey(el,title,sort){

      sortkey = new Element('span', {'class':'msc_sortkey',text:title,title:sort}).inject(el, 'after');
      sortkey.innerHTML = title+'<img src="./extensions/MsCatSelect/sortkey.png">';
      
      sortkey.addEvent('click', function() {
          
          userInput = prompt(unescape('Hier bitte den Sortierschl%FCssel eingeben. Der Sortierschl%FCssel ist f%FCr die Sortierung in der Kategorie%FCbersicht relevant.'),sort);
          
          if (userInput != '' && userInput != null && userInput!= sort) {

           el.value = title+'|'+userInput; 
           //alert('Der Sortierschl%FCssel wurde gespeichert');
           sortkey.title = userInput;
           sort = userInput;
          }

        }.bind(this));
     return sortkey;  
}


function neu() {
catg_kat = '';

  for (l=4;l>0;l--){ // von hinten anfangen

      if (dd = document.getElementById('dd_'+l)) { //wenn element existiert
        if(dd.value!= 0) {
           catg_kat =dd.value;           
           break;
         }
       }
  }

  if (catg_kat != 0){
  catg_kat = '[[Kategorie:'+catg_kat+']]';
  }

  new_name = document.getElementById('new_name').value;
  name_kat = 'Kategorie:'+new_name;
  
  sajax_do_call( 'fnNewCategory', [name_kat,catg_kat],function (result) {
  
  	    warning = result.responseText;
        if (warning.substr(0,2) == 'no') {
        alert('Kategorie schon vorhanden');
        }else {
        getUnterkat(dd.value,l);
        alert('Kategorie erfolgreich angelegt');
        addKat(new_name);
        }
  
  	});

}

function addKat(new_kat) {

  if (new_kat==1) {
    for (l=4;l>0;l--){

        if (dd = document.getElementById('dd_'+l)) {
        
          if(dd.value!= 0) {
            new_kat = dd.value;
            break; //damit nur der hinterste wert genommen wird
          } 
  
        } //if
     } //for
     
   
  }
  if (new_kat!=1) {

        msc_added = document.getElementById("msc_added"); 
        checkbox = new Element('input', {'class':'msc_checkbox',type:'checkbox',name:'SelectCategoryList[]',value:new_kat,'checked': true}).inject(msc_added, 'after');
        sortkey = add_sortkey(checkbox,new_kat,wgTitle);
        br = new Element('span').inject(sortkey, 'after');
        br.innerHTML = '<br>'; 
       
  } //newcat !=1        
}

function getUnterkat(kat,ebene){

  sajax_do_call( 'fnCategoryGetChildren', [kat], 
        			function (result) {
        			warning = result.responseText;
              
              //alte dropdowns löschen
              for (i=ebene+1;i<=4;i++){
                  if (child = document.getElementById('dd_'+i)) {
                    child.parentNode.removeChild(child); 
                  }
              }
              
              if (warning!="" && ebene<4) {
                createDD(warning,ebene+1);
              } 
              
              });

}

function createDD (str_werte,ebene)  {


  var werte = str_werte.split("|");
	// Select erstellen       

     var objSel = document.createElement("select");
     objSel.id = 'dd_'+ebene;
     
     objSel.onchange= function(){
        
        var sel = this.options[this.selectedIndex];
          if (sel != 0) {
            getUnterkat(sel.value,ebene);
          }
        
        };
          
          objSel.options[objSel.options.length] = new Option('---', 0);
           
          for (var i = 0; i < werte.length; ++i){
          
            objSel.options[objSel.options.length] = new Option(werte[i], werte[i]);
          }
      
    document.getElementById('sdd').appendChild(objSel);
    
}
