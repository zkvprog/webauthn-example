async function registerUser(event) {
    const form = event.target.closest('form');
    const errorPanel = form.querySelector('.form-error');

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
            errorPanel.textContent = 'Check your form';
        }
    } catch(err) {
        console.error(err);
        errorPanel.textContent = err;
    }
}

async function authUser(event) {
    const form = event.target.closest('form');
    const errorPanel = form.querySelector('.form-error')

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
            errorPanel.textContent = 'Check your form';
        }
    } catch(err) {
        console.error(err);
        errorPanel.textContent = err;
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

window.onload = (event) => {
    document.getElementById("registerBtn").addEventListener("click", registerUser);
    document.getElementById("authBtn").addEventListener("click", authUser);
    document.getElementById("logoutBtn").addEventListener("click", logout);
};