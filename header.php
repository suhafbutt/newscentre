<!DOCTYPE html>
<html lang="en"> 
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>DOCTOR - Responsive HTML &amp; Bootstrap Template</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/all.min.css">
  <link rel="stylesheet" href="css/notification/lobibox.min.css">
  <link rel="stylesheet" href="css/preloader.css">
  <link rel="stylesheet" href="css/jquery.mCustomScrollbar.css">
  <link rel="stylesheet" href="css/jquery.dialog.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:600italic,400,800,700,300' rel='stylesheet' type='text/css'>
  <link href='http://fonts.googleapis.com/css?family=BenchNine:300,400,700' rel='stylesheet' type='text/css'>

</head>

<?php 
$p_id = '';
if(isset($_REQUEST['provider_id']) && $_REQUEST['provider_id'] != ''){
  $p_id = $_REQUEST['provider_id'] ;
}
?>

<body>
  <div style="background-image: url(img/back.jpg);">
    <section class="slider" id="home" data-api_key="KMPydWQBnSXVZZXZK0jg" data-record_id=<?php echo $p_id ?>>
      <div class="container-fluid">
        <div class="row">
            <div id="carouselHacked" class="carousel slide carousel-fade" data-ride="carousel">
                <div class="carousel-inner" role="listbox" style="background-image: url(img/header.jpg);">
                  <div class="item active" id="img_item">
                    <a href="/newscentre/index.php" class='header-logo-link'> 
                      <img src="img/output-onlinepngtools.png" alt="News" class="img_news">
                    </a>  
                  </div>
                </div>
            </div>
        </div>
      </div>
    </section>
  