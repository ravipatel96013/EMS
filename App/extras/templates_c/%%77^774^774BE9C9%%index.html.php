<?php /* Smarty version 2.6.31, created on 2022-07-06 11:41:03
         compiled from index.html */ ?>
<div class="w-50 col col-md-6 offset-md-3 mt-3 text-center">
    <span class="display-6">Leave Balance Sheet</span>
    <div>
<table class="table table-sm mt-3">
    <thead>
      <tr>
        <th scope="col">Id</th>
        <th scope="col">Amount</th>
        <th scope="col">Type</th>
        <th scope="col">Description</th>
        <th scope="col">Action by</th>
        <th scope="col">Date</th>
      </tr>
    </thead>
    <tbody>
        <?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['row']):
?>
      <tr>
        <th scope="row"><?php echo $this->_tpl_vars['row']['id']; ?>
</th>
        <td><?php echo $this->_tpl_vars['row']['amount']; ?>
</td>
        <td><?php echo $this->_tpl_vars['row']['type']; ?>
</td>
        <td><?php echo $this->_tpl_vars['row']['description']; ?>
</td>
        <td><?php echo $this->_tpl_vars['row']['actionTakenBy']; ?>
</td>
        <td><?php echo $this->_tpl_vars['row']['createdOn']; ?>
</td>
      </tr>
      <?php endforeach; endif; unset($_from); ?>
    </tbody>
</table>
</div>
<!-- Button trigger modal -->  
<div class="container">
  <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal">
    Add Holidays
  </button>  
</div>
  <!--  Button trigger modal End -->
      <!--Model-->
      <form method="post" id="addHoliday">
      <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #007bff;">
              <h5 class="modal-title" id="exampleModalLabel">Add Holiday</h5>
            </div>
            <div class="modal-body text-left">

              <div class="row">
              <label class="col-md-3 offset-md-1"><b>Name</b></label>
              <input type="text" name="name" class="form-control form-control-sm col-md-6">
            </div>

            <div class="row mt-4">
              <label class="col-md-3 offset-md-1"><b>Date</b></label>
              <input type="date" name="date" class="form-control form-control-sm col-md-6">
            </div>

            <div class="row mt-4">
              <label class="col-md-3 offset-md-1"><b>Description</b></label>
              <textarea type="text" name="description" class="form-control form-control-sm col-md-6"></textarea>
            </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary add">Add</button>
            </div>
          </div>
        </div>
      </div>
    </form>