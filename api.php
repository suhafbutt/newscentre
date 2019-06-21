<?php 

  include('database.php');

  if (isset($_REQUEST['api_key']))
  {
    if(!authenticate_key($conn)){
      set_error_message('Authentication Failed!');
    }
    else {
      $_REQUEST['method']($conn);
    }
  }
  else
  {
    $response_message['error_message'] = 'Api key required!';
    set_error_message($response_message);
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

  function set_error_message($message) {
    // $status_message = 'Unauthorized Access..1!!';
    // $response['status']=401;
    // $response['status_message']=$status_message;
    // $response['error_message']=$message;
    // $response = array();
    // $response['success'] = $success;
    // $response['general_message'] = $message;
    // $response['errors']  = $errors;
    $json_response = json_encode($message);
    // echo $json_response;
    exit(json_encode($json_response));
  }

  function newFeed($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $response_message = [];
      // echo "sdasdasdas";
      // echo isset($_POST['submitButton']);
      // echo $_POST['submitButton'];
      // import_webfeed('https://www.cnbc.com/id/100003114/device/rss/rss.html');
      // import_webfeed('http://rss.cnn.com/rss/edition.rss', $conn);

      if(isset($_POST['submitButton']) && $_POST['submitButton'] == 'newWebFeedForm'){
        $is_valid = validate_provider($conn); 
        if($is_valid[0]) {
          if (( $provider =  mysqli_query($conn, create_query_for_provider()))) {
            // echo "New record created successfully";
            import_webfeed($conn, $_POST["url"], $conn->insert_id);
            $response_message['success_message'] = "New record created successfully";
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
    }
    set_error_message($response_message);
  }

  function validate_provider($conn) {
    $errors = array();
    $valid_url_regex = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
    $is_valid = true;
    if(is_duplicate_provider($conn)) {
      $is_valid = false;
      array_push($errors, 'Provider already exist.');
    }

    if(!is_valid_webfeed_url($_POST["url"])) {
      $is_valid = false;
      array_push($errors, 'Invalid feed URL');
    }

    if(!isset($_POST["name"])) {
      $is_valid = false;
      array_push($errors, 'Name is required.');
    }

    if(!preg_match($valid_url_regex, $_POST["url"])){
      $is_valid = false;
      array_push($errors, 'Url is not valid.');
    }
    return array($is_valid, $errors);
  }

  function is_duplicate_provider($conn) {
    $sql = "SELECT * FROM providers WHERE providers.url = '".$_POST["url"]."'";
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
    return "INSERT INTO providers(name, url, created_at, original_name) VALUES ('".$_POST["name"]."','".$_POST["url"]."','".date("Y-m-d H:i:s")."', '".$_POST["name"]."')";
  }

  function create_query_for_feed($provider_id, $item) {
    $pubDate = date('Y-m-d H:i:s', strtotime($item->pubDate));

    return "INSERT INTO webfeeds(title, description, url, publish_date, provider_id) 
            VALUES ('".addslashes($item->title)."','".addslashes($item->description)."','".$item->link."','".$pubDate."', '".$provider_id."')";
  }

  function import_webfeed($conn, $url, $provider_id) {
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

    // echo "----------------------------------------------------------- ";
    // echo 'Success: '.$success;
    // echo 'Fail: '.$fail;
  }
?>