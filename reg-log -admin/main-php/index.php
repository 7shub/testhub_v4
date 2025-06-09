<?php
session_start();

// Check if admin is already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: dashbord.php");
    exit();
}

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Testhub | Admin Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css"/>
  <link rel="stylesheet" href="../assets/css/style.css"/>
</head>
<body>
  <div class="form-container">
    <div class="col col-1">
      <div class="image-layer">
        <img src="../assets/img/white-outline.png" alt="Login Image" class="form-image-main"/>
        <img src="../assets/img/dots.png" class="form-image dots"/>
        <img src="../assets/img/coin.png" class="form-image coin"/>
        <img src="../assets/img/spring.png" class="form-image spring"/>
        <img src="../assets/img/rocket.png" class="form-image rocket"/>
        <img src="../assets/img/cloud.png" class="form-image cloud"/>
        <img src="../assets/img/stars.png" class="form-image stars"/>
      </div>
      <p class="featured-words">
        You Are Few Minutes Away From Managing <span>Testhub</span> Platform
      </p>
    </div>
    <div class="col col-2">
      <div class="btn-box">
        <button class="btn btn-1" id="login">Sign In</button>
        <button class="btn btn-2" id="register">Sign Up</button>
      </div>

      <!-- Login Form -->
      <form action="../php/admin-auth.php" method="POST" class="login-form">
        <div class="form-title"><span>Sign In</span></div>
        <div class="form-inputs">
          <div class="input-box">
            <input type="text" name="username" class="input-field" placeholder="Username" required/>
            <i class="bx bx-user icon"></i>
          </div>
          <div class="input-box">
            <input type="password" name="password" class="input-field" placeholder="Password" required/>
            <i class="bx bx-lock-alt icon"></i>
          </div>
          <div class="input-box">
            <button type="submit" name="login" class="input-submit">
              <span>Sign In</span>
              <i class="bx bx-right-arrow-alt"></i>
            </button>
          </div>
        </div>
      </form>

      <!-- Register Form -->
      <form action="../php/admin-auth.php" method="POST" class="register-form">
        <div class="form-title"><span>Create Account</span></div>
        <div class="form-inputs">
          <div class="input-box">
            <input type="text" name="username" class="input-field" placeholder="Username" required/>
            <i class="bx bx-user icon"></i>
          </div>
          <div class="input-box">
            <input type="email" name="email" class="input-field" placeholder="Email" required/>
            <i class="bx bx-envelope icon"></i>
          </div>
          <div class="input-box">
            <input type="password" name="password" class="input-field" placeholder="Password" required/>
            <i class="bx bx-lock-alt icon"></i>
          </div>
          <div class="input-box">
            <button type="submit" name="register" class="input-submit">
              <span>Sign Up</span>
              <i class="bx bx-right-arrow-alt"></i>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <script src="../assets/js/main.js"></script>
  <script>
    // Disable browser back/forward buttons
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
      history.go(1);
    };

    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href);
    }
  </script>
</body>
</html>
