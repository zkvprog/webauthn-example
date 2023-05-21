async function registerUser(event) {
    try {
        let form = event.target.closest('form');
        let errorPanel = form.querySelector('.form-error')

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
                throw new Error(register.msg);
            }
        } else {
            errorPanel.textContent = 'Check your form';
        }
    } catch(err) {
        console.error(err);
    }
}

async function authUser(event) {
    try {
        const form = event.target.closest('form');
        const errorPanel = form.querySelector('.form-error')

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
                throw new Error(authResponse.msg);
            }
        } else {
            errorPanel.textContent = 'Check your form';
        }
    } catch(err) {
        console.error(err);
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

