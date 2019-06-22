function notify(message_type, message) {
  Lobibox.notify(message_type, {
    position: 'right top',
    msg: message
  });
}

function delete_provider(data) {
  console.log(data);
  data = JSON.parse(data.responseText);
  console.log(data);

  if(data.success_message){
    notify( 'success', data.success_message);
    $('.provider_'+data.record_id).remove();
  }
  if(data.error_message)
    notify( 'error', data.error_message);
}

function render_new_provider(data) {
  console.log(data);
  data = JSON.parse(data.responseText);
  console.log(data);
  
  if(data.success_message){
    notify( 'success', data.success_message);
    is_already_exist = $('.provider_'+data.record_id)[0]
    // get_provider_feeds(data.record_id);
    if(is_already_exist) {
      new_provider_div(data.record_id, data.record_name, data.record_url);
      $('.provider_'+data.record_id+' ul').preloader('remove');
    }
    else {
      new_provider_div(data.record_id, data.record_name, data.record_url);
      import_webfeed(data.record_id);
    }
    
    resetWebFeedForm(); 
    $('.modal').modal('hide');
  }
  if(data.error_message)
    notify( 'error', data.error_message);
}

function get_providers(){
  request_to_server('get', 'api.php', {'method': 'get_providers', 'api_key': $('#home').data('api_key')}, 'render_providers');
}

function render_providers(data){
  console.log(data);
  simplified_data = JSON.parse(data.responseText).data;
  if(simplified_data.length > 0) {
    $.each(simplified_data, function (index, provider) {
      new_provider_div(provider.id, provider.name, provider.url);
    });
  }
  else {
    $('#prividersList').append("<h3 class='no_web_feeds'>No Web Feeds found!</h3>");
  }

  $.each(simplified_data, function (index, provider) {
    get_provider_feeds(provider.id);
  });
}

function new_provider_div(provider_id, provider_name, provider_url) {
  $('.no_web_feeds').remove();
  if($('.provider_'+provider_id)[0])
    newDiv = $('.provider_'+provider_id)
  else {
    newDiv = $('.newsHubProviderArea #hiddenArea').clone().appendTo( '.newsHubProviderArea #prividersList' );
    $(newDiv).find('.providerWebFeedArea li').html('');
  }

  $(newDiv).addClass('provider_'+provider_id);
  $(newDiv).data('record_id', provider_id);
  $(newDiv).removeAttr('id');  
  $(newDiv).find('.provider_name').html(provider_name);
  $(newDiv).find('.provider_name').data('full_name', provider_name);
  $(newDiv).find('.webFeedActions .external_provider_link').attr('href', provider_url);  
  // $(newDiv).find('.webFeedActions .sync_provider_data').attr('href', "api.php?id="+provider_id+"&method=sync_provider");  
  $(newDiv).find('ul').preloader();
  $(newDiv).removeClass('hidden');
}

function get_provider_feeds(provider_id) {
  request_to_server('get', 'api.php', {'method': 'get_provider_feeds', 'api_key': $('#home').data('api_key'), 'provider_id': provider_id}, 'render_provider_feeds');
}

function import_webfeed(provider_id){
  request_to_server('get', 'api.php', {'method': 'import_new_webfeed', 'api_key': $('#home').data('api_key'), 'provider_id': provider_id}, 'render_provider_feeds');
}

function render_provider_feeds(data) {
  console.log(data);
  adata = JSON.parse(data.responseText);
  console.log(adata);
  if(adata.error_message) {
    notify( 'error', adata.error_message);
    $('.prividersListArea ul').preloader('remove');
  }
  else{
    last_updated_provider_id = 0;
    simplified_data = JSON.parse(data.responseText).data;
    $.each(simplified_data, function (index, provider_feed) {
      last_updated_provider_id = provider_feed.provider_id;
      newDiv = $('.newsHubProviderArea #hiddenArea .providerWebFeedArea li').clone().appendTo( '.provider_'+provider_feed.provider_id+' .providerWebFeedArea' );
      $(newDiv).find('.title').html(provider_feed.title);
      $(newDiv).find('.newsActions .external_provider_feed_link').attr('href', provider_feed.url);  
      $(newDiv).find('.newsActions .edit_provider_feed_data').attr('href', "api.php?id="+provider_feed.id+"&method=edit_provider_feed");  
      $(newDiv).find('.newsActions .delete_provider_feed_data').attr('href', "api.php?id="+provider_feed.id+"&method=delete_provider_feed");  
    });
    console.log('Id is : '+last_updated_provider_id);
    if(last_updated_provider_id == 0)
      $('.prividersListArea ul').preloader('remove');
    else
      $('.provider_'+last_updated_provider_id+' ul').preloader('remove');
      $('.provider_'+last_updated_provider_id+' ul').mCustomScrollbar("destroy");
      $('.provider_'+last_updated_provider_id+' ul').mCustomScrollbar();
  }
}

