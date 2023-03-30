import {WebAuthnConfig} from './WebAuthnConfig';
import {WebAuthn} from './WebAuthn';

let config = new WebAuthnConfig();
config.getRegisterArgsEndpoint += "webauthn_get_args.php?cmd=getRegisterArgs";
config.registerEndpoint += "webauthn_register.php";
config.getAuthenticateArgsEndpoint += "webauthn_get_args.php?cmd=getAuthenticateArgs";
config.authenticateEndpoint += "webauthn_authenticate.php";
config.authenticateSuccess = function () {
    window.location.reload();
};
config.logErrors = true;
config.logSuccess = true;

window._webAuthn = new WebAuthn(config);
