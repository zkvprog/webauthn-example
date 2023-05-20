class WebAuthnConfig {
    constructor() {
        this.getRegisterArgsEndpoint = "";
        this.registerEndpoint = "";
        this.registerSuccess = function (){};
        this.registerFail = function (){};
        this.getAuthenticateArgsEndpoint = "";
        this.authenticateEndpoint = "";
        this.authenticateSuccess = function (){};
        this.authenticateFail = function (){};
        this.logErrors = true;
        this.logSuccess = false;
    }
}

export {WebAuthnConfig}