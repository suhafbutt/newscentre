is_sync_clicked = false;
function notify(message_type, message) {
  Lobibox.notify(message_type, {
    position: 'right top',
    msg: message
  });
}

function date_format(date, format) {
  if(date)
    return $.format.date(date, format);
  else
    return '';
}

function delete_provider(data) {
  console.log(data);
  data = JSON.parse(data.responseText);
  console.log(data);

  if(data.success_message){
    notify( 'success', data.success_message);
    if($('#providerDetailView').length > 0) {
      window.location.replace('/newscentre/index.php');
    }
    else {
      $('.provider_'+data.record_id).remove();
    }
  }
  if(data.error_message)
    notify( 'error', data.error_message);
}

function render_new_provider(data) {
  console.log(data);
  data = JSON.parse(data.responseText);
  console.log(data);
  
  if(data.success_message){
    if($('#providerDetailView').length > 0) {
      $('#providerDetailView .header_section .heading').html(data.record_name);
      $('#providerDetailView .header_section .heading.provider_name').data('full_name', data.record_name);
    }
    else {
      is_already_exist = $('.provider_'+data.record_id)[0]
      // get_provider_feeds(data.record_id);
      if(is_already_exist) {
        is_provider_disable = ($('.provider_651').find('.webFeedActions .provider_disable_link:visible').data('disable_message') == 'enable');
        new_provider_div(data.record_id, data.record_name, data.record_url);
        $('.provider_'+data.record_id+' ul').preloader('remove');
      }
      else {
        new_provider_div(data.record_id, data.record_name, data.record_url);
        import_webfeed(data.record_id);
      }
    }
    
    notify( 'success', data.success_message);
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
      new_provider_div(provider.id, provider.name, provider.external_url);
      update_provider_actions(provider.id, provider.is_disable);
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

  if($('.provider_'+provider_id)[0]){
    newDiv = $('.provider_'+provider_id);
  }
  else {
    newDiv = $('.newsHubProviderArea #hiddenArea').clone().appendTo( '.newsHubProviderArea #prividersList' );
    $(newDiv).find('.providerWebFeedArea li').html('');
    $(newDiv).addClass('provider_'+provider_id);
  }

  $(newDiv).data('record_id', provider_id);
  $(newDiv).removeAttr('id');  
  $(newDiv).find('.provider_name').html(provider_name);
  $(newDiv).find('.provider_name').data('full_name', provider_name);
  set_provider_actions_attributes(newDiv, provider_url, provider_id);
  $(newDiv).find('ul').preloader();
  $(newDiv).removeClass('hidden');
}

function set_provider_actions_attributes(elem, provider_url, provider_id) {
  $(elem).find('.webFeedActions .view_provider_link').attr('href', 'provider.php?api_key='+$('#home').data('api_key')+'&provider_id='+provider_id);
  $(elem).find('.webFeedActions .external_provider_link').attr('href', provider_url);
  $(elem).find('.webFeedActions .export_provider_xml').attr('href', 'api.php?api_key='+$('#home').data('api_key')+'&provider_id='+provider_id+'&method=exportProvider');
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
    simplified_data= JSON.parse(data.responseText).data;
    to_be_updated_provider_id = 0;
    if(simplified_data.length > 0){
      console.log(simplified_data);
      to_be_updated_provider_id = simplified_data[0].provider_id
      $('.provider_'+to_be_updated_provider_id).find('.providerWebFeedArea li').remove();
      console.log(simplified_data[0].provider_id);
    }
    else {
      console.log('qwertyuioiuytr');
      console.log('.provider_'+adata.provider_id);
      $('.provider_'+adata.provider_id).find('.providerWebFeedArea li').remove();
    }
    $.each(simplified_data, function (index, provider_feed) {
      newDiv = $('.newsHubProviderArea #hiddenArea .providerWebFeedArea li').clone().appendTo( '.provider_'+provider_feed.provider_id+' .providerWebFeedArea' );
      $(newDiv).addClass('provider_post_'+provider_feed.id);
      render_provider_post(newDiv, provider_feed);
    });
    if(to_be_updated_provider_id != 0) {
      $('.provider_'+to_be_updated_provider_id+' ul').preloader('remove');
      $('.provider_'+to_be_updated_provider_id+' ul').mCustomScrollbar("destroy");
      $('.provider_'+to_be_updated_provider_id+' ul').mCustomScrollbar();
    }
    else
      $('.prividersListArea ul').preloader('remove');

    if(adata.success_message && is_sync_clicked) {
      notify( 'success', adata.success_message);
      is_sync_clicked = false;
    }
  }
}

