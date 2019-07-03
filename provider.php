<?php include('header.php') ?>
  <section class="about text-center newsHubProviderArea" id="about">
    <div class="container provider_<?php echo $_REQUEST['provider_id'] ?>" id='providerDetailView'>
      <div class="header_section prividersListArea">
        <div class='row'>
          <div class='col-md-12 col-sm-12'>
            <h1 class='heading turncate_to provider_name'> CNN </h1>
            <div class='webFeedActions pull-right'>
              <a href="JavaScript:void(0);" class='export_provider_xml' target="_blank">
                <i class="fas fa-arrow-circle-up"></i>
              </a>
              <a href="JavaScript:void(0);" class='external_provider_link' target="_blank">
                <i class="fas fa-external-link-square-alt"></i>
              </a>
              <a href="JavaScript:void(0);" class='sync_provider_view_data'>
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
        </div>
        <div class='row'>
          <div class='col-md-6 col-sm-6 section1'>
            
            <div class='row' >
              <div class='col-md-12 col-sm-12'>
                <div class='col-md-6 col-sm-6'>
                  <h3 class='detail_section'>
                    Provider:
                  </h3>
                </div>
                <div class='col-md-6 col-sm-6'>
                  <h3 class='detail_section provider_source_name'>
                    CNN Top Site 
                  </h3>
                </div>
              </div>
            </div>

            
            <div class='row' >
              <div class='col-md-12 col-sm-12'>
                <div class='col-md-6 col-sm-6'>
                  <h3 class='detail_section'>
                    Last Update Attempt:
                  </h3>
                </div>
                <div class='col-md-6 col-sm-6'>
                  <h3 class='detail_section last_update_attempt'>
                    Sunday Jun 23, 2019 15:24  
                  </h3>
                </div>
              </div> 
            </div>

            <div class='row' >
              <div class='col-md-12 col-sm-12'>
                <div class='col-md-6 col-sm-6'>
                  <h3 class='detail_section'>
                    Records Found on last Attempt:
                  </h3>
                </div>
                <div class='col-md-6 col-sm-6'>
                  <h3 class='detail_section last_update_attempt_response'>
                    Sunday Jun 23, 2019 15:24  
                  </h3>
                </div>
              </div>
            </div>

          </div>
          <div class='col-md-6 col-sm-6 section2'>
            <div class='row' >
              <div class='col-md-12 col-sm-12'>
                <div class='col-md-6 col-sm-6'>
                  <h3 class='detail_section'>
                    Last Successfull Update:
                  </h3>
                </div>
                <div class='col-md-6 col-sm-6'>
                  <h3 class='detail_section last_successfull_update'>
                    Sunday Jun 23, 2019 15:24 
                  </h3>
                </div>
              </div>
            </div>


            <div class='row' >
              <div class='col-md-12 col-sm-12'>
                <div class='col-md-6 col-sm-6'>
                  <h3 class='detail_section'>
                    Latest Record Date:
                  </h3>
                </div>
                <div class='col-md-6 col-sm-6'>
                  <h3 class='detail_section latest_record_date'>
                    Sunday Jun 23, 2019 15:24  
                  </h3>
                </div>
              </div>
            </div>
            

          </div>
        </div>
      </div>

      <div class='row'>
        <div class='col-md-12 col-sm-12'>
          <h1> Feeds </h1>

          <table class="table table-striped" id='provider_view_feed_table'>
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Title</th>
                <th scope="col">Description</th>
                <th scope="col">Published On Feed</th>
                <th scope="col">Detected by App</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
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
  get_provider_view_data();
</script>