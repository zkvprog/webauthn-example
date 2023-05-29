<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
    <div class="container">
        <div class="center-block">
            <div class="greeting">Hello <?=$user_name?>!</div>
            <div class="button-list" style="margin-top: 10px">
                <button class="btn"type="button" id="logoutBtn">Logout</button>
                <button class="btn" type="button" onclick="_webAuthn.register()">Add webauthn credentials</button>
            </div>
        </div>
    </div>
    <script src="/public/bundle.js"></script>
</body>
</html>