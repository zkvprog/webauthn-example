import Toastify from "toastify-js";

async function registerUser(event) {
    const form = event.target.closest('form');

    try {
        if (form.checkValidity()) {
            let register = await window.fetch('signup', {
                method  : 'POST',
                body    : new FormData(form),
                cache   : 'no-cache'
            });
            register = await register.json();

            if (register.success) {
                window.location.reload();
            } else {
                throw new Error(register.message);
            }
        } else {
            toastError('Check your form');
        }
    } catch(err) {
        console.error(err);
        toastError(err);
    }
}

async function authUser(event) {
    const form = event.target.closest('form');

    try {
        if (form.checkValidity()) {
            const auth = await window.fetch('login', {
                method  : 'POST',
                body    : new FormData(form),
                cache   : 'no-cache'
            });
            const authResponse = await auth.json();

            if (authResponse.success) {
                window.location.reload();
            } else {
                throw new Error(authResponse.message);
            }
        } else {
            toastError('Check your form');
        }
    } catch(err) {
        console.error(err);
        toastError(err);
    }
}

async function logout(event) {
    try {
        let logout = await window.fetch('logout', {
            method  : 'GET',
            cache   : 'no-cache'
        });
        logout = await logout.json();
        if (logout.success) {
            window.location.reload();
        } else {
            throw new Error(logout.msg);
        }
    } catch(err) {
        console.error(err);
    }
}

function toastError (error) {
    Toastify({
        text: error,
        duration: -1,
        close: true,
        style: {
            background: "linear-gradient(to right, rgb(255, 95, 109), rgb(255, 195, 113))",
        }
    }).showToast();
}

window.onload = (event) => {
    const registerBtn = document.getElementById("registerBtn");
    const authBtn = document.getElementById("authBtn");
    const logoutBtn = document.getElementById("logoutBtn");

    if (registerBtn) {
        registerBtn.addEventListener("click", registerUser);
    }

    if (authBtn) {
        authBtn.addEventListener("click", authUser);
    }

    if (logoutBtn) {
        logoutBtn.addEventListener("click", logout);
    }

    const formContainer = document.getElementById('form-group');
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('slide-up')) {
            formContainer.querySelectorAll('.form-title').forEach((el) => el.classList.toggle("slide-up"));
            formContainer.querySelectorAll('.signup, .login').forEach((el) => el.classList.toggle("form-active"));
        }
    });
};