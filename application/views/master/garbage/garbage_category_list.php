<!-- Page header -->
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home</span> - Garbage Category</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a> </div>
        <div class="header-elements d-none">
            <div class="d-flex justify-content-center"> </div>
        </div>
    </div>
    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb"> <a href="index.html" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a> <span class="breadcrumb-item active">Garbage Category</span> </div>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a> </div>

    </div>
</div>
<!-- /page header --> 
<!-- Content area -->
<div class="content"> 

    <!-- Main Table -->
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">Garbage Category List</h5>
            <div class="header-elements"> <a href="<?php echo site_url('master') . '/garbage/add_garbage'; ?>" class="btn bg-success-400 legitRipple align-self-right" target="_self"><i class="icon-lifebuoy mr-2"></i>Add Garbage Category</a> </div>
        </div>
        <div class="container-fluid">
            <?php
            if (isset($this->session->flashdata('temp_data')['color']) || isset($this->session->flashdata('temp_data')['msg'])) {
                $color = $this->session->flashdata('temp_data')['color'];
                $msg = $this->session->flashdata('temp_data')['msg'];
                ?>
                <div class="<?php echo $color; ?>" <?php if (!empty($color) && !empty($msg)) { ?>>
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>                
                        <?php echo $msg; ?>
                    </div>
                <?php
                }
            }
            ?>
        </div>
        <table class="table datatable-colvis-state" style="width: 100%;">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Garbage Category</th>
                    <th>Last Updated</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($result['value'] as $key => $value) {
                    $status = $value['status'] == 'TRUE' ? 'In-Active' : 'Active';
                    $statuscol = $value['status'] == 'TRUE' ? 'Active' : 'In-Active';
                    $color = $value['status'] == 'TRUE' ? 'badge badge-success' : 'badge badge-secondary';
                    ?>
                    <tr>
                        <td><?php echo $key + 1; ?></td>
                        <td><?php echo $value['garbage']; ?></td>
                        <td><?php echo date('d/M/Y', strtotime($value['timestamp'])); ?></td>
                        <td><span class="<?php echo $color; ?>"><?php echo $statuscol; ?></span></td>
                        <td class="text-center"><div class="list-icons">
                                <div class="dropdown"> <a href="" class="list-icons-item" data-toggle="dropdown"> <i class="icon-menu9"></i> </a>
                                    <div class="dropdown-menu dropdown-menu-right"> 
                                        <a href="<?php echo site_url('master') . '/garbage/add_garbage/edit/' . $value['id']; ?>" class="dropdown-item"><i class="icon-pencil"></i> Edit</a> 
                                        <a href="<?php echo site_url('master') . '/garbage/garbage_category_list/delete/' . $value['id']; ?>" class="dropdown-item"><i class="icon-arrow-down7"></i><?php echo $status; ?></a> 
                                    </div>
                                </div>
                            </div></td>
                    </tr>
                    <?php
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