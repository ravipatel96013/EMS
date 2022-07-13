<?php /* Smarty version 2.6.31, created on 2022-07-13 16:45:31
         compiled from update.html */ ?>
<div class="container">
  <div class="content">
      <div class="row">
        <div class="col-lg-6 offset-sm-3 mt-3">
          <div class="card">
            <div class="card-header">
              <h2>Edit Holiday</h2>
            </div>
            <div class="card-body offset-sm-3">
          <form method="post" id="editHoliday">
            <input type="hidden" name="id" value="<?php echo $this->_tpl_vars['dataRow']['id']; ?>
">
            <div class="row">
            <label class="col-md-4"><b>Name</b></label>
            <input type="text" name="name" value="<?php echo $this->_tpl_vars['dataRow']['name']; ?>
" class="form-control form-control-sm col-md-4">
          </div>

          <div class="row mt-4">
            <label class="col-md-4"><b>Date</b></label>
            <input type="date" name="date" value="<?php echo $this->_tpl_vars['dataRow']['date']; ?>
" class="form-control form-control-sm col-md-4">
          </div>

          <div class="row mt-4">
            <label class="col-md-4"><b>Description</b></label>
            <textarea type="text" name="description" class="form-control form-control-sm col-md-4"><?php echo $this->_tpl_vars['dataRow']['description']; ?>
</textarea>
          </div>
          </div>
          <div class="card-footer d-flex justify-content-center">
            <button type="button" class="btn btn-primary submit">Edit Holiday</button>
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
            var holidayData = $("form#editHoliday").serializeArray();
            $.ajax({
                url: "/admin/holidays/update/",
                type: "POST",
                dataType: "json",
                data: holidayData, 
                success: function (result) {
                  if(result.status == 1)
                  {
                    window.location.href = "/admin/holidays";
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