function render_provider_feeds_for_view(data) {
  console.log(data);
  adata = JSON.parse(data.responseText);
  console.log(adata);
  // console.log(adata.provider);
  if(adata.error_message) {
    notify( 'error', adata.error_message);
    // $('.prividersListArea ul').preloader('remove');
  }
  else{
    simplified_data= JSON.parse(data.responseText).data;
    if(simplified_data.length < 1){
      console.log('qwertyuioiuytr');
      console.log('.provider_'+adata.provider_id);
      $('#provider_view_feed_table tbody').find('tr').remove();
      $('#provider_view_feed_table tbody').html('<tr><td>No Data found!</td></tr>');
    }
    render_feed_data_in_table(simplified_data);
    render_provider_view(data);
    if(adata.success_message && is_sync_clicked) {
      notify( 'success', adata.success_message);
    }
  }
}

function render_provider_post(newDiv, provider_feed) {
  $(newDiv).find('.title').html(provider_feed.title);
  $(newDiv).find('.title').data('full_title', provider_feed.title);
  $(newDiv).find('.title').data('description', provider_feed.description);
  $(newDiv).find('.published_date').html(provider_feed.publish_date);
  $(newDiv).find('.created_at').html(provider_feed.created_at);
  $(newDiv).data('record_id', provider_feed.id);
  if(provider_feed.url)
    $(newDiv).find('.newsActions .external_provider_feed_link').attr('href', provider_feed.url);  
  // $(newDiv).find('.newsActions .edit_provider_feed_data').attr('href', "api.php?id="+provider_feed.id+"&method=edit_provider_feed");  
  // $(newDiv).find('.newsActions .delete_provider_feed_data').attr('href', "api.php?id="+provider_feed.id+"&method=delete_provider_feed");
  if($(newDiv).closest('.prividersListArea').find('.provider_disable_link.enable_icon:visible').length > 0) {
    $(newDiv).closest('.providerWebFeedArea').find('a:not(.view_link)').addClass('hidden');
    $(newDiv).closest('.providerWebFeedArea').find('.turncate_to.title').addClass('provider_feed_title_width_disable'); 
  }
}

function request_to_server(request_type, url, data, render_method){
  $.ajax({
    type: request_type,
    url: url,
    data: data,
    dataType: 'application/json',
    success: function(resultData) { 
      window[render_method](errorData);
    },
    error: function(errorData) { 
      window[render_method](errorData);
    }
  })
}

