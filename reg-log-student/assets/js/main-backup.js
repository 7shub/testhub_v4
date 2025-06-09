// Variable Declaration

const loginBtn = document.querySelector('#login');
const registerBtn = document.querySelector('#register');
const loginForm = document.querySelector('.login-form');
const registerForm = document.querySelector('.register-form');

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
