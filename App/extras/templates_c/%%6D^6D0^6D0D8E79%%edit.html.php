<?php /* Smarty version 2.6.31, created on 2022-06-28 14:58:20
         compiled from edit.html */ ?>
<form method="post">
    <!-- Email input -->
    <center>
        <div class="text-left w-25 mt-2">
            <h1 class="display-5 mb-2">Edit User</h1>
            <div class="form-outline mb-2">
                <input type="text" name="firstName" id="firstName" class="form-control" required/>
                <label class="form-label">First Name</label>
            </div>

            <div class="form-outline mb-2">
                <input type="text" name="lastName" id="lastName" class="form-control" required/>
                <label class="form-label">Last Name</label>
            </div>

            <div class="form-outline mb-2">
                <input type="email" name="email" id="email" class="form-control" required/>
                <label class="form-label">Email address</label>
            </div>

            <!-- Password input -->
            <div class="form-outline mb-2">
                <input type="password" name="password" id="password" class="form-control" required/>
                <label class="form-label">Password</label>
            </div>
            

            <!-- 2 column grid layout for inline styling -->
            <!-- <div class="row mb-2">
      <div class="col d-flex justify-content-center">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="" checked />
          <label class="form-check-label"> Remember me </label>
        </div>
      </div>
  
      <div class="col">
        <a href="#!">Forgot password?</a>
      </div>
    </div> -->

            <!-- Submit button -->
            <button type="button" class="btn btn-dark btn-block submit mb-2">
                Login
            </button>
        </div>
    </center>
</form>