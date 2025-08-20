userArea = document.getElementById('userArea');
userInfo = document.getElementById('userInfo');
let userNavVisible = false;
userArea.classList.remove('show');


userArea.addEventListener('click', function () {
    userNavVisible = !userNavVisible;
    if (userNavVisible) {
        userArea.classList.remove('hidden');
        userArea.classList.add('show');
    } else {
        userArea.classList.add('hidden');
        userArea.classList.remove('show');
    }
});