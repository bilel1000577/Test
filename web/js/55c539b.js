$('#edit-button').click(function(){
    $('#edit-input').show();
    $('#update-hash').hide();
    $('#input_url_fixe').autoGrowInput();
    $('#input_url_fixe').trigger('update');  
});
 
$("#copy-button").zclip({
    path: "http://www.steamdev.com/zclip/js/ZeroClipboard.swf",
  copy:function(){
             return $("#appendedInputButton").val(); 
     }
});
//set path
//ZeroClipboard.setMoviePath('http://davidwalsh.name/demo/ZeroClipboard.swf');
////create client
//var clip = new ZeroClipboard.Client();
//clip.setHandCursor(true);
////event
//clip.addEventListener('mousedown',function() {
//	clip.setText(document.getElementById('appendedInputButton').value);
//       alert('copy !');
//});
//
////glue it to the button
//clip.glue('copy-button');   


