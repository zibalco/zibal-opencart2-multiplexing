<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-zibal" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>

  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form id="form-zibal" method="post" enctype="multipart/form-data" action="<?php echo $action; ?>" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="zibal_merchant"><?php echo $entry_merchant_id; ?></label>
                <div class="col-sm-10">
                  <input name="zibal_merchant" type="text" placeholder="<?php echo $placeholder_merchant; ?>" value="<?php echo $zibal_merchant; ?>" class="form-control" style="direction:rtl;" />
                  <?php if ($error_merchant) { ?><div class="text-danger"><?php echo $error_merchant; ?></div><?php } ?>
                </div>
              </div>
<div class="form-group">
                <label class="col-sm-2 control-label" for="zibal_direct"><?php echo $entry_zibal_direct; ?></label>
                <div class="col-sm-10">
                  <select name="zibal_direct" class="form-control">
                    <?php if ($zibal_direct=="yes") { ?>
                    <option value="yes" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="no"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="yes"><?php echo $text_enabled; ?></option>
                    <option value="no" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="zibal_order_status_id"><?php echo $entry_order_status; ?></label>
                <div class="col-sm-10">
                  <select name="zibal_order_status_id" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $zibal_order_status_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="zibal_failed_status_id"><span data-toggle="tooltip" title="<?php echo $help_failed_status; ?>"><?php echo $entry_failed_status; ?></span></label>
                <div class="col-sm-10">
                  <select name="zibal_failed_status_id" class="form-control">
                    <option value="0"><?php echo $text_none; ?></option>
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $zibal_failed_status_id) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="zibal_status"><?php echo $entry_status; ?></label>
                <div class="col-sm-10">
                  <select name="zibal_status" class="form-control">
                    <?php if ($zibal_status) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="zibal_sort_order"><?php echo $entry_sort_order; ?></label>
                <div class="col-sm-10">
                  <input name="zibal_sort_order" type="text" value="<?php echo $zibal_sort_order; ?>" class="form-control" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="zibal_submerchant1"><?php echo $entry_submerchant1; ?></label>
                <div class="col-sm-10">
                  <input name="zibal_submerchant1" type="text" value="<?php echo $zibal_submerchant1; ?>" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="zibal_submerchant2"><?php echo $entry_submerchant2; ?></label>
                <div class="col-sm-10">
                  <input name="zibal_submerchant2" type="text" value="<?php echo $zibal_submerchant2; ?>" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="zibal_submerchant3"><?php echo $entry_submerchant3; ?></label>
                <div class="col-sm-10">
                  <input name="zibal_submerchant3" type="text" value="<?php echo $zibal_submerchant3; ?>" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="zibal_submerchant4"><?php echo $entry_submerchant4; ?></label>
                <div class="col-sm-10">
                  <input name="zibal_submerchant4" type="text" value="<?php echo $zibal_submerchant4; ?>" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="zibal_submerchant5"><?php echo $entry_submerchant5; ?></label>
                <div class="col-sm-10">
                  <input name="zibal_submerchant5" type="text" value="<?php echo $zibal_submerchant5; ?>" class="form-control" />
                </div>
              </div>
              
            </div>
           
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