// function update_provider(data) {
//   console.log(data);
//   simplified_data = JSON.parse(data.responseText);
//   console.log(simplified_data);
// }

function request_to_server(request_type, url, data, render_method){
  $.ajax({
    type: request_type,
    url: url,
    data: data,
    dataType: 'application/json',
    success: function(resultData) { 
      // eval(render_method+"("+resultData+")");
      // resultData = $.parseJSON(JSON.parse(resultData));
      // console.log('Success: '+resultData); 
      // console.log('Success: '+resultData.success_message); 
      // console.log('Error: '+resultData.error_message);
      
      // if(resultData.success_message)
      //   notify( 'success', resultData.success_message);
      // if(resultData.error_message)
      //   notify( 'error', resultData.error_message);
      window[render_method](errorData);
    },
    error: function(errorData) { 
      // eval(render_method+"("+errorData+")");
      window[render_method](errorData);
      // const func1 = new Function(render_method);
      // func1(errorData);
      // console.log("Error: "+ errorData);
      // console.log(JSON.stringify(errorData)); 
      // console.log("Error: "+ JSON.parse(errorData.responseText).error_message); 
    }
  })
}

function resetWebFeedForm() {
  $('#exampleModalCenter h3.modal-title').html('New Web Feed');
  // $('#newWebFeedForm').find('input').val(''); 
  $('#exampleModalCenter form #name').val('');
  $('#exampleModalCenter form #feedUrl').val('');
  $('#exampleModalCenter form #provider_id').val('');
  // $('#exampleModalCenter form #submitButton').val('newWebFeedForm');
  // $('#exampleModalCenter form #method').val('newFeed');
}

function getConfiguration() {
  request_to_server('GET', 'api.php', {'method': 'getConfiguration', 'api_key': $('#home').data('api_key')}, 'setConfiguration');
}

function setConfiguration(data) {
  console.log(data);
  data = JSON.parse(data.responseText).data;
  $('form#configurationForm  #keep_until').val(data.keep_until);
  $('form#configurationForm #time_interval').val(data.update_gap);
}

function updateConfiguration(data) {
  console.log(data);
  data = JSON.parse(data.responseText);
  console.log(data);
  if(data.success_message){
    notify( 'success', data.success_message);
    $('.modal').modal('hide');
  }
  if(data.error_message)
    notify( 'error', data.error_message);
}

jQuery(document).ready(function () {
  get_providers();
  getConfiguration();
  $('body').on('click', '#new_web_feed_submit', function(){
    console.log('Submit clicked');

    request_to_server(  'post', 
                        'api.php', 
                        $('#newWebFeedForm').serialize(), 
                        'render_new_provider'
                      ); 
  });

  $('body').on('click', '.edit_provider_data', function(){
    console.log('Edit Provider');
    parent_div = $(this).closest('.prividersListArea')
    console.log($(parent_div).find('.provider_name').data('full_name'));
    $('#exampleModalCenter form #name').val($(parent_div).find('.provider_name').data('full_name'));
    $('#exampleModalCenter form #feedUrl').val($(parent_div).find('.external_provider_link').attr('href'));
    $('#exampleModalCenter form #provider_id').val($(parent_div).data('record_id'));
    $('#exampleModalCenter h3.modal-title').html('Update Web Feed');
    $('#exampleModalCenter').modal('show');
    // request_to_server(  'post', 
    //                     'api.php?id='+$(parent_div).data('record_id'), 
    //                     $('#newWebFeedForm').serialize(), 
    //                     'update_provider'
    //                   ); 
  });

  $('body').on('hidden.bs.modal', '#exampleModalCenter', function(){
    resetWebFeedForm();
  });

  $('body').on('click', '.delete_provider_data', function(){
    record_id = $(this).closest('.prividersListArea').data('record_id');

    dialog.confirm({
      title: "Delete Web Feed",
      message: "Are you sure you want to delete this web feed?",
      cancel: "No",
      button: "Yes",
      required: true,
      callback: function(value){
        if(value)
          request_to_server('POST', 'api.php', {'method': 'delete_provider', 'api_key': $('#home').data('api_key'), 'id': record_id}, 'delete_provider');
      }
    });
  });

  $('body').on('click', '.sync_provider_data', function(){
    import_webfeed($(this).closest('.prividersListArea').data('record_id'));
  });

  $('body').on('click', '#configurationFormSubmit', function(){
    console.log('Configuration Update');
    // import_webfeed($(this).closest('.prividersListArea').data('record_id'));
    request_to_server('POST', 'api.php', $('#configurationForm').serialize(), 'updateConfiguration');
  });
});