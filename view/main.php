<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="css/main.css">
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
            <button class="btn" type="button" onclick="registerUser(event)">Sign up</button>
            <button class="btn" type="button" onclick="authUser(event)">Log in</button>
        </div>
        <hr>
        <div class="button-list" style="margin-top: 10px">
            <button class="btn" type="button" onclick="_webAuthn.authenticate()">Log in with Webauthn</button>
        </div>
    </form>
    <script src="js/index.js"></script>
    <script type="module" src="js/main.js"></script>
</div>
</body>
</html>