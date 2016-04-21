$(document).ready(function() {
var locale = document.getElementById("locale").value;

// setting the default error mesaage 
$.validator.messages.required = 'The answer to this question is incomplete';

$('.sortable').sortable();
      $('#respendant').validate({
          //ignore: [],
          focusInvalid: false,
          invalidHandler: function(form, validator) {

              if (!validator.numberOfInvalids())
                  return;

              $('html, body').animate({
                  scrollTop: $(validator.errorList[0].element).offset().top
              }, 2000);

          },
          errorPlacement: function (error, element) {
            if (element.is("input:radio#rating")) {
                $(element).closest('tr').next().find('.error_label').html(error);
            }else if (element.is("input:radio.ranking")) {
                var elId = $(element).attr('id');
                $('#rankingLbl'+elId).text("");
                $('#rankingLbl'+elId).append(error);
                //element.after(error);
                //$(element).closest('tr').next().find('.error_label').html(error);
            }else if (element.is("input:radio#multipleChoiceRadio")) {
                error.insertAfter(element.closest('ul.grp_radio'));
                error.addClass('error_class');
                //error.appendTo( element.parents('ul.grp_radio') );
            }else if (element.is("input:checkbox#multipleChoiceCheckbox")) {
                error.insertAfter(element.closest('ul.grp_radio'));
                error.addClass('error_class');
            }else if (element.is("select")) {
                 error.insertAfter(element.closest('select'));
                 error.addClass('error_class');
                
            }

            else{
              //element.parents("div:first").prepend(error);
               error.insertAfter(element);
               error.addClass('error_class');
              //error.insertBefore(element.parent().next('div').children().first());
            }

          }    
    });
  $('#respendant').preventDoubleSubmission(); 
  });

// jQuery plugin to prevent double submission of forms
jQuery.fn.preventDoubleSubmission = function() {
  $(this).on('submit',function(e){
    var $form = $(this);

    if ($form.data('submitted') === true) {
      // Previously submitted - don't submit again
      e.preventDefault();
    } else {
      // Mark it so that the next submit can be ignored
     if ($form.valid()){
      $form.data('submitted', true);
     }
      
    }
    console.log('submit exected ');
  });

  // Keep chainability
  return this;
};