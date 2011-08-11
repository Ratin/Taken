window.addEvent('domready', function() {
   if($('show_msnews')){
    sajax_do_call( 'getLatestNews', [$('show_msnews').innerHTML], 
        function (result) {       
        if(result.responseText != ""){
            $('show_msnews').innerHTML = result.responseText;
        } else {
        
        }//if
    });
   } //if
});