<?php /* Smarty version 2.6.31, created on 2022-07-12 15:32:46
         compiled from update.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'update.html', 20, false),)), $this); ?>
<div class="container">
  <div class="content">
      <div class="row">
        <div class="col-lg-6 offset-sm-3 mt-3">
          <div class="card">
            <div class="card-header">
              <h2>Edit Leave Status
              </h2>
            </div>
            <div class="card-body offset-sm-3">
        <form method="post" id="editStatus">
          <input type="hidden" name="id" value="<?php echo $this->_tpl_vars['dataRow']['id']; ?>
">
          <div class="row">
          <label class="col-md-4"><b>Leave Type</b></label>:
          <span class="col-md-4"><?php if ($this->_tpl_vars['dataRow']['type'] == 0): ?>Sick<?php elseif ($this->_tpl_vars['dataRow']['type'] == 1): ?>Paid<?php elseif ($this->_tpl_vars['dataRow']['type'] == 2): ?>Unpaid<?php endif; ?></span>
        </div>

        <div class="row mt-4">
          <label class="col-md-4"><b>From</b></label>:
          <span class="col-md-4"><?php echo ((is_array($_tmp=$this->_tpl_vars['dataRow']['startDate'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d') : smarty_modifier_date_format($_tmp, '%Y-%m-%d')); ?>
</span>
        </div>

        <div class="row mt-4">
          <label class="col-md-4"><b>To</b></label>:
          <span class="col-md-4"><?php echo ((is_array($_tmp=$this->_tpl_vars['dataRow']['endDate'])) ? $this->_run_mod_handler('date_format', true, $_tmp, '%Y-%m-%d') : smarty_modifier_date_format($_tmp, '%Y-%m-%d')); ?>
</span>
        </div>

        <div class="row mt-4">
          <label class="col-md-4"><b>Half Leave</b></label>:
          <span class="col-md-4"><?php if ($this->_tpl_vars['dataRow']['isHalf'] == 0): ?>No<?php else: ?>Yes<?php endif; ?></span>
        </div>

        <div class="row mt-4">
          <label class="col-md-4"><b>Comment</b></label>:
          <span class="col-md-4"><?php echo $this->_tpl_vars['dataRow']['comment']; ?>
</span>
        </div>

        <div class="row mt-4">
          <label class="col-md-4"><b>Status</b></label>:
          <select name="status" class="form-control form-control-sm col-md-4 mb-3 ml-3">
            <option value="0">Pending</option>
            <option value="1">Approved</option>
            <option value="2">Rejected</option>
          </select>
        </div>
        </div>
        <div class="card-footer d-flex justify-content-center">
        <button type="button" class="btn btn-primary submit">Update Status</button>
        </div>
      </div>
        </form>
      </div>
    </div>
  </div>
  <!-- /.col-md-6 -->
  <!-- /.col-md-6 -->
</div>
<!-- /.row -->
</div><!-- /.container-fluid -->
</div>
        <?php echo '
<script>
    $(document).ready(function()
    {
        $(".submit").click(function()
        {
            var data = $("form#editStatus").serializeArray();
            $.ajax({
                url: "/admin/leaves/update/",
                type: "POST",
                dataType: "json",
                data: data, 
                success: function (result) {
                  if(result.status == 1)
                  {
                    window.location.href = "/admin/leaves";
                  }
                  else
                  {
                    console.log(result.errors);
                  }
                }
            });
        });
    });
</script>
'; ?>