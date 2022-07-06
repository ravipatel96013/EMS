<?php /* Smarty version 2.6.31, created on 2022-07-04 16:10:22
         compiled from add.html */ ?>
         <!-- Horizontal Form -->
            <div class="card-header col-sm-4 offset-sm-4 mt-3 rounded" style="background-color:#343a40;">
              <h3 class="card-title" style="color: white;">Add User</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form class="form-horizontal" method="post" id="addUser">
              <div class="card-body">
                <div class="form-group row">
                    <div class="col-sm-2 offset-sm-4">
                    <input type="text" class="form-control form-control-sm" name="firstName" id="firstName" placeholder="First Name">
                  </div>
                  <div class="col-sm-2">
                    <input type="text" class="form-control form-control-sm" name="lastName" id="lastName" placeholder="Last Name">
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-sm-2 offset-sm-4">
                    <input type="text" class="form-control form-control-sm" name="city" id="city" placeholder="City">
                  </div>
                  <div class="col-sm-2">
                    <input type="text" class="form-control form-control-sm" name="state" id="state" placeholder="State">
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-sm-4 offset-sm-4">
                    <textarea class="form-control form-control-sm" id="address" name="address" rows="3" placeholder="Address..."></textarea>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-sm-2 offset-sm-4">
                    <input class="form-control form-control-sm" type="text" name="designation" id="designation" placeholder="Designation">
                  </div>
                  <div class="col-sm-2">
                    <select class="form-control form-control-sm" name="role" id="role">
                      <option value="user">User</option>
                      <option value="admin">Admin</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-sm-2 offset-sm-4">
                    <input type="email" class="form-control form-control-sm" name="email" id="email" placeholder="E-mail">
                  </div>
                  <div class="col-sm-2">
                    <input type="tel" class="form-control form-control-sm" name="phone" id="phone" placeholder="Phone">
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-sm-2 offset-sm-4">
                    <input type="password" class="form-control form-control-sm" name="password" id="password" placeholder="Password">
                  </div>
                  <div class="col-sm-2">
                    <input type="password" class="form-control form-control-sm" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password">
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-sm-2 offset-sm-4">
                    <label for="joinDate"><b>Date of Joining</b></label>
                  <input type="date" class="form-control form-control-sm" name="joinDate" id="joinDate" >
                </div>
                </div>
                <div class="card-footer col-sm-4 offset-sm-4">
                  <button type="button" class="btn btn-dark submit">Add User</button>
                </div>
                
                </div>
                  </div>
                </div>
              </div>
              <!-- /.card-body -->
              <!-- /.card-footer -->
            </form>
          
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

