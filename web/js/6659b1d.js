$('document').ready(function (){
	
	$('h8 a').click(function() {
		//console.log($(this).text());
		$('#templates_detail h7').html($(this).text());
	});
}) ;


$('document').ready(function (){

    $('#accordion2').accordion({
                                header: 'fieldset',
                                disabled: false
                        });
});