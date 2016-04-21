$(document).ready(function(){
        $('#freeReport').submit(function(e) {
            e.preventDefault();
            $("#loading").show(); 
            if ($(this)) {
                $.ajax({
                    type: "POST",
                    url:  $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function(data) {
                        $("#loading").hide();
                        $(".messagesuccess").show(); 
            
                        $(".messagesuccess").delay(1500).fadeOut();

                        setTimeout(function(){
                            $("#addFreeCustumer").modal('hide')
                        }, 1500);
        
                    },
                    error:function(jqXHR , status, error){
                      var responseText = jQuery.parseJSON(jqXHR.responseText);
                      $.each(responseText, function(key, value) {
                          $(".messageerror").html(key + ' : ' + value);
                         
                      });
                      $("#loading").hide(); 
                      $(".messageerror").show(); 
                      $(".messageerror").delay(1500).fadeOut();   
                    }
                });
            }
            return false;
      });

      $('#paidReport').submit(function(e) {
            e.preventDefault();
            $("#loading").show(); 
            if ($(this)) {
                $.ajax({
                    type: "POST",
                    url:  $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function(data) {
                        $("#loading").hide(); 
                        $(".paidmessagesuccess").show(); 
            
                        $(".paidmessagesuccess").delay(1500).fadeOut();

                        setTimeout(function(){
                            $("#addPaidCustumer").modal('hide')
                        }, 1500);
        
                    },
                    error:function(jqXHR , status, error){
                      var responseText = jQuery.parseJSON(jqXHR.responseText);
                      $.each(responseText, function(key, value) {
                          $(".paidmessageerror").html(key + ' : ' + value);
                         
                      });
                      $("#loading").hide(); 
                      $(".paidmessageerror").show(); 
                      $(".paidmessageerror").delay(1500).fadeOut();   
                    }
                });
            }
            return false;
      });

      $('#add_free').attr('disabled','disabled');  
      var checkboxes = $(".reportChecked"),
          addFree = $("#add_free");

      checkboxes.click(function() {
          addFree.attr("disabled", !checkboxes.is(":checked"));
      });

      $('#add_paid').attr('disabled','disabled');  
      var checkboxesp = $(".paidReport"),
          addPaid = $("#add_paid");

      checkboxesp.click(function() {
          addPaid.attr("disabled", !checkboxesp.is(":checked"));
      });
 });

 $(document).on("click", "#addFreeCustumer", function () {
  var chkArray = [];
  $(".reportChecked:checked").each(function() {
      chkArray.push($(this).val());
  });
  var selected;
  selected = chkArray.join(',');
  /* check if there is selected checkboxes, by default the length is 1 as it contains one single comma */
  if(selected.length > 1){
      
      $(".modal-body #attached_report").val( selected );

  }else{
      alert("Sélectionner le(s) noms des rapports à envoyer ");
  }
   
  });

 $(document).on("click", "#addPaidCustumer", function () {
  var chkArray = [];
  $(".paidReport:checked").each(function() {
      chkArray.push($(this).val());
  });
  var selected;
  selected = chkArray.join(',');
  /* check if there is selected checkboxes, by default the length is 1 as it contains one single comma */
  if(selected.length > 1){
      
      $(".modal-body #attached_report").val( selected );
  }else{
        alert("Sélectionner le(s) noms des rapports à commander ");
  }
   
  });

 /* $(document).on("click", ".open-free-report", function () {
   var report = $(this).data('id');
   $(".modal-body #attached_report").val( report );
  });

  $(document).on("click", ".open-free-report1", function () {
   var report = $(this).data('id');
   $(".modal-body #attached_report").val( report );
  });*/
//google analytics script
/*(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-53632559-1', 'auto');
    ga('send', 'pageview');*/