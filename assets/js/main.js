import "../css/main.css"
import "./index.js";

import {WebAuthnConfig} from './WebAuthnConfig.js';
import {WebAuthn} from './WebAuthn.js';

import Toastify from 'toastify-js'
import "toastify-js/src/toastify.css"

let config = new WebAuthnConfig();
config.getRegisterArgsEndpoint += "webauthn_get_args?cmd=getRegisterArgs";
config.registerEndpoint += "webauthn_register";
config.registerSuccess = function (message) {
    Toastify({
        text: message,
        duration: 3000,
        style: {
            background: "linear-gradient(to right, #00b09b, #96c93d)",
        }
    }).showToast();
};
config.registerFail = function (error) {
    Toastify({
        text: error,
        duration: 3000,
        style: {
            background: "linear-gradient(to right, #ff5f6d, #ffc371)",
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
