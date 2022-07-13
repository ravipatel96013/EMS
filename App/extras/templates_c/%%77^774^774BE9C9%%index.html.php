<?php /* Smarty version 2.6.31, created on 2022-07-13 16:59:30
         compiled from index.html */ ?>
<div class="container">
  <div class="d-flex justify-content-center">
    <h1 class="display-5" id="currentTime"></h1>
  </div>
  <div class="row">
    <div id="button" class="d-flex justify-content-center"></div>
  </div>
  <div id="breakButton" class="d-flex justify-content-center mt-2">
  </div>
</div>
<?php echo '
<script>
  $(document).ready(function () {
    $.ajax({
      url: "/app/index/checkstatus",
      type: "GET",
      dataType: "json",
      success: function (response) {
        if (response.status == 1) {
          document.getElementById("button").innerHTML =
            "<h1 class=\'display-5\'>Done for the day</h1>";
        } else if (response.status == 2) {
          $("#checkout").prop("disabled", true);
          document.getElementById("button").innerHTML =
            "<button type=\'button\' class=\'col-sm-3 btn btn-block bg-gradient-success btn-lg\' onclick=\'doCheckIn()\' id=\'checkin\'>Check-in</button>";
        } else if (response.status == 3) {
          document.getElementById("breakButton").innerHTML = 
          "<button type=\'button\' onclick=\'endBreak()\' class=\'col-sm-2 btn btn-block btn-danger\'>End Break</button>";
        }
        else if(response.status == 4)
        {
          document.getElementById("button").innerHTML =
            "<button type=\'button\' class=\'col-sm-3 btn btn-block bg-gradient-danger btn-lg\' onclick=\'doCheckOut()\' id=\'checkout\'>Check-out</button>";
          document.getElementById("breakButton").innerHTML = 
          "<button type=\'button\' onclick=\'startBreak()\' class=\'col-sm-2 btn btn-block btn-success\'>Start Break</button>";
        }
      },
    });
  });
</script>

<script>
  function doCheckIn() {
    $.ajax({
      url: "/app/index/checkin",
      type: "POST",
      dataType: "json",
      success: function (response) {
        if (response.status == 1) {
          alert("You CheckedIn Successfully");
          location.reload();
        } else {
          consol.log(response.errors);
        }
      },
    });
  }
</script>

<script>
  function doCheckOut() {
    $.ajax({
      url: "/app/index/checkout",
      type: "POST",
      dataType: "json",
      success: function (response) {
        if (response.status == 1) {
          alert("You CheckedOut Successfully");
          location.reload();
        } else {
          consol.log(response.errors);
        }
      },
    });
  }
</script>

<script>
  function startBreak() {
    $.ajax({
      url: "/app/index/startbreak",
      type: "POST",
      dataType: "json",
      success: function (response) {
        if (response.status == 1) {
          alert("You Break Started");
          location.reload();
        } else {
          consol.log(response.errors);
        }
      },
    });
  }
</script>

<script>
  function endBreak() {
    $.ajax({
      url: "/app/index/endbreak",
      type: "POST",
      dataType: "json",
      success: function (response) {
        if (response.status == 1) {
          alert("Break Ended");
          location.reload();
        } else {
          consol.log(response.errors);
        }
      },
    });
  }
</script>

<script>
  window.onload = function () {
    clock();
    function clock() {
      var now = new Date();
      var TwentyFourHour = now.getHours();
      var hour = now.getHours();
      var min = now.getMinutes();
      var sec = now.getSeconds();
      var mid = "pm";
      if (min < 10) {
        min = "0" + min;
      }
      if (hour > 12) {
        hour = hour - 12;
      }
      if (hour == 0) {
        hour = 12;
      }
      if (TwentyFourHour < 12) {
        mid = "am";
      }
      document.getElementById("currentTime").innerHTML =
        hour + ":" + min + ":" + sec + " " + mid;
      setTimeout(clock, 1000);
    }
  };
</script>
'; ?>
