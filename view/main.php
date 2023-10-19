<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<div class="container">
    <div class="form-block">
        <div id="form-group" class="form-container" action="" method="POST" enctype="multipart/form-data">
            <div class="signup">
                <form action="" method="POST" enctype="multipart/form-data">
                    <h2 class="form-title" id="signup"><span>or</span>Sign up</h2>
                    <div class="form-fields">
                        <label for="username">Username:</label>
                        <input required type="text" name="username" id="username"
                               autocomplete="username webauthn">

                        <label for="username">Password:</label>
                        <input required type="password" name="password" id="password"
                               autocomplete="username webauthn">

                        <div class="button-list" style="margin-top: 10px">
                            <button class="btn" type="button" id="registerBtn">Sign up</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="login form-active">
                <div class="login-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <h2 class="form-title slide-up" id="login"><span>or</span>Log in</h2>
                        <div class="form-fields">
                            <label for="username">Username:</label>
                            <input required type="text" name="username" id="username"
                                   autocomplete="username webauthn">

                            <label for="username">Password:</label>
                            <input required type="password" name="password" id="password"
                                   autocomplete="username webauthn">

                            <div class="button-list" style="margin-top: 10px">
                                <button class="btn" type="button" id="authBtn">Log in</button>
                                <button class="btn" type="button" onclick="_webAuthn.authenticate()">Log in with Webauthn</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="/public/bundle.js"></script>
</div>
</body>
</html>