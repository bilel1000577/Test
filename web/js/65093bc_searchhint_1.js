/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 $(document).ready(function() {
	 questionnaireName = '';
	 $('input[type=submit]', $('#copyForm')).attr('disabled','disabled');
	 $("#inputNewQuestionnaire").prop('disabled', true);
	 
			//alert((questionnaireName === undefined) );
	 
                        autocomplete(Routing.generate("quizmoo_questionnaire_search"),"questionnaire_copy");
                        $('#accordion').accordion({
                                header: 'fieldset',
                                disabled: false
                        });
			
			
			$.validator.addMethod("checkIfHasModifiedQuestionnaireName",function (value , element) {
				
				return  (questionnaireName !== $('#inputNewQuestionnaire').val() );
				
			}, "Two questionnaires could not have the same name");
			
			$('#copyForm').validate({
				rules: {
					inputNewQuestionnaire : {
						
						checkIfHasModifiedQuestionnaireName: true
					}
					
				}
			});
			
                });
		



function autocomplete(url,testurl){	
		var creationTimes;
	var modificationTimes;
	var titles;
	var ids;


    $("#searchQuestionnaire").typeahead({
        minLength: 1,
        source: function(query, process) {
           return  $.get(url,
	  		 { q:query , limit: 8 },
	   		 function(data) {
				 
				 ids			= data.ids;
				 creationTimes		= data.creationTimes ;
				 modificationTimes	= data.modificationTimes;
				 titles			= data.titles;
				 
              			 return  process(data.titles);
            
	    		});
			
        }
	 ,
			updater: function (item) {
				$("#inputNewQuestionnaire").prop('disabled', false);
				
				questionnaireName = item;
				
				if ($("#timeCreation").length ){
					$("#timeCreation").html(creationTimes[titles.indexOf(item)]);
				}
				
				if ($("#timeModification").length){
				$("#timeModification").html(modificationTimes[titles.indexOf(item)]);
				}
				
				if($('#title').length){
						$('#title').html(item);
				}
				
					
				if ($('#inputNewQuestionnaire').length){
				$('#inputNewQuestionnaire').val(item);
				
				}
				
				var id = ids[titles.indexOf(item)] ;
				var newQuestionName = $('#inputNewQuestionnaire').val();
				var route = Routing.generate(testurl,{'id':id, 'name':newQuestionName});
				$('#copyForm').attr('action',route);

				
				
				if ($('#description_holder').length){
					$('#description_holder').html($('#description').html());
				}
				
				$('#copyForm input[type=submit]').removeAttr('disabled');
				
				
   } 
  }); 
}
