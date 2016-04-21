var go=false ;
$(document).ready(function() {
    var locale = document.getElementById("locale").value;
    //alert("first url is "+)
    $('#appendedInputButton').autoGrowInput();
    $('#appendedInputButton').trigger('update');
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

/* selecting all link input when clicking gon it */
$('#appendedInputButton').click(function(){ $(this).select()}) ;
$('#WebLinkDesc').click(function(){ $(this).select()});
             
$("#loading").hide();
$("#loadingSend").hide();

   //listen for the form beeing submitted
   $("#myForm").submit(function(){
      //get the url for the form
      var url=$("#myForm").attr("action");
   $("#loading").show();
      //start send the post request
       $.post(url,{
           formName:$("#name_id").val(),
           other:"attributes"
       },function(data){
           //the response is in the data variable
   
            if(data.responseCode==200 ){ 
                
              if(data.hash !=""){

                $('#output').html(data.message);
                $('#url').html(data.url);
                document.getElementById('appendedInputButton').value = data.url;
                $('#appendedInputButton').autoGrowInput();
                $('#appendedInputButton').trigger('update');
                $('#fb_share_btn').attr("href","javascript:window.open('http://www.facebook.com/sharer/sharer.php?u="+data.url+"', '_blank', 'width=400,height=600');void(0);");
                $('#WebLinkDesc').val("<a href=\""+data.url+"\">Click here to take Questionnaire</a>");
                $('#WebLinkDesc').autoGrowInput();
                $('#WebLinkDesc').trigger('update');
                //alert("new Url is : "+data.url);
                $('#gplus_share').html('<g:plus action="share" href="'+data.url+'" style="height: 28px;" ></g:plus>');
                 gapi.plus.go('gplus_share');
                $("#loading").hide();
                      $('#edit-input').hide();
                      $('#update-hash').show();
                      $('#appendedInputButton').autoGrowInput();
                      $('#appendedInputButton').trigger('update');
                      $('p[for="name"]').hide();
               
                }
                                 
             }
           else if(data.responseCode==400 ){//bad request
               
                $("#loading").hide();
                $('p[for="name"]').show();
                //console.log('hash already exist');
                if ( $('p[for="name"]').length >0){
                  if(locale=="ar"){
                      var text = "هذا الاسم موجود يرجى اختيار اسم آخر";
                    }else if(locale=="fr")
                    {
                      var text="Ce nom de sondage existe déja, veuillez choisir un autre !";
                    }else{
                      var text="This survey name already exists , please choose another !";
                    }
                    $('p[for="name"]').text(text).attr("class","text-error");
               
                }else{
                   
                   if(locale=="ar"){
                      $("#myForm").after('<p for="name"  class="text-error">هذا الاسم موجود يرجى اختيار اسم آخر</label>');
                    }else if(locale=="fr")
                    {
                      $("#myForm").after('<p for="name"  class="text-error">Ce nom de sondage existe déja, veuillez choisir un autre. </label>');
                    }else{
                      $("#myForm").after('<p for="name"  class="text-error">This survey name already exists , please choose another !</label>');
                    }
                }
                $('#name_id').focus();
           }
           
           else{
             
              console.log("An unexpeded error occured.");
              

              //if you want to print the error:
             
           }
        });//It is silly. But you should not write 'json' or any thing as the fourth parameter. It should be undefined. I'll explain it futher down
    
      //we dont what the browser to submit the form
      return false;
   });
   
   $("#myFormSend").submit(function(){
   if (go){
    $("#loadingSend").show(); 
     //get the url for the form
     var url=$("#myFormSend").attr("action");
     //start send the post request
       $.post(url,{
           mails:$("#mails").val(),
           description: $("#description").val()
           
       },function(data){
           //the response is in the data variable
   
            if(data.responseCode==200 ){ 
                $("#loadingSend").hide(); 
                if ( $('p[for="mails"]').length >0){
                    if(locale=="ar"){
                      var text = "تم ارسال الإستطلاع بنجاح";
                    }else if(locale=="fr")
                    {
                      var text="Le sondage a été envoyé avec succès";
                    }else{
                      var text="The survey has been sent successfully";
                    }
                    $('p[for="mails"]').text(text).attr("class","text-success");
                  
                 } else {
                  if(locale=="ar"){
                      $("#mails").after('<p for="mails"  class="text-success">تم ارسال الإستطلاع بنجاح</p>');
                    }else if(locale=="fr")
                    {
                      $("#mails").after('<p for="mails"  class="text-success">Le sondage a été envoyé avec succès</p>');
                    }else{
                      $("#mails").after('<p for="mails"  class="text-success">The survey has been sent successfully</p>');
                    }
          
                }
                $('p[for="mails"]').fadeIn();   
                $('#description').val('');
                $('#mails').val('');
                $('p[for="mails"]').delay(3000).fadeOut();   
             }
            else if(data.responseCode==400 ){//bad request
            $("#loadingSend").hide();
              $('p[for="mails"]').text("An error occured please try to resend mails ").attr("class","text-error");
                
            }else{
             $("#loadingSend").hide();
             $('p[for="mails"]').text("An error occured please try to resend mails ").attr("class","text-error");
            }
      });//It is silly. But you should not write 'json' or any thing as the fourth parameter. It should be undefined. I'll explain it futher down   
    }
      
      //we dont what the browser to submit the form
      return false;
   });
   
   
   
   function testMails(value, element) {
         if (this.optional(element)) // return true on optional element 
         {
          return true;
          go = true ;
         }  
     
         var emails = value.split(','); // split element by , and ;
         valid = true;
         for (var i in emails) {
             go = true ;
             value = emails[i];
             valid = valid &&
                     jQuery.validator.methods.email.call(this, $.trim(value), element);
             if (!valid) { 
               
               go = false ;
             }
         }
         return valid;
     }
   
   
   $.validator.addMethod("validateMail",testMails,

    jQuery.validator.messages.email);
    if(locale=="ar"){
      var msg = "يرجى إدخال البريد الإلكتروني";
    }else if(locale=="fr")
    {
      var msg="Veuillez entrer une adresse email";
    }else{
      var msg="Please enter an email address";
    }
    $('#myFormSend').validate(
      {
      rules: { 
        "mails" : {
          required: true,
          validateMail: true
                                                    
    }
  },
  messages: {
    "mails" : {
      required: msg
                        
                }
  }
  });

   function validate(email) {
   var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
   return reg.test(email);
    }

    function trim(str){
      var str = str.replace(/^\s\s*/,''),
        ws = /\s/,
        i = str.length;
      while (ws.test(str.charAt(--i)));
      return str.slice(0, i + 1);
    }              
    $('#WebLinkDesc').autoGrowInput();
    $('#WebLinkDesc').trigger('update');
    function myPopup(url) {
    window.open( url, "myWindow", "status = 1, height = 500, width = 360, resizable = 0" )
}
   
        /*   
        $('#fb_share_btn').click(function (e) {
                    e.preventDefault();
                   FB.ui(
                {
                    method: 'feed',
                    name:  document.getElementById('titre').value,
                    link:  document.getElementById('appendedInputButton').value,
                    picture: 'http://quizmoo.com/web/img/logoquiz.png',
                    caption: 'www.quizmoo.com',
                    description: 'This is the content of the "description" field, below the caption.',
                    message: ''
                });
        });*/
        FB.init({
        appId : '360076597440090'
});

 });
 
 