function resetWebFeedForm() {
  $('#exampleModalCenter h3.modal-title').html('New Web Feed');
  $('#exampleModalCenter form #name').val('');
  $('#exampleModalCenter form #feedUrl').val('');
  $('#exampleModalCenter form #provider_id').val('');
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

function updateFeedPost(data) {
  console.log(data);
  data = JSON.parse(data.responseText);
  console.log(data);
  console.log(data.id);
  console.log($('.provider_post_'+data.id)[0]);
  if(data.success_message){
    if($('#providerDetailView').length > 0) {
      update_provider_post_table(data);
    }
    else {
      render_provider_post($('.provider_post_'+data.id), data);
    }

    notify( 'success', data.success_message);
    $('#providerFeed').modal('hide');
  }
  if(data.error_message)
    notify( 'error', data.error_message);
}

function update_provider_post_table(data) {
  elem_tr = $('#provider_view_feed_table tbody').find('.probider_post_'+data.id);
  $(elem_tr).find('td:nth-child(2)').html(data.title);
  $(elem_tr).find('td:nth-child(3)').html(data.description);
}

function delete_provider_post(data) {
  console.log(data);
  data = JSON.parse(data.responseText);
  console.log(data);

  if(data.success_message){
    notify( 'success', data.success_message);
    if($('#providerDetailView').length > 0) {
      $('#provider_view_feed_table tbody').find('.probider_post_'+data.record_id).remove();
    }
    else {
      $('.provider_post_'+data.record_id).remove();
    }
  }
  if(data.error_message)
    notify( 'error', data.error_message);
}

function get_provider_view_data() {
  request_to_server('get', 'api.php', {'method': 'get_provider', 'api_key': $('#home').data('api_key'), 'provider_id': $('#home').data('record_id')}, 'render_provider_view');
  request_to_server('get', 'api.php', {'method': 'get_provider_feeds', 'api_key': $('#home').data('api_key'), 'provider_id': $('#home').data('record_id')}, 'render_provider_feeds_table');
}
function render_provider_view(data) {
  console.log(data);
  data = JSON.parse(data.responseText);
  console.log(data);
  console.log(data.provider);
  console.log(data.provider.name);
  $('#providerDetailView .header_section.prividersListArea').data('record_id', data.provider.id);;
  $('#providerDetailView .header_section .heading').html(data.provider.name);
  $('#providerDetailView .header_section .heading.provider_name').data('full_name', data.provider.name);
  $('#providerDetailView .header_section .provider_source_name').html(data.provider.original_name);
  $('#providerDetailView .header_section .last_successfull_update').html(date_format(data.provider.lastest_successful_update, 'ddd MMM dd, yyyy'));
  $('#providerDetailView .header_section .last_update_attempt').html(date_format(data.provider.last_update_attempt, 'ddd MMM dd, yyyy'));
  $('#providerDetailView .header_section .last_update_attempt_response').html(data.provider.last_attempt_response);
  $('#providerDetailView .header_section .last_attempt_response').html(data.provider.lastest_successful_update);
  $('#providerDetailView .header_section .latest_record_date').html(date_format(data.provider.latest_record, 'ddd MMM dd, yyyy'));
  set_provider_actions_attributes($('#providerDetailView .header_section'), data.provider.external_url, data.provider.id);
  update_provider_actions(data.provider.id , data.provider.is_disable)
}

function render_provider_feeds_table(data){
  console.log(data);
  adata = JSON.parse(data.responseText);
  console.log(adata);
  if(adata.error_message) {
    notify( 'error', adata.error_message);
  }
  else{
    simplified_data= JSON.parse(data.responseText).data;
    to_be_updated_provider_id = 0;
    
    if(simplified_data.length > 0){
      console.log('Whaaaaaat'+simplified_data.length);
      to_be_updated_provider_id = simplified_data[0].provider_id
      $('.provider_'+to_be_updated_provider_id).find('.providerWebFeedArea li').remove();
      console.log(simplified_data[0].provider_id);
    }
    else {
      console.log('qwertyuioiuytr');
      console.log('.provider_'+adata.provider_id);
      $('#provider_view_feed_table tbody tr').remove();
      $('#provider_view_feed_table tbody').html('<tr><td>No Data found!</td></tr>');
    }
    
    render_feed_data_in_table(simplified_data);
    if($('.newsHubProviderArea .provider_'+adata.provider_id+' .provider_disable_link.enable_icon:visible').length > 0){
      $('#provider_view_feed_table td:last-child a:not(.view_link)').addClass('hidden');
    }
    if(adata.success_message && is_sync_clicked) {
      notify( 'success', adata.success_message);
      is_sync_clicked = false;
    }
  }
}

function render_feed_data_in_table(simplified_data) {
  $('#provider_view_feed_table tbody').find('tr').remove();
  i = 1;
  $.each(simplified_data, function (index, provider_feed) {
    trData = tr_string_for_table_entry(provider_feed, i);
    if($('#provider_view_feed_table tbody tr').length == 0){
      $('#provider_view_feed_table tbody').html(trData);
    }
    else {
      $('#provider_view_feed_table tbody tr:last').after(trData);
    }
    i+=1;
  });
}

function tr_string_for_table_entry(provider_feed, i) {
  return "<tr class='probider_post_"+provider_feed.id+"'> \
    <td> "+i+" </td>\
    <td data-record_id="+provider_feed.id+"> "+provider_feed.title+" </td>\
    <td> "+provider_feed.description+" </td>\
    <td> "+provider_feed.publish_date+" </td>\
    <td> "+provider_feed.created_at+" </td>\
    <td> <div class='newsActions pull-right'>\
      <a href='JavaScript:void(0);' class='view_link view_provider_feed_link'>\
        <i class='fas fa-eye'></i>\
      </a>\
      <a href='"+provider_feed.url+"' class='external_provider_feed_link' target='_blank'>\
        <i class='fas fa-external-link-square-alt'></i>\
      </a>\
      <a href='JavaScript:void(0);' class='edit_provider_feed_data'>\
        <i class='fas fa-edit'></i>\
      </a>\
      <a href='JavaScript:void(0);' class='delete_provider_feed_data'>\
        <i class='fas fa-trash'></i>\
      </a>\
    </div></td>\
  </tr>";
}

function disable_provider(data) {
  data = JSON.parse(data.responseText);
  update_provider_actions(data.record_id, data.is_disable)
}

function update_provider_actions(record_id , is_disable) {
  if(is_disable == 0) {
    $('.newsHubProviderArea .provider_'+record_id).find('a').removeClass('hidden');
    $('.newsHubProviderArea .provider_'+record_id+' .provider_disable_link.enable_icon ').addClass('hidden');
    $('.newsHubProviderArea .provider_'+record_id+' .provider_name').removeClass('provider_title_width');
    $('.newsHubProviderArea .provider_'+record_id+' .providerWebFeedArea .turncate_to.title').removeClass('provider_feed_title_width_disable');
  }
  else {
    $('.newsHubProviderArea .provider_'+record_id).find('a:not(.view_link)').addClass('hidden');
    $('.newsHubProviderArea .provider_'+record_id+' .provider_disable_link.enable_icon ').removeClass('hidden');
    $('.newsHubProviderArea .provider_'+record_id+' .provider_name').addClass('provider_title_width');
    $('.newsHubProviderArea .provider_'+record_id+' .providerWebFeedArea .turncate_to.title').addClass('provider_feed_title_width_disable');
  }
}

jQuery(document).ready(function () {
  // get_providers();
  // getConfiguration();
  // get_provider_view_data();
  $('body').on('click', '#new_web_feed_submit', function(){
    console.log('Submit clicked');

    request_to_server(  'post', 
                        'api.php', 
                        $('#newWebFeedForm').serialize(), 
                        'render_new_provider'
                      ); 
  });

  $('body').on('click', '.edit_provider_data', function(){
    parent_div = $(this).closest('.prividersListArea')
    $('#exampleModalCenter form #name').val($(parent_div).find('.provider_name').data('full_name'));
    $('#exampleModalCenter form #feedUrl').val($(parent_div).find('.external_provider_link').attr('href'));
    $('#exampleModalCenter form #provider_id').val($(parent_div).data('record_id'));
    $('#exampleModalCenter h3.modal-title').html('Update Web Feed');
    $('#exampleModalCenter').modal('show');
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
    is_sync_clicked = true;
    import_webfeed($(this).closest('.prividersListArea').data('record_id'));
  });

  $('body').on('click', '.sync_provider_view_data', function(){
    is_sync_clicked = true;
    request_to_server('get', 'api.php', {'method': 'import_new_webfeed', 'api_key': $('#home').data('api_key'), 'provider_id': $('#home').data('record_id')}, 'render_provider_feeds_for_view');
  });

  $('body').on('click', '#configurationFormSubmit', function(){
    console.log('Configuration Update');
    request_to_server('POST', 'api.php', $('#configurationForm').serialize(), 'updateConfiguration');
  });

  $('body').on('click', '.edit_provider_feed_data', function(){
    if($('#providerDetailView').length > 0) {
      parent_div = $(this).closest('tr');
      $('#providerFeed form #provider_post_id').val($(parent_div).find('td:nth-child(2)').data('record_id'));
      $('#providerFeed form #post_name').val($(parent_div).find('td:nth-child(2)').text());
      $('#providerFeed form #post_description').val($(parent_div).find('td:nth-child(3)').text());
    }
    else {
      parent_div = $(this).closest('.providerWebFeedPostArea');
      $('#providerFeed form #post_name').val($(parent_div).find('.title').data('full_title'));
      $('#providerFeed form #provider_post_id').val($(parent_div).data('record_id'));
      $('#providerFeed form #post_description').val($(parent_div).find('.title').data('description'));
    }
    $('#providerFeed').modal('show');
  });

  $('body').on('click', '#providerFeedPostFormSubmit', function(){
    request_to_server('POST', 'api.php', $('#providerFeedForm').serialize(), 'updateFeedPost');
  });

  $('body').on('click', '.delete_provider_feed_data', function(){
    if($('#providerDetailView').length > 0) {
      record_id = $(this).closest('tr').find('td:nth-child(2)').data('record_id');
    }
    else {
      record_id = $(this).closest('.providerWebFeedPostArea').data('record_id');
    }
    console.log('ANd the Id is: '+ record_id);
    dialog.confirm({
      title: "Delete Web Feed Post",
      message: "Are you sure you want to delete this post",
      cancel: "No",
      button: "Yes",
      required: true,
      callback: function(value){
        if(value)
          request_to_server('POST', 'api.php', {'method': 'delete_provider_post', 'api_key': $('#home').data('api_key'), 'id': record_id}, 'delete_provider_post');
      }
    });
  });

  $('body').on('click', '.view_provider_feed_link', function(){
    if($('#providerDetailView').length > 0) {
      parent_div = $(this).closest('tr');
      $('#viewProviderPost .name').html($(parent_div).find('td:nth-child(2)').text());
      $('#viewProviderPost .description').html($(parent_div).find('td:nth-child(3)').text());
      $('#viewProviderPost .published_date').html(date_format($(parent_div).find('td:nth-child(4)').text(), 'ddd MMM dd, yyyy HH:mm'));
      $('#viewProviderPost .created_at').html(date_format($(parent_div).find('td:nth-child(5)').text(), 'ddd MMM dd, yyyy HH:mm'));
      $('#viewProviderPost .external_provider_view_feed_link').attr('href', $(parent_div).find('.external_provider_feed_link').attr('href'));
    }
    else {
      parent_div = $(this).closest('.providerWebFeedPostArea');
      $('#viewProviderPost .name').html($(parent_div).find('.title').data('full_title'));
      $('#viewProviderPost .description').html($(parent_div).find('.title').data('description'));
      $('#viewProviderPost .published_date').html(date_format($(parent_div).find('.published_date').text(), 'ddd MMM dd, yyyy HH:mm'));
      $('#viewProviderPost .created_at').html(date_format($(parent_div).find('.created_at').text(), 'ddd MMM dd, yyyy HH:mm'));
      $('#viewProviderPost .external_provider_view_feed_link').attr('href', $(parent_div).find('.external_provider_feed_link').attr('href'));
    }
    $('#viewProviderPost').modal('show');

  });

  $('body').on('click', '.provider_disable_link', function(){
    record_id = $(this).closest('.prividersListArea').data('record_id');
    req_message = $(this).data('disable_message');
    dialog.confirm({
      title: "Delete Web Feed",
      message: "Are you sure you want to "+$(this).data('disable_message')+" this web feed?",
      cancel: "No",
      button: "Yes",
      required: true,
      callback: function(value){
        if(value) {
          console.log(req_message);
          request_to_server('POST', 'api.php', {'method': 'disable_provider', 'disable': req_message, 'api_key': $('#home').data('api_key'), 'id': record_id}, 'disable_provider');
        }
      }
    });
  });
  
  $('body').on('click', '.disabled_link', function(e){
    e.preventDefault();
    notify( 'error', 'Web is disabled!');
  });
});