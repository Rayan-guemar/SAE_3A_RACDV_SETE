const visibilityToggler = document.getElementById('toggle-eye');
const passwordInput = document.getElementById('x_password');

visibilityToggler.addEventListener('click', () => {
    if (visibilityToggler.classList.contains('opened_eye')) {
        visibilityToggler.classList.remove('opened_eye');
        visibilityToggler.src = '/icons/closed_eye.png';
        passwordInput.setAttribute('type', 'text');
    } else {
        visibilityToggler.classList.add('opened_eye');
        visibilityToggler.src = '/icons/opened_eye.png';
        passwordInput.setAttribute('type', 'password');
    }
})