jQuery(document).ready(function () {

  $('body').on('click', '#new_web_feed_submit', function(){
    console.log('Submit clicked');
    
    $.ajax({
      type: 'POST',
      url: "api.php",
      data: $('#newWebFeedForm').serialize(),
      dataType: "script",
      success: function(resultData) { 
        // console.log('Success: '+JSON.stringify(resultData));
        // resultData = JSON.stringify(resultData);
        resultData = $.parseJSON(JSON.parse(resultData));
        console.log('Success: '+resultData); 
        console.log('Success: '+resultData.success_message); 
        console.log('Error: '+resultData.error_message); 
      },
      error: function(errorData) { 
        console.log("Error: "+ errorData);
        console.log(JSON.stringify(errorData)); 
        console.log("Error: "+ JSON.parse(errorData.responseText).error_message); 
      }
    }); 
  });

});