<?php 

  include('database.php');

  if (isset($_REQUEST['api_key']))
  {
    if(!authenticate_key($conn)){
      $response_message['apikey'] = $_REQUEST["api_key"];
      $response_message['error_message'] = 'Authentication Failed!';
      send_response($response_message);
    }
    else {
      $_REQUEST['method']($conn);
    }
  }
  else
  {

    $response_message['error_message'] = 'Api key required!';
    send_response($response_message);
    // response('401','Unauthorized Access..1!!',401);
  }


  function authenticate_key($conn) {
    // echo $_GET["api_key"];
    $sql = "SELECT * FROM authentications WHERE authentications.auth_key = '".$_REQUEST["api_key"]."'";
    $res = mysqli_query($conn, $sql);

    $is_authenticated = false;

    if(mysqli_num_rows($res) > 0){
      $is_authenticated = true;
    }
    return $is_authenticated;
  }

  function send_response($data) {
    // $status_message = 'Unauthorized Access..1!!';
    // $response['status']=401;
    // $response['status_message']=$status_message;
    // $response['error_message']=$message;
    // $response = array();
    // $response['success'] = $success;
    // $response['general_message'] = $message;
    // $response['errors']  = $errors;
    $json_response = json_encode($data);
    // echo $json_response;
    exit($json_response);
  }

  function newFeed($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $response_message = [];
      // echo "sdasdasdas";
      // echo isset($_POST['submitButton']);
      // echo $_POST['submitButton'];
      // import_webfeed('https://www.cnbc.com/id/100003114/device/rss/rss.html');
      // import_webfeed('http://rss.cnn.com/rss/edition.rss', $conn);
      $is_valid = validate_provider($conn, $_REQUEST['id']); 
      if($is_valid[0]) {
        if (mysqli_query($conn, create_query_for_provider())) {
          if(isset($_REQUEST['id']) && $_REQUEST['id'] != ''){
            $response_message['success_message'] = "Web Feed updated successfully!";
            $provider_id = $_REQUEST['id'];
          }
          else {
            $response_message['success_message'] = "New record created successfully!";
            $provider_id = $conn->insert_id;
          }
          
          $response_message['record_id'] = $provider_id;
          $response_message['record_name'] = $_REQUEST['name'];
          $response_message['record_url'] = $_REQUEST['url'];
          $response_message['stage'] = 1;
        } 
        else {
          $response_message['stage'] = 2;
          $response_message['error_message'] = $conn->error;
          // echo "Error: " . $sql . "<br>" . $conn->error;
        }  
      }
      else {
        // echo 'Array Length: '.count($is_valid[1]);
        $response_message['stage'] = 3;
        $response_message['error_message'] = join(', ', $is_valid[1]);
      }

      // header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
    $response_message['apikey'] = $_REQUEST["api_key"];
    send_response($response_message);
  }

  function validate_provider($conn, $provider_id='') {
    $errors = array();
    $valid_url_regex = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
    $is_valid = true;
    if(is_duplicate_provider($conn, $provider_id)) {
      $is_valid = false;
      array_push($errors, 'Provider already exist.');
    }

    if(!is_valid_webfeed_url($_POST["url"])) {
      $is_valid = false;
      array_push($errors, 'Invalid feed URL');
    }

    if(!isset($_POST["name"]) || $_POST["name"] == '') {
      $is_valid = false;
      array_push($errors, 'Name is required.');
    }

    if(!preg_match($valid_url_regex, $_POST["url"])){
      $is_valid = false;
      array_push($errors, 'Url is not valid.');
    }
    return array($is_valid, $errors);
  }

  function is_duplicate_provider($conn, $provider_id) {
    if($provider_id == '')
      $sql = "SELECT * FROM providers WHERE providers.url = '".$_POST["url"]."'";
    else
      $sql = "SELECT * FROM providers WHERE providers.url = '".$_POST["url"]."' AND providers.id <>".$provider_id."";
 
    $res = mysqli_query($conn, $sql);
    $is_duplicate = true;

    if(mysqli_num_rows($res) == 0){
      $is_duplicate = false;
    }
    return $is_duplicate;
  }

  function is_valid_webfeed_url($url) {
    $is_valid_url = false;
    if(@simplexml_load_file($url)){
      $is_valid_url = true;
    }
    return $is_valid_url;
  }

  function create_query_for_provider(){
    if(isset($_REQUEST["id"]) && $_REQUEST["id"] != '')
      return $sql = "UPDATE providers SET providers.name = '".$_REQUEST["name"]."', providers.url = '".$_REQUEST["url"]."', providers.updated_at = '".date("Y-m-d H:i:s")."' WHERE providers.id = ".$_REQUEST["id"]."";
    else
      return $sql = "INSERT INTO providers(name, url, created_at, original_name) VALUES ('".$_REQUEST["name"]."','".$_REQUEST["url"]."','".date("Y-m-d H:i:s")."', '".$_REQUEST["name"]."')";
  }

  function create_query_for_feed($provider_id, $item) {
    $pubDate = date('Y-m-d H:i:s', strtotime($item->pubDate));

    return "INSERT INTO webfeeds(title, description, url, publish_date, provider_id) 
            VALUES ('".addslashes($item->title)."','".addslashes($item->description)."','".$item->link."','".$pubDate."', '".$provider_id."')";
  }

  function import_webfeed($conn, $url, $provider_id) {
    
    if(can_import_provider_feeds($conn)) { 
      $i=0;
      $feeds = simplexml_load_file($url);
      if(!empty($feeds)){
        $success = 0;
        $fail = 0;

        foreach ($feeds->channel->item as $item) {
          $q = create_query_for_feed( $provider_id, $item);
          
          if (mysqli_query($conn, $q)) {
            // echo "New record created successfully";
            $success++;
          } 
          else {
            // echo "Error: " . $sql . "<br>" . $conn->error;
            $fail++;
          }  
        }
      }
      else {
        // echo "<h2>No item found</h2>";
      }
    }

    // echo "----------------------------------------------------------- ";
    // echo 'Success: '.$success;
    // echo 'Fail: '.$fail;
  }

  function get_providers($conn){
    $sql = "SELECT * FROM providers";
    $result = mysqli_query($conn, $sql);
    if( mysqli_num_rows($result) > 0) {
      $r = array();
      // $r['data'] = $data;
      while( $rows = mysqli_fetch_assoc($result) ) {
          $r[] = $rows;
      }
      $r['data'] = $r;
      $r['data_count'] = mysqli_num_rows($result);
    }
    else {
      $r['data'] = [];
    }
    send_response($r);
  }

  function get_provider_feeds($conn) {
    if(isset($_REQUEST['provider_id']) && $_REQUEST['provider_id'] != ''){
      $sql = "SELECT * FROM webfeeds WHERE provider_id=".$_REQUEST['provider_id'];
      $result = mysqli_query($conn, $sql);
      if( mysqli_num_rows($result) > 0) {
        $r = array();
        // $r['data'] = $data;
        while( $rows = mysqli_fetch_assoc($result) ) {
            $r[] = $rows;
        }
        $r['data'] = $r;
        $r['data_count'] = mysqli_num_rows($result);
      }
      else {
        $r['data'] = [];
      }
      send_response($r);
    }
    else {
      $r['error_message'] = "No webfeeds present.";
      send_response($r); 
    }
  }

  function delete_provider($conn) {
    if(isset($_REQUEST['id']) && $_REQUEST['id'] != ''){
      $sql = "DELETE FROM providers WHERE providers.id=".$_REQUEST['id'];
      $result = mysqli_query($conn, $sql);
      if( $result ) {
        $r['record_id'] = $_REQUEST['id'];
        $r['success_message'] = 'Web Feed deleted successfully!';
      }
      else {
        $r['error_message'] = 'Can not find the desired record!';
      }
    }
    else {
      $r['error_message'] = 'Something went wrong, try again later!';
    }
    send_response($r);
  }

  function import_new_webfeed($conn) {
    // import_webfeed();
    // $r['damit'] ='';
    if(isset($_REQUEST['provider_id']) && $_REQUEST['provider_id'] != ''){
      $sql = "SELECT * FROM providers WHERE providers.id=".$_REQUEST['provider_id'];
      $result = mysqli_query($conn, $sql);
      if( mysqli_num_rows($result) > 0) {
        $provider = mysqli_fetch_assoc($result);
        import_webfeed($conn, $provider['url'], $provider['id']);
        update_web_feed_update_time_interval($conn);
        get_provider_feeds($conn);
      }
      else {
        $r['error_message'] = 'No Web Feed found!';
      }
      send_response($r);
    }
    else {
      $r['error_message'] = "No webfeeds found!";
      send_response($r); 
    }
  }

  function update_web_feed_update_time_interval($conn) {
    $sql = "UPDATE configurations SET configurations.keep_until = 10, configurations.last_updated = '".date("Y-m-d H:i:s")."' WHERE configurations.id=1";
    mysqli_query($conn, $sql);
  }

  function can_import_provider_feeds($conn){
    $conf = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM configurations"));
    $diff_last_updated = abs(time() - strtotime($conf['last_updated'])) / 60;
    if($diff_last_updated >= $conf['update_gap'])
      return true;
    else {
      $r['error_message'] = 'You will be able to update the feed after '.round(($conf['update_gap']-$diff_last_updated));
      send_response($r);
    }
  }
?>