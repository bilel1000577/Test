/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function validate_form(){
//$("label[for='fos_user_registration_form_plainPassword_second']").html("Retype Password:");
$('#signupform').validate(
	{
	rules: { 
		"fos_user_registration_form[username]" : {
			required: true,
			minlength: 4
		},
		'fos_user_registration_form[plainPassword][first]':{
			required:true,
			minlength : 6
		},
		'fos_user_registration_form[plainPassword][second]':{
			required   : true ,
			minlength  : 6
		}	
	}/*,
	messages: {
		"fos_user_registration_form[username]" : {
			required: "Username could not be empty",
			minlength : "Username must  contain  at least  4 caracters"
		},
		"fos_user_registration_form[plainPassword][first]":{
			required:"Password is required",
			minlength : "Password should contain at least 6 characters "
		},

		"fos_user_registration_form[plainPassword][second]":{
			required:"Password confirmation is required",
			minlength :" Password should be at least 6 characters " 
			
		}

	}*/
	});
}

