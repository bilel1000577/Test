$("#copy-button").zclip({
    path: "http://www.steamdev.com/zclip/js/ZeroClipboard.swf",
    
         copy:function(){
             return $("#appendedInputButton").val();}
  
});

$('#edit-button').click(function(){
    $('#edit-input').show();
    $('#update-hash').hide();
    $('#input_url_fixe').autoGrowInput({
            });
    $('#input_url_fixe').trigger('update');  
    
   
    
    
    
});