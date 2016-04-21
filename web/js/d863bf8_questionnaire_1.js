var container ;
var oldDescription ;


$("document").ready(function (){
  jQuery (function($) {
  $('#save_tmp').on("click",function(e){
    e.preventDefault();
    $("#loadingSave").show();
        $.post($(this).attr('data-link'), $(this).serialize(), function(response){
              if(response.code == 100 && response.success){
                $("#loadingSave").hide();
                $("#saveastemplateModal").modal('show');
              }
              if(response.code == 200 && response.success){
                $("#loadingSave").hide();
              }
        },'json');
     });
  });
  /*$('#multipleCheck').change(function() {
      if($(this).is(":checked")) {
        console.log('checked');
        $('.myCheckC').prop('checked', false);
        $.ajax({
          type: "POST",
          url:  $(this).attr('value'),
          data: $(this).serialize(),
          success: function(data) {
           $('#check').hide();
           $('#checkDiv').show();
          }
        });
      } else{
        console.log('unchecked');
      }
  });*/
  $('.tooltipster').tooltipster({position:'right', contentAsHTML: true});
  $('#singleCheck').change(function() {
    var id = document.getElementById("questionnaire_id");
    var locale = document.getElementById("locale").value;
      if($(this).is(":checked")) {
        //$('.myCheck').prop('checked', false);
        $.ajax({
          type: "POST",
          url:  $(this).attr('value'),
          data: $(this).serialize(),
          success: function(data) {
              var url= Routing.generate('displayMultipleQuestion', { _locale: locale,id: id.value });
              console.log(url);
             $('.myCheckC').val(url);
          }
        });

      } else{
         $.ajax({
          type: "POST",
          url:  $(this).attr('value'),
          data: $(this).serialize(),
          success: function(data) {
             var url= Routing.generate('displaySingleQuestion', { _locale: locale,id: id.value });
             console.log(url);
             $('.myCheckC').val(url);
          }
        });

      }

  });
  // Choose Categorie
  $('select#my_selection').change(function(){
    if($('select#my_selection option:selected').val() != ""){
    $.ajax({
          type: "POST",
          url:  $('select#my_selection option:selected').attr("action"),
          data: $(this).serialize(),
          success: function(data) {
              console.log(data);
          }
        });
  }else{
    console.log('Choose one category');
  }
  });
  jQuery (function($) {
  $('#overrideTmp').click(function() {
        var url = $(this).attr('data-url');
        if($(this)){
          $.ajax({
              type: "POST",
              url:  url,
              success: function(response){
                $("#saveastemplateModal").modal('hide');
              },error:function(response){
                console.log('an error occured please try again !');
                
              }
         });
        } 
  });
  });
  scrollToBottom();
  /*$('.modify-description').on('click',function(){
    var data =  $('#descriptionTextArea').val();
    $('#descriptionTextArea').focus().val('').val(data);
    $('.description-error').hide();
    return false ;
  });
 
  /*$('#descriptionTextArea').keyup();
  
  $('#descriptionTextArea').on('focus',function(){
    oldDescription = $(this).val();
  });

  var clicky;

  $(document).mousedown(function(e) {
      // The latest element clicked
      clicky = $(e.target);
  });*/

  /*$('#descriptionTextArea').on('blur',function(){
    if (clicky[0].nodeName=='DIV'){
      $('.description-error').hide();
       var url = $(this).attr('data-link');
       var newDescription = $(this).val();
       if ( newDescription === ''){
        $('.modify-description').html('Ajouter description');
       } else {
        $('.modify-description').html('Change description');
       }
       //alert(url);
      $.ajax({
            url:url,
            dataType: "html",
            data: { textarea_description : newDescription },
            // beforeSend: function(){
            //   $('.dragbox#'+ id+' .dragbox-content').html('<br><br><br><img src="../../../img/ajax-loader.gif" style="margin-left: 50%;">');
            // },
            success: function(data){
           console.log(data);
           },error:function(){
            $('.description-error').show();
            $('#descriptionTextArea').val(oldDescription);
           }
      });
    }
  });*/

  $('#edit-description').on("click",function(e){
         e.preventDefault(); 
         $(this).hide();
         $("#submit-description").show();
         $("#descriptionTextArea").focus();
          
  });
  //validate Description
  $("#myFormDescription").submit(function(){
    $("#loadingDescription").show();
    //get the url for the form
    var url=$("#myFormDescription").attr("action");
    if($("#descriptionTextArea").val() == ""){
    $('#descriptionTextArea').focus();
    $("#loadingDescription").hide();
    return false;
    }
   //start send the post request
   $.post(url,{
       newDescription: $("#descriptionTextArea").val()
       
   },function(data){
    $("#loadingDescription").hide(); 
      if(data.responseCode==200 ){ 
         $("#edit-description").show();
         $("#submit-description").hide();
      }else if(data.responseCode==400 ){
         console.log('bad request');  
      }else{

          console.log('An unexpeded error occured.');    
      }
    });
   //we dont what the browser to submit the form
   return false;
  });


  $('body').on('click','a[id^=edit]',function(){
    console.log($(this).attr('data-link'));
    var url = $(this).attr('data-link') ;
    $container = $(this).parents('.dragbox-content') ;
     $(this).parents('.dragbox-content').html('<br><br><br><img src="../../../../img/ajax-loader.gif" style="margin-left: 50%;">');
     
    $container.load(url, function() {
      
    });
    
     console.log('after load ');
    return false ;
  });

  $('body').on('click','a[id^=deleteQuestion]',function(){
      var url = $(this).attr('href') ;
      $container = $(this).parents('.dragbox-content') ;
      $(this).parents('.dragbox-content').html('<br><br><br><img src="../../../../img/ajax-loader.gif" style="margin-left: 50%;">');
      $container.load(url, function() {
        
      });
      return false ;
  });

  $('body').on('click','a[id^=backDelete]',function(){
    var url = $(this).attr('data-link');
    var id = $(this).attr('data-id');
    if($(this).attr('data-id')){
      $.ajax({
      url:url,
          dataType: "html",
          beforeSend: function(){
            $('.dragbox#'+ id+' .dragbox-content').html('<br><br><br><img src="../../../../img/ajax-loader.gif" style="margin-left: 50%;">');
          },
          success: function(msg){
            console.log('received Message '+msg);
             $('.dragbox#'+ id).html(msg);
             $("#questions_div :input").attr("disabled", true);

          }
     });
    } 
    return false ;
  });

  //set the scrolling 
  if ($('#pos').html().trim() == 'BOTTOM'){
    $('html, body').animate({ 
     scrollTop: $(document).height()-$(window).height()}, 
     1400, 
     "easeOutQuint"
  );}
  

  $('.sortable').sortable();
  $('body').on('click','a.minimize',
    function(e){
      e.preventDefault();
      $(this).parent().siblings('.dragbox-content').toggle();
    });
    // minimize question
  $('body').on('click','a.minimize1',
    function(e){
      e.preventDefault();
      $(this).css('display','none');
      var id = $(this).attr('data-minimize');
      $('#maximize'+id).css('display','block');
      $(this).parent().parent().parent().parent().siblings('.dragbox-content').toggle();
      $(this).parent().parent().parent().siblings('.dragbox-content').toggle();
  });
  $('body').on('click','a.minimize2',
    function(e){
      e.preventDefault();
      $(this).css('display','none');
      var id = $(this).attr('data-maximize');
      $('#minimize'+id).css('display','block');
      $(this).parent().parent().parent().parent().siblings('.dragbox-content').toggle();
      $(this).parent().parent().parent().siblings('.dragbox-content').toggle();
  });
  //copy question
  $('body').on('click','a.cloneQuestion',function(){
    var url = $(this).attr('data-linkCopy');
    var idQuestion = $(this).attr('data-idQuestion');
    var idQuestionnaire = $(this).attr('data-idQuestionnaire');
    if ($(this).attr('data-idQuestion')) {
      $.ajax({
        url:url,
        async: true,
        dataType: "json",
        beforeSend: function(){
         // $('.dragbox#'+ idQuestion+' .dragbox-content').html('<br><br><br><img src="../../../../img/ajax-loader.gif" style="margin-left: 50%;">');
        },
        success: function(msg){
         
          $('#questions_div .column.ui-sortable').append(msg.Content);
          $("#questions_div :input").attr("disabled", true);
          scrollToBottom();
        }
      });
    } 
    return false ;
  });

  var from = 0;
  var to  = 0 ;
  $('.column').sortable({
    connectWith: '.column',
    handle: 'h2',
    cursor: 'move',
    placeholder: 'placeholder',
    forcePlaceholderSize: true,
    opacity: 0.4, 
    start: function(e, ui){
        ui.placeholder.height(ui.item.height());
        ui.item.startPos = ui.item.index();
        from = ui.item.startPos+1;
        console.log(from);
    },
    stop: function(event, ui)
    {
        to  = ui.item.index()+1;
        console.log(to);
        $(ui.item).find('h2').click();
        var sortorder='';
        var url= Routing.generate('questionnaire_cyclic_permutation', { id: ui.item.attr('data-id')  });
        /* setting the order of quetionnaire  */
         $.post(url,
         { from : (ui.item.startPos), 
          to: (ui.item.index()) },
         function(data) {
          console.log(data);
          if (! data.success){
            console.log('failure in permuting elements , some parameters are wrong :(')
          } else {
              // displaying new order in client side
                $('.questionOrder').each(function(index){
                  var myValue = $.trim($(this).html()) ;
                  if (from > to ) {
                    if(index+1 >= to && index <from ){
                      $(this).html((index+1));
                    }
                  }
                  else {
                    if(index+1 < to && index+1 >= from ){
                      $(this).html((myValue-1));
                    }
                    if (index+1 == to ){
                     $(this).html(to); 
                    }
                  }
                });
            }

          });
        /* end of setting order */
    }
  }).disableSelection();

  $("#loadingSend").hide();
  //update tittle btn 
  $('#update-button').on("click",function(e){
         e.preventDefault(); 
         $(this).hide();
         $("#validate-button").show();
         $("#input_new_name").focus();
          
  });
  $("#myFormModify").submit(function(){
  $("#loading").show(); 
  //get the url for the form
  var url=$("#myFormModify").attr("action");
  if($("#input_new_name").val() == ""){
    /*$('#input_new_name').val($('#input_new_name').val() + 'Add title');*/
    $('#input_new_name').focus();
    $("#loading").hide();
    return false;
   }
   //start send the post request
   $.post(url,{
       newName:$("#input_new_name").val()
       
   },function(data){
   //the response is in the data variable
    $("#loading").hide(); 
      if(data.responseCode==200 ){ 
  
        /* $("#questionnaireTitle").html("<a href='#' onclick='swap();' title='click modify' id='aQuestionnaireTittle'> "+
              $.trim(data.newName) +"</a>" + '<span href="javascript:void(0)" onclick="swap();" rel="tooltip" title="click to modify" class="Questionnaire_title_edit"></span>');*/
        /*$("#questionnaireTitle").html("<a href='#' onclick='swap();' title='click modify' id='aQuestionnaireTittle'> "+
              $.trim(data.newName) +"</a>");
        $("#questionnaireTitle").show();*/

        //$('#hidden-input').hide();
         $("#update-button").show();
         $("#validate-button").hide();
         if ( $('#questions_div').children().length > 0 ) {
              $('#send_btn').removeAttr('disabled');
              $('#preview_btn').removeAttr('disabled');
              $('#save_tmp').removeAttr('disabled');
          }
      }else if(data.responseCode==400 ){
         console.log('bad request');  
      }else{

          console.log('An unexpeded error occured.');    
      }
    });
   //we dont what the browser to submit the form
   return false;
  });

  //disable all inputs 
  $("#questions_div :input").attr("disabled", true);
});

