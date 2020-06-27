 <!-- Page header --> 
    
    <!-- /page header --> 
    
    <!-- Content area -->
    <div class="content"> 
      
      <!-- Main Form -->
      <div class="card">
        <div class="card-body">
          <h5 class="mb-3">Agency Wise Report</h5>
          <form name="agency_wise_report_form" action="<?php echo site_url('report') . $url; ?>" method="post" _lpchecked="1">
            <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                  <label>Agency</label>
                   <select data-placeholder="Select Agency" class="form-control select-search" name="agency" data-fouc >
                    <option></option>
                    <option value="All">All</option>
                    <?php
                    if(count($agencyarr)>0){
                    foreach($agencyarr as $key=>$value){?>
                        <option value="<?php echo $value['agencyname'];?>"><?php echo $value['agencyname'];?></option>
                  <?php  }}
                    ?>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Start Date</label>
                  <input type="text" class="form-control pickadate-accessibility" placeholder="Start Date" name="startdate" required>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>End Date</label>
                  <input type="text" class="form-control pickadate-accessibility" placeholder="End Date" name="enddate" required>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <button type="submit" class="btn btn-primary btn-lg legitRipple mt-2"><i class="icon-statistics mr-1"></i>View Report</button>
                </div>
              </div>
            </div>
            
          </form>
        </div>
      </div>
      <!-- /Main Form --> 
      
      <!-- Main Table -->
      <div class="card">
        <div class="card-header justify-contect-center text-center">
          <h5 class="card-title">Agency Wise Wise Report of Vehicles Coming at SLF From</h5>
			<h6 class="card-title"><?php echo $startdate;?> To <?php echo $enddate;?></h6>
          <div class="header-elements">  </div>
        </div>
        <table class="table datatable-colvis-state" style="width: 100%;">
          <thead>
            <tr>
                    <th>Sr. No.</th>
                    <th>Registration No.</th>
			        <th>Slip No.</th>
                    <th>Agency</th>
                    <th>Zone</th>
			        <th>In Weight</th>
		            <th>Out Weight</th>
			        <th>Net Garbage</th>
                    <th>SLF Entry Time</th>
                    <th>SLF Exit Time</th>
            </tr>
          </thead>
          <tbody>
          <?php 
          if(count($result)>0){
            foreach($result as $key=>$value){
            ?>
            <tr class="text-center">
          <td><?php echo $key+1;?></td>
          <td><?php echo $value['vehicle_no'];?></td>
          <td><?php echo $value['slipno'];?></td>
          <td><?php echo $value['fleetoperator'];?></td>
          <td><?php echo $value['zone_coming_from'];?></td>
          <td><?php echo $value['inweight'];?></td>
          <td><?php echo $value['outweight'];?></td>
          <td><?php echo $value['net_garbage'];?></td>
          <td><?php echo $value['entrytime'];?></td>
          <td><?php echo $value['exittime'];?></td>
          </tr>
          <?php
          }
          }
          ?>
</tbody>
        </table>
      </div>
      <!-- /main table --> 
      
      <!-- Dashboard content --> 
      
      <!-- /dashboard content --> 
      
    </div>
    <!-- /content area --> 
