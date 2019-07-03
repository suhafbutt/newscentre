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
  }


  function authenticate_key($conn) {
    $sql = "SELECT * FROM authentications WHERE authentications.auth_key = '".$_REQUEST["api_key"]."'";
    $res = mysqli_query($conn, $sql);

    $is_authenticated = false;

    if(mysqli_num_rows($res) > 0){
      $is_authenticated = true;
    }
    return $is_authenticated;
  }

  function send_response($data) {
    $json_response = json_encode($data);
    exit($json_response);
  }

  function newFeed($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $response_message = [];
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
        } 
        else {
          $response_message['error_message'] = $conn->error;
        }  
      }
      else {
        $response_message['error_message'] = join(', ', $is_valid[1]);
      }

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
    $pubDate = date('Y-m-d H:i:s', strtotime(get_feed_publish_date($item)));

    return "INSERT INTO webfeeds(title, description, url, publish_date, provider_id, imported_feed_id) 
            VALUES ('".addslashes($item->title)."','".addslashes(get_feed_description($item))."','".get_feed_url($item)."','".$pubDate."', '".$provider_id."', '".get_feed_post_id($item)."')";
  }

  function import_webfeed($conn, $url, $provider_id) {
    $success_count = 0;
    $fail_count = 0;
    $last_attempt_response = '';
    $age = (int)get_feed_age_limit($conn);
    $error_find = [];

    if(can_import_provider_feeds($conn)) { 
      $i=0;
      $feeds = simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOWARNING);
      if($feeds === false) {
        $last_attempt_response = 'Could not import file content.';
      }
      else {
        if(!empty($feeds)){
          $i = 0;
          update_old_feeds($conn, $provider_id, $age);
          foreach (get_feeds_data($feeds) as $item) {
            $date2 = new DateTime(date('Y-m-d', strtotime(get_feed_publish_date($item))));
            $current_date = new DateTime(date('Y-m-d'));
            $diff = $current_date->diff($date2)->days;
            $is_already_saved = is_already_imported($conn, $item);
            
            $i += 1;
            if( empty($latest_record) || $latest_record < $date2)
              $latest_record = $date2;

            if($age > $diff) {
              if(!$is_already_saved) {
                $q = create_query_for_feed( $provider_id, $item);
                if(mysqli_query($conn, $q)) {
                  $success_count += 1;
                }
                else {
                  $last_attempt_response = $conn->error;
                  $fail_count += 1;
                }
              }
            }
            elseif ($is_already_saved) {
              $sql = "UPDATE webfeeds SET webfeeds.is_deleted= true WHERE webfeeds.url='".get_feed_url($item)."'";
              mysqli_query($conn, $sql);
            }
          }
        }
      }
      
      if($fail_count == 0) {
        $last_attempt_response = $success_count;
        $lastest_successful_update = date("Y-m-d H:i:s");
      }
      else {
        $last_attempt_response = 'There were some issues while saving Web feed posts';
      }

      
      // $response_message['d'] = $lastest_successful_update;
      // send_response($response_message);
  
      // $latest_record
      // $last_update_attempt = new DateTime('Y-m-d H:i:s');
      // $response_message['d'] = $latest_record;
      // $response_message['qr'] = $conn->error;
      // send_response($response_message);
      update_provider_data($conn, $provider_id, $lastest_successful_update, $latest_record, date("Y-m-d H:i:s"), $last_attempt_response, $feeds);
    }
  }

  function get_feeds_data($feeds) {
    if(isset($feeds->channel->item)) {
      return $feeds->channel->item;
    }
    else {
      return $feeds->entry;
    }
  }

  function get_feed_description($item) {
    if(isset($item->content)) {
      return $item->content;
    }
    else {
      return $item->description;
    }
  }

  function get_feed_url($item) {
    if(isset($item->link['href'])) {
      return $item->link['href'];
    }
    else {
      return $item->link;
    }
  }

  function get_feed_publish_date($item) {
    if(isset($item->published)) {
      return $item->published;
    }
    else {
      return $item->pubDate;
    }
  }

  function get_feed_post_id($item) {
    if(isset($item->id)) {
      return $item->id;
    }
    else {
      return $item->guid;
    }
  }

  function get_provider_external_url($item) {
    if(isset($item->link['href'])) {
      return $item->link['href'];
    }
    else {
      return $item->channel->link;
    }
  }

  function get_original_title($item) {
    if(isset($item->title)) {
      return $item->title;
    }
    else {
      return $item->channel->image->title;
    }
  }

  function get_providers($conn){
    $sql = "SELECT * FROM providers";
    $result = mysqli_query($conn, $sql);
    if( mysqli_num_rows($result) > 0) {
      $r = array();
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
      $sql = "SELECT * FROM webfeeds WHERE webfeeds.is_deleted = false AND webfeeds.provider_id=".$_REQUEST['provider_id']." ORDER BY `webfeeds`.`publish_date` DESC";
      $provider_sql = "SELECT * FROM providers WHERE providers.id =".$_REQUEST['provider_id'];
      $pro = mysqli_query($conn, $provider_sql);
      $provider = mysqli_fetch_assoc($pro);

      $result = mysqli_query($conn, $sql);
      $result_count = mysqli_num_rows($result);
      if( $result_count > 0) {
        $r = array();
        while( $rows = mysqli_fetch_assoc($result) ) {
            $r[] = $rows;
        }
        $r['data'] = $r;
        $r['data_count'] = $result_count;
        $r['provider_id'] = $_REQUEST['provider_id'];
        $r['provider'] = $provider;
        if($result_count > 0) {
          $r['success_message'] = 'Web feed posts successfully updated!';
        }
        else {
          $r['error_message'] = 'No posts found!';
        }
      }
      else {
        $r['provider'] = $provider;
        $r['provider_id'] = $_REQUEST['provider_id'];
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
        update_web_feed_time_interval($conn);
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

  function update_web_feed_time_interval($conn) {
    $sql = "UPDATE configurations SET 
    configurations.last_updated = '".date("Y-m-d H:i:s")."' WHERE configurations.id=1";
    mysqli_query($conn, $sql);
  }

  function can_import_provider_feeds($conn){
    $conf = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM configurations"));
    $diff_last_updated = abs(time() - strtotime($conf['last_updated'])) / 60;
    if($diff_last_updated >= $conf['update_gap'])
      return true;
    else {
      $r['error_message'] = 'You will be able to update the feed after '.ceil(($conf['update_gap']-$diff_last_updated)).' minute(s)';
      send_response($r);
    }
  }

  function getConfiguration($conn) {
    $sql = "SELECT * FROM configurations WHERE configurations.id=1";
    $result = mysqli_query($conn, $sql);
    $r['data'] = mysqli_fetch_assoc($result);
    send_response($r);
  }

  function updateConfiguration($conn) {
    if(isset($_REQUEST['time_interval']) && $_REQUEST['time_interval'] != '') {
      if(isset($_REQUEST['keep_until']) && $_REQUEST['keep_until'] != '') {
        $sql = "UPDATE configurations SET configurations.keep_until = ".$_REQUEST['keep_until'].", configurations.update_gap = ".$_REQUEST['time_interval']." WHERE configurations.id=1";
        if(mysqli_query($conn, $sql)) {
          $r['success_message'] = "Configuration(s) updated successfully!";
        }
        else {
          $r['error_message'] = $conn->error;
        }
      }
      else {
        $r['error_message'] = 'Old Record Age can not be empty!';
      }
    }
    else {
      $r['error_message'] = 'Time Interval can not be empty!';
    }
    send_response($r);
  }

  function get_feed_age_limit($conn) {
    $sql = "SELECT * FROM configurations WHERE configurations.id=1";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)['keep_until'];
  }

  function is_already_imported($conn, $item) {
    $sql = "SELECT * FROM webfeeds WHERE webfeeds.url='".get_feed_url($item)."'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0)
      return true;
    return false;
  }

  function updateFeedPost($conn) {
    if(isset($_REQUEST['provider_post_id']) && $_REQUEST['provider_post_id'] != ''){
      $sql = "UPDATE webfeeds SET webfeeds.title='".addslashes($_REQUEST['title'])."', webfeeds.description='".addslashes($_REQUEST['description'])."' WHERE webfeeds.id=".$_REQUEST['provider_post_id'];
      $res = mysqli_query($conn, $sql);
      if( $res ) {
        $r['id'] = $_REQUEST['provider_post_id'];
        $r['title'] = $_REQUEST['title'];
        $r['description'] = $_REQUEST['description'];
        $r['success_message'] = 'Post successfully updated!';
      }
      else {
        $r['error_message'] = 'No post found! ';
      }
      send_response($r);
    }
    else {
      $r['error_message'] = "No post found!";
      send_response($r); 
    }
  }

  function delete_provider_post($conn) {
    if( delete_feed_post($conn, $_REQUEST['id']) ) {
      $r['record_id'] = $_REQUEST['id'];
      $r['success_message'] = 'post deleted successfully!';
    }
    else {
      $r['error_message'] = 'Can not find the desired post!';
    }
    send_response($r);
  }

  function delete_feed_post($conn, $feed_id) {
    if(isset($feed_id) && $feed_id != ''){
      $sql = "UPDATE webfeeds SET webfeeds.is_deleted= true WHERE webfeeds.id=".$feed_id;
      return $result = mysqli_query($conn, $sql);
    }
    return false;
  }

  function exportProvider($conn) {
    $r = array();
    $sql = "SELECT * FROM providers WHERE id=".$_REQUEST['provider_id'];
    $provider = mysqli_fetch_assoc(mysqli_query($conn, $sql));

    $sql = "SELECT * FROM webfeeds WHERE provider_id=".$_REQUEST['provider_id']." AND is_deleted=false";
    $result = mysqli_query($conn, $sql);
    $result_count = mysqli_num_rows($result);

    header('Content-Type: text/xml; charset=utf-8', true);

    $rss = new SimpleXMLElement('<rss xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom"></rss>');
    $rss->addAttribute('version', '2.0');
    $channel = $rss->addChild('channel'); //add channel node

    $atom = $channel->addChild('atom:atom:link'); //add atom node
    $atom->addAttribute('href', 'http://localhost'); //add atom node attribute
    $atom->addAttribute('rel', 'self');
    $atom->addAttribute('type', 'application/rss+xml');

    $provider_name = htmlentities(escape_string_xml($provider['name']));
    $assdas = $channel->addChild('title', $provider_name); //title of the feed
    $link = $channel->addChild('link',$provider['url']); //feed site

    $language = $channel->addChild('language','en-us'); //language
    $date_f = date("D, d M Y H:i:s T", time());
    $build_date = gmdate(DATE_RFC2822, strtotime($date_f)); 
    $lastBuildDate = $channel->addChild('lastBuildDate',$date_f); //feed last build date

    $generator = $channel->addChild('generator','PHP Simple XML'); //add generator svn_fs_node_prop(fsroot, path, propname)

    if( $result_count > 0) {
      while( $rows = mysqli_fetch_assoc($result) ) {
        $item = $channel->addChild('item'); //add item node
        $title = $item->addChild('title', escape_string_xml($rows['title'])); //add title node under item
        $link = $item->addChild('link', $rows['url']); //add link node under item
        $des_text = utf8_encode(html_entity_decode($rows['description']));
        $description = $item->addChild('description', '<![CDATA['. htmlentities(escape_string_xml($des_text)) . ']]>');
        
        $date_rfc = gmdate(DATE_RFC2822, strtotime($rows['publish_date']));
        $item = $item->addChild('pubDate', $date_rfc); //add pubDate node
      }
    }

    echo $rss->asXML();
  }

  function escape_string_xml($str){
    $regex = '/(\s\&\s)/i';
    $regex1 = '/(\s\<\s)/i';
    $regex2 = '/(\s\>\s)/i';
    $replace = ' &amp; ';
    $replace1 = ' &lt; ';
    $replace2 = ' &gt ';


    if(preg_match($regex, $str) || preg_match($regex1, $str) || preg_match($regex2, $str)){
        $new_str = preg_replace($regex, $replace, $str );
        $new_str = preg_replace($regex1, $replace1, $new_str );
        return preg_replace($regex2, $replace2, $new_str );
    }
    else {
      return $str;
    }
  }

  function update_provider_data($conn, $provider_id, $lastest_successful_update, $latest_record, $last_update_attempt, $last_attempt_response, $imported_data) {
    $newDate = $latest_record->format(\DateTime::ISO8601);
    $sql = "UPDATE providers SET providers.original_name='".addslashes(get_original_title($imported_data))."', providers.external_url='".get_provider_external_url($imported_data)."', providers.lastest_successful_update='".$lastest_successful_update."', providers.last_update_attempt='".$last_update_attempt."', providers.last_attempt_response='".$last_attempt_response."', providers.latest_record='".$newDate."' WHERE providers.id=".$provider_id;
    $res = mysqli_query($conn, $sql);
  }

  function get_provider($conn) {
    if(isset($_REQUEST['provider_id']) && $_REQUEST['provider_id'] != ''){
      $sql = "SELECT * FROM providers WHERE providers.id=".$_REQUEST['provider_id'];
      $result = mysqli_query($conn, $sql);
      if( mysqli_num_rows($result) > 0) {
        $r['provider'] = mysqli_fetch_assoc($result);
      }
      else {
        $r['error_message'] = 'No provider found!';
      }
      send_response($r);
    }
  }

  function update_old_feeds($conn, $provider_id, $age) {
    $current_date = new DateTime(date('Y-m-d'));
    $old_record_ids = [];

    $sql = "SELECT * FROM webfeeds WHERE webfeeds.is_deleted = false AND webfeeds.provider_id=".$provider_id;
    $result = mysqli_query($conn, $sql);
    $result_count = mysqli_num_rows($result);

    if( $result_count > 0) {

      while( $rows = mysqli_fetch_assoc($result) ) {
        
        $date2 = new DateTime(date('Y-m-d', strtotime($rows['publish_date'])));
        $diff = $current_date->diff($date2)->days;

        if($age < $diff) {
          $old_record_ids[] = $rows['id'];
        }
      }
      
      if(count($old_record_ids) > 0){
        $sql = "UPDATE webfeeds SET webfeeds.is_deleted= true WHERE webfeeds.id IN (".implode(",", $old_record_ids).")";
        mysqli_query($conn, $sql);
      }
    }
  }

  function disable_provider($conn) {
    if(isset($_REQUEST['id']) && $_REQUEST['id'] != ''){
      $is_disable = 0;
      $msg = 'Enabled';
      $r['req'] = $_REQUEST;
      if($_REQUEST['disable'] == 'disable') {
        $is_disable = 1;
        $msg = 'Disabled';
      }

      $r['is'] = $is_disable;
      $sql = "UPDATE providers SET providers.is_disable=".$is_disable." WHERE providers.id=".$_REQUEST['id'];
      $result = mysqli_query($conn, $sql);
      if( $result ) {
        $r['record_id'] = $_REQUEST['id'];
        $r['is_disable'] = $is_disable;
        $r['success_message'] = 'Web Feed '.$msg.' successfully!';
      }
      else {
        $r['sql'] = $sql;
        $r['err_msg'] = $conn->error;
        $r['error_message'] = 'Can not find the desired record!';
      }
    }
    else {
      $r['error_message'] = 'Something went wrong, try again later!';
    }
    send_response($r);
  }
?>