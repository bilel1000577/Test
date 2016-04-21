$(document).ready(function() {

// setting the default error mesaage 
$.validator.messages.required = 'La réponse à cette question est incomplète';
$.validator.addMethod("cRequired", $.validator.methods.required,'La réponse à cette question est incomplète');
$.validator.addClassRules("matrix", { cRequired: true });
	$('.sortable').sortable();
      $('#respendant').validate({

           errorPlacement: function (error, element) {
      
        /*console.log(element.parents("div:first").attr("class"));    
        element.parents("div:first").prepend(error);*/
        if (element.is("input:radio")) {
            element.after(error);
            //console.log(i);
        }else{
          element.parents("div:first").prepend(error);
        }

    }
      });
  });