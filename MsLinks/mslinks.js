var button = {
        "imageFile": "extensions/MsLinks/images/L-Link-S.gif",  //image to be shown on the button (may be a full URL too), 22x22 pixels
        "speedTip": "Direktlink",    //text shown in a tooltip when hovering the mouse over the button
        "tagOpen": "{{#l:",        //the text to use to mark the beginning of the block
        "tagClose": "\}\}",      //the text to use to mark the end of the block (if any)
        "sampleText": "Dateiname.ext"   //the sample text to place inside the block
};
mwCustomEditButtons.push(button);
