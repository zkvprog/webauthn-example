<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<div class="container">
    <form class="center-block" action="" method="POST" enctype="multipart/form-data">
        <div class="form-error"></div>
        <div>
            <label for="username">Username:</label>
            <input required type="text" name="username" id="username"
                   autocomplete="username webauthn">

            <label for="username">Password:</label>
            <input required type="password" name="password" id="password"
                   autocomplete="username webauthn">
        </div>
        <div class="button-list" style="margin-top: 10px">
            <button class="btn" type="button" id="registerBtn">Sign up</button>
            <button class="btn" type="button" id="authBtn">Log in</button>
        </div>
        <hr>
        <div class="button-list" style="margin-top: 10px">
            <button class="btn" type="button" onclick="_webAuthn.authenticate()">Log in with Webauthn</button>
        </div>
    </form>
    <script src="/public/bundle.js"></script>
</div>
</body>
</html>