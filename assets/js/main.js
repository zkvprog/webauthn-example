import {WebAuthnConfig} from './WebAuthnConfig.js';
import {WebAuthn} from './WebAuthn.js';

let config = new WebAuthnConfig();
config.getRegisterArgsEndpoint += "webauthn_get_args?cmd=getRegisterArgs";
config.registerEndpoint += "webauthn_register";
config.getAuthenticateArgsEndpoint += "webauthn_get_args?cmd=getAuthenticateArgs";
config.authenticateEndpoint += "webauthn_authenticate";
config.authenticateSuccess = function () {
    window.location.reload();
};
config.logErrors = true;
config.logSuccess = true;

window._webAuthn = new WebAuthn(config);
