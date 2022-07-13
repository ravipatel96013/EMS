<?php /* Smarty version 2.6.31, created on 2022-07-11 14:11:41
         compiled from add.html */ ?>
<div class="container">
  <div class="content">
      <div class="row">
        <div class="col-lg-6 offset-sm-3 mt-3">
          <div class="card">
            <div class="card-header">
              <h2>Add User</h2>
            </div>
            <div class="card-body">
              <form class="form-horizontal" method="post" id="addUser">
                <div class="form-group row">
                    <div class="col-sm-6">
                    <input type="text" class="form-control form-control-sm" name="firstName" id="firstName" placeholder="First Name">
                  </div>
                  <div class="col-sm-6">
                    <input type="text" class="form-control form-control-sm" name="lastName" id="lastName" placeholder="Last Name">
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-sm-6">
                    <input type="text" class="form-control form-control-sm" name="city" id="city" placeholder="City">
                  </div>
                  <div class="col-sm-6">
                    <input type="text" class="form-control form-control-sm" name="state" id="state" placeholder="State">
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-sm-12">
                    <textarea class="form-control form-control-sm" id="address" name="address" rows="3" placeholder="Address..."></textarea>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-sm-6">
                    <input class="form-control form-control-sm" type="text" name="designation" id="designation" placeholder="Designation">
                  </div>
                  <div class="col-sm-6">
                    <select class="form-control form-control-sm" name="role" id="role">
                      <option value="user">User</option>
                      <option value="admin">Admin</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-sm-6">
                    <input type="email" class="form-control form-control-sm" name="email" id="email" placeholder="E-mail">
                  </div>
                  <div class="col-sm-6">
                    <input type="tel" class="form-control form-control-sm" name="phone" id="phone" placeholder="Phone">
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-sm-6">
                    <input type="password" class="form-control form-control-sm" name="password" id="password" placeholder="Password">
                  </div>
                  <div class="col-sm-6">
                    <input type="password" class="form-control form-control-sm" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password">
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-sm-6">
                    <label for="joinDate"><b>Date of Joining</b></label>
                  <input type="date" class="form-control form-control-sm" name="joinDate" id="joinDate" >
                </div>
                </div>
                <div class="card-footer col-sm-6">
                  <button type="button" class="btn btn-primary submit">Add User</button>
                </div>
                
                </div>
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
            var userdata = $("form#addUser").serializeArray();
            $.ajax({
                url: "/admin/users/add/",
                type: "POST",
                dataType: "json",
                data: userdata,
                success: function (result) {
                  if(result.status == 1)
                  {
                    window.location.href = "/admin/users";
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

