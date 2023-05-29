import "../css/main.css"
import "./index.js";

import {WebAuthnConfig} from './WebAuthnConfig.js';
import {WebAuthn} from './WebAuthn.js';

import Toastify from 'toastify-js'
import "toastify-js/src/toastify.css"

Toastify({
    text: "This is a toast",
    duration: 3000
}).showToast();

let config = new WebAuthnConfig();
config.getRegisterArgsEndpoint += "webauthn_get_args?cmd=getRegisterArgs";
config.registerEndpoint += "webauthn_register";
config.registerSuccess = function (message) {
    Toastify({
        text: message,
        duration: 3000,
        newWindow: true,
        close: true,
        gravity: "top", // `top` or `bottom`
        position: "left", // `left`, `center` or `right`
        stopOnFocus: true, // Prevents dismissing of toast on hover
        style: {
            background: "linear-gradient(to right, #00b09b, #96c93d)",
        }
    }).showToast();
};
config.getAuthenticateArgsEndpoint += "webauthn_get_args?cmd=getAuthenticateArgs";
config.authenticateEndpoint += "webauthn_authenticate";
config.authenticateSuccess = function () {
    window.location.reload();
};
config.logErrors = true;
config.logSuccess = true;

window._webAuthn = new WebAuthn(config);
