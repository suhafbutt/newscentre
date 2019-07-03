<?php include('header.php') ?>
  <section>
    <div class="container action_area">
      <div class='row' >
        <div class='col-md-12 col-sm-12' >
          <button type="button" class="pull-right primary" data-toggle="modal" data-target="#exampleModalCenter">
            New Web Feed
          </button>
          <button type="button" class="pull-right primary conf" data-toggle="modal" data-target="#configurationModal">
            Update Configuration
          </button>
        </div>
      </div>
    </div>  
  </section>
  <!-- about section -->
  <section class="about text-center newsHubProviderArea" id="about">
    <div class="container">
      <div class="row" id='prividersList'>
        <div class="col-md-6 col-sm-6 hidden prividersListArea" id='hiddenArea'>
          <div class="single-about-detail">
            <div class="about-details">
              <div class="pentagon-text">
                <h1 class='turncate_to provider_name'>
                  FOX News
                </h1>
                <div class='webFeedActions pull-right'>
                  <a href="JavaScript:void(0);" class='view_link view_provider_link'>
                    <i class="fas fa-eye"></i>
                  </a>
                  <a href="JavaScript:void(0);" class='export_provider_xml' target="_blank">
                    <i class="fas fa-arrow-circle-up"></i>
                  </a>
                  <a href="JavaScript:void(0);" class='external_provider_link' target="_blank">
                    <i class="fas fa-external-link-square-alt"></i>
                  </a>
                  <a href="JavaScript:void(0);" class='sync_provider_data'>
                    <i class="fas fa-sync"></i>
                  </a>
                  <a href="JavaScript:void(0);" class='provider_disable_link enable_icon' data-disable_message='enable'>
                    <i class="far fa-check-circle"></i>
                  </a>
                  <a href="JavaScript:void(0);" class='provider_disable_link disable_icon' data-disable_message='disable'>
                    <i class="fas fa-ban"></i>
                  </a>
                  <a href="JavaScript:void(0);" class='edit_provider_data'>
                    <i class="fas fa-edit"></i>
                  </a>
                  <a href="JavaScript:void(0);" class='delete_provider_data'>
                    <i class="fas fa-trash"></i>
                  </a>
                </div>
              </div>
              <ul class= 'providerWebFeedArea'>
                <li class='providerWebFeedPostArea'>
                  <p class='turncate_to title'>
                    mom left paralyzed after pregnancy complication triggers stroke at 29 weeks
                  </p>
                  <p class='hidden published_date'></p>
                  <p class='hidden created_at'></p>
                  <div class='newsActions pull-right'>
                    <a href="JavaScript:void(0);" class='view_link view_provider_feed_link'>
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="JavaScript:void(0);" class='external_provider_feed_link' target="_blank">
                      <i class="fas fa-external-link-square-alt"></i>
                    </a>
                    <a href="JavaScript:void(0);" class='edit_provider_feed_data'>
                      <i class="fas fa-edit"></i>
                    </a>
                    <a href="JavaScript:void(0);" class='delete_provider_feed_data'>
                      <i class="fas fa-trash"></i>
                    </a>
                  </div>
                </li>
              </ul>
              <!-- <h3>Children’s specialist</h3>
              <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer.</p> -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </section><!-- end of about section -->

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id='newWebFeedForm'>
        <div class="modal-header">
          <h3 class="modal-title" id="exampleModalLongTitle">
            New Web Feed
          </h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="Name">Name</label>
            <input type="text" class="form-control" name='name' id="name">
          </div>
          <div class="form-group">
            <label for="feedUrl">Url</label>
            <input type="url" name='url' class="form-control" id="feedUrl">
            <input type="text" name='api_key' class="form-control hidden" id="api_key" value="KMPydWQBnSXVZZXZK0jg">
            <input type="text" name='method' class="form-control hidden" id="method" value="newFeed">
            <input type="text" name='submitButton' class="form-control hidden" id="submitButton" value="newWebFeedForm">
            <input type="text" name='id' class="form-control hidden" id="provider_id" value="">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="small-btn secondary" data-dismiss="modal">Close</button>
          <button type="button" class="small-btn primary" id='new_web_feed_submit'>Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="configurationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle2" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id='configurationForm'>
        <div class="modal-header">
          <h3 class="modal-title">
            Change Configurations
          </h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="time_interval">Time Interval to update Web Feed (minutes)</label>
            <input type="number" class="form-control" name='time_interval' id="time_interval" min=0>
          </div>
          <div class="form-group">
            <label for="keep_until">Old Record Age (days)</label>
            <input type="number" name='keep_until' class="form-control" id="keep_until" min=0>
            <small id="keep_until_help" class="form-text text-muted">Web Feeds records will not be saved if post is older than the threshold.</small>
            <input type="text" name='api_key' class="form-control hidden" id="api_key" value="KMPydWQBnSXVZZXZK0jg">
            <input type="text" name='method' class="form-control hidden" id="method" value="updateConfiguration">
            <input type="text" name='submitButton' class="form-control hidden" id="submitButton" value="updateConfiguration">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="small-btn secondary" data-dismiss="modal">Close</button>
          <button type="button" class="small-btn primary" id='configurationFormSubmit'>Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="providerFeed" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle3" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id='providerFeedForm'>
        <div class="modal-header">
          <h3 class="modal-title">
            Edit Web Feed Post
          </h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="post_name">Name</label>
            <input type="text" class="form-control" name='title' id="post_name">
          </div>
          <div class="form-group">
            <label for="post_description">Description</label>
            <textarea class="form-control" name='description' id="post_description" rows="3"></textarea>
            <input type="text" name='api_key' class="form-control hidden" id="api_key" value="KMPydWQBnSXVZZXZK0jg">
            <input type="text" name='method' class="form-control hidden" id="method" value="updateFeedPost">
            <input type="text" name='provider_post_id' class="form-control hidden" id="provider_post_id" value="">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="small-btn secondary" data-dismiss="modal">Close</button>
          <button type="button" class="small-btn primary" id='providerFeedPostFormSubmit'>Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="viewProviderPost" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle4" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">
          View
        </h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class='row'>
          <div class='col-md-12 col-sm-12' >
            <label>Title:</label>
            <p class='name'>
              qwertytdsxcvgrewsdcv gvgy c yc gyc gv gjv 
            </p>
          </div>
        </div>
        <hr/>
        <div class='row'>
          <div class='col-md-12 col-sm-12' >
            <label>Description:</label>
            <p class='description'>
              qwertytdsxcvgrewsdcv gvgy c yc gyc gv gjv 
            </p>
          </div>
        </div>
        <hr/>
        <div class='row'>
          <div class='col-md-12 col-sm-12' >
            <div class='pull-left'>
              <label>Published Date:</label>
              <p class='published_date'>
                qwertytdsxcvgrewsdcv gvgy c yc gyc gv gjv 
              </p>
            </div>
            <div class='pull-right'>
              <label>Detected by app:</label>
              <p class='created_at'>
                qwertytdsxcvgrewsdcv gvgy c yc gyc gv gjv 
              </p>
            </div>
          </div>
        </div>
        <hr/>
        <div class='row'>
          <div class='col-md-12 col-sm-12' >
            <a href="JavaScript:void(0);" class='external_provider_view_feed_link' target="_blank">
              External Link
            </a>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="small-btn secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php include('footer.php') ?>  
<script type="text/javascript">
  get_providers();
  getConfiguration();
</script>