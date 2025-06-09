// Variable Declaration
const loginBtn = document.querySelector('#login');
const registerBtn = document.querySelector('#register');
const loginForm = document.querySelector('.login-form');
const registerForm = document.querySelector('.register-form');
const successPopup = document.getElementById('successPopup');

// Login button function
loginBtn.addEventListener('click', () => {
  loginBtn.style.backgroundColor = '#21264d';
  registerBtn.style.backgroundColor = 'rgba(255, 255, 255, 0.2)';

  loginForm.style.left = '50%';
  registerForm.style.left = '-50%';

  loginForm.style.opacity = 1;
  registerForm.style.opacity = 0;

  document.querySelectorAll('.col-1').forEach((el) => {
    el.style.borderRadius = '0 30% 20% 0';
  });
});

// Register button function
registerBtn.addEventListener('click', () => {
  registerBtn.style.backgroundColor = '#21264d';
  loginBtn.style.backgroundColor = 'rgba(255, 255, 255, 0.2)';

  loginForm.style.left = '150%';
  registerForm.style.left = '50%';

  loginForm.style.opacity = 0;
  registerForm.style.opacity = 1;

  document.querySelectorAll('.col-1').forEach((el) => {
    el.style.borderRadius = '0 20% 30% 0';
  });
});

// Client-side validation for registration form
registerForm.addEventListener('submit', function (event) {
  let username = document.querySelector("input[name='username']").value.trim();
  let rollNo = document.querySelector("input[name='roll_no']").value.trim();
  let semester = document.querySelector("select[name='semester']").value;
  let email = document.querySelector("input[name='email']").value.trim();
  let password = document.querySelector("input[name='password']").value.trim();

  // Username validation
  if (!/^[a-zA-Z0-9_]{4,}$/.test(username)) {
    alert(
      'Username must be at least 4 characters long and contain only letters, numbers, and underscores.'
    );
    event.preventDefault();
    return;
  }

  // Roll number validation
  if (!/^\d+$/.test(rollNo)) {
    alert('Roll number must be numeric.');
    event.preventDefault();
    return;
  }

  // Semester validation
  if (!semester) {
    alert('Please select a semester.');
    event.preventDefault();
    return;
  }

  // Email validation
  if (!/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test(email)) {
    alert('Invalid email format.');
    event.preventDefault();
    return;
  }

  // Password validation
  if (!/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/.test(password)) {
    alert(
      'Password must be at least 8 characters long and contain at least one letter and one number.'
    );
    event.preventDefault();
  }
});

// Login form validation
loginForm.addEventListener('submit', function (event) {
  let username = document
    .querySelector(".login-form input[name='username']")
    .value.trim();
  let password = document
    .querySelector(".login-form input[name='password']")
    .value.trim();

  if (username === '' || password === '') {
    alert('All fields are required.');
    event.preventDefault();
  }
});

// Show success popup if session variable is set (handled via PHP)
document.addEventListener('DOMContentLoaded', function () {
  if (successPopup) {
    successPopup.style.display = 'block';
    setTimeout(() => {
      loginBtn.click(); // Redirect to login form after 3 seconds
      successPopup.style.display = 'none'; // Hide popup
    }, 3000);
  }
});
