<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="container">
        <div class="center-block">
            <div class="greeting">Hello {{user_name}}!</div>
            <div class="button-list" style="margin-top: 10px">
                <button class="btn"type="button" onclick="logout()">Logout</button>
                <button class="btn" type="button" onclick="_webAuthn.register()">Add webauthn credentials</button>
            </div>
        </div>
    </div>
    <script src="js/index.js"></script>
    <script type="module" src="js/main.js"></script>
</body>
</html>