// ajax editing of questions 
$('body').on('click','a[id^=cancelBtn]',function(){
  var url = $(this).attr('data-link');
  var id = $(this).attr('data-id');
  if($(this).attr('data-id')){
  $.ajax({
  url:url,
      dataType: "html",
      beforeSend: function(){
        $('.dragbox#'+ id+' .dragbox-content').html('<br><br><br><img src="../../../../img/ajax-loader.gif" style="margin-left: 50%;">');
      },
      success: function(msg){
        //console.log('received Message '+msg);
         $('.dragbox#'+ id).html(msg);
          $("#questions_div :input").attr("disabled", true);
      }
 });
 } 
  return false ;
});

$('body').on('submit','form:not(#form_description)',function(){
 var locale = document.getElementById("locale").value;
 var url = $(this).attr('action');
 var id = $(this).attr('id');
 var isDeleteForm  = ($(this).attr('data-type')=='delete');
 $.ajax({
  url:url,
  type:'POST',
  data: $(this).serialize(),
      dataType: "json",
      beforeSend: function(){
        $('.dragbox#'+ id+' .dragbox-content').html('<br><br><br><img src="../../../../img/ajax-loader.gif" style="margin-left: 50%;">');
      },
      success: function(data){
       if (isDeleteForm ){
              var idQuestion = id;
              $('.dragbox#'+ id).animate({
                  height:'toggle'}, function(){
                    $('.dragbox#'+ id).remove();
                      if(data.isLast === true){
                        if(locale=="ar"){
                          $('#questions_div .column').html('<div class="divempty alert alert-info" role="alert" style="margin-top:2%;">إختر نوع السؤال الذي تريد من القائمة في اليمين لتتمكن من اضافة السؤال</div>');
                        }else if(locale=="fr")
                        {
                          $('#questions_div .column').html('<div class="divempty alert alert-info" role="alert" style="margin-top:2%;">Cliquer sur le type de question de votre choix dans la liste à gauche pour ajouter une question</div>');
                        }else{
                          $('#questions_div .column').html('<div class="divempty alert alert-info" role="alert" style="margin-top:2%;">Your survey is empty. Choose an item from questions menu types to add new question</div>');
                        }
                        $('#send_btn').attr("disabled", true);
                        $('#preview_btn').attr("disabled", true);
                        $('#save_tmp').attr("disabled", true);
                      }
                  }
                );
              var orderQuestion = $('.dragbox#'+ idQuestion).attr('data-order');
              $('.questionOrder').each(function(){
                console.log('this: '+$(this).html());
                console.log('current order : '+orderQuestion);
                if ($.trim($(this).html()) > orderQuestion) {
                  $(this).html(($.trim($(this).html())-1));
                }
              })
        
       } else {
        $('.dragbox#'+ id).html(data.content);
        $("#questions_div :input").attr("disabled", true);
       }
         
      },
      error:function(){
        $('.dragbox#'+ id+' .dragbox-content').html('<div class="alert alert-danger description-error" style="display:block">An error occured :(  </div>');
      }
 });
 return false ;
});

function textAreaAdjust(o) {
    o.style.height = "1px";
    o.style.height = (10+o.scrollHeight)+"px";
}

function scrollToBottom() {
  $('html, body').animate({scrollTop:$(document).height()}, 'slow');
}

