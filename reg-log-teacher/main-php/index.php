<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Testhub | Teacher Login</title>
    <!-- BOXICONS -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css"
    />

    <!-- STYLE -->
    <link rel="stylesheet" href="../assets/css/style.css" />
  </head>
  <body>
    <!-- Form Container -->
    <div class="form-container">
      <div class="col col-1">
        <div class="image-layer">
          <img
            src="../assets/img/white-outline.png"
            alt="Login Image"
            class="form-image-main"
          />
          <img
            src="../assets/img/dots.png"
            alt="Login Image"
            class="form-image dots"
          />
          <img
            src="../assets/img/coin.png"
            alt="Login Image"
            class="form-image coin"
          />
          <img
            src="../assets/img/spring.png"
            alt="Login Image"
            class="form-image spring"
          />
          <img
            src="../assets/img/rocket.png"
            alt="Login Image"
            class="form-image rocket"
          />
          <img
            src="../assets/img/cloud.png"
            alt="Login Image"
            class="form-image cloud"
          />
          <img
            src="../assets/img/stars.png"
            alt="Login Image"
            class="form-image stars"
          />
        </div>
        <p class="featured-words">
          You Are Few Minutes Away From Managing Your <span>Testhub</span> Exams
        </p>
      </div>
      <div class="col col-2">
        <div class="btn-box">
          <button class="btn btn-1" id="login">Sign In</button>
          <button class="btn btn-2" id="register">Sign Up</button>
        </div>
        <!-- Login Form -->
        <form action="../php/teach-auth.php" method="POST" class="login-form">
          <div class="form-title">
            <span>Sign In</span>
          </div>
          <div class="form-inputs">
            <div class="input-box">
              <input
                type="text"
                class="input-field"
                name="username"
                placeholder="Username"
                required
              />
              <i class="bx bx-user icon"></i>
            </div>
            <div class="input-box">
              <input
                type="password"
                class="input-field"
                name="password"
                placeholder="Password"
                required
              />
              <i class="bx bx-lock-alt icon"></i>
            </div>
            <div class="forgot-pass">
              <a href="#">Forgot Password?</a>
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
        <form action="../php/teach-auth.php" method="POST" class="register-form">
          <div class="form-title">
            <span>Create Account</span>
          </div>
          <div class="form-inputs">
            <div class="input-box">
              <input
                type="text"
                class="input-field"
                name="username"
                placeholder="Username"
                required
              />
              <i class="bx bx-user icon"></i>
            </div>
            <div class="input-box">
              <input
                type="email"
                class="input-field"
                name="email"
                placeholder="Email"
                required
              />
              <i class="bx bx-envelope icon"></i>
            </div>
            <div class="input-box">
              <input
                type="password"
                class="input-field"
                name="password"
                placeholder="Password"
                required
              />
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

    <!-- JS -->
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
