class WebAuthn {
    /**
     *
     * @param {WebAuthnConfig} config
     * @returns {undefined}
     */
    constructor(config) {
        this.config = config;
    }

    /**
     * convert RFC 1342-like base64 strings to array buffer
     * @param {mixed} obj
     * @returns {undefined}
     */
    static recursiveBase64StrToArrayBuffer(obj) {
        let prefix = '=?BINARY?B?';
        let suffix = '?=';
        if (typeof obj === 'object') {
            for (let key in obj) {
                if (typeof obj[key] === 'string') {
                    let str = obj[key];
                    if (str.substring(0, prefix.length) === prefix && str.substring(str.length - suffix.length) === suffix) {
                        str = str.substring(prefix.length, str.length - suffix.length);

                        let binary_string = window.atob(str);
                        let len = binary_string.length;
                        let bytes = new Uint8Array(len);
                        for (let i = 0; i < len; i++)        {
                            bytes[i] = binary_string.charCodeAt(i);
                        }
                        obj[key] = bytes.buffer;
                    }
                } else {
                    WebAuthn.recursiveBase64StrToArrayBuffer(obj[key]);
                }
            }
        }
    }

    /**
     * Convert a ArrayBuffer to Base64
     * @param {ArrayBuffer} buffer
     * @returns {String}
     */
    static arrayBufferToBase64(buffer) {
        let binary = '';
        let bytes = new Uint8Array(buffer);
        let len = bytes.byteLength;
        for (let i = 0; i < len; i++) {
            binary += String.fromCharCode( bytes[ i ] );
        }
        return window.btoa(binary);
    }

    detectSupport() {
        if (window.PublicKeyCredential) {
            PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable()
                .then((available) => {
                    if (!available) {
                        throw new Error(
                            "WebAuthn supported, Platform Authenticator not supported."
                        );
                    }
                })
        } else {
            throw new Error("WebAuthn not supported");
        }
    }

    isSupportLibrary() {
        if (window.fetch && navigator.credentials && navigator.credentials.create) {
            return true;
        } else {
            console.error('Browser not supported');
            return false;
        }
    }

    isSupportWebAuthn() {
        if (window.PublicKeyCredential) {
            return true;
        } else {
            console.error("WebAuthn not supported");
            return false;
        }0
    }

    async authenticate() {
        if (this.isSupportLibrary() && this.isSupportWebAuthn()) {
            try {
                const authenticateArgs = await window.fetch(this.config.getAuthenticateArgsEndpoint, {
                    method: 'GET',
                    cache:'no-cache',
                });

                const authenticateArgsResponse = await authenticateArgs.json();

                if (authenticateArgsResponse.success === false) {
                    throw new Error(createArgs.msg || 'unknown error occured');
                }

                let authArgsResult = authenticateArgsResponse.result;
                WebAuthn.recursiveBase64StrToArrayBuffer(authArgsResult);

                const credential = await navigator.credentials.get(authArgsResult);

                const authenticatorAttestationResponse = {
                    id: credential.rawId ? WebAuthn.arrayBufferToBase64(credential.rawId) : null,
                    clientDataJSON: credential.response.clientDataJSON  ? WebAuthn.arrayBufferToBase64(credential.response.clientDataJSON) : null,
                    authenticatorData: credential.response.authenticatorData ? WebAuthn.arrayBufferToBase64(credential.response.authenticatorData) : null,
                    signature: credential.response.signature ? WebAuthn.arrayBufferToBase64(credential.response.signature) : null,
                    userHandle: credential.response.userHandle ? WebAuthn.arrayBufferToBase64(credential.response.userHandle) : null
                };

                let response = await window.fetch(this.config.authenticateEndpoint, {
                    method:'POST',
                    body: JSON.stringify(authenticatorAttestationResponse),
                    cache:'no-cache'
                });
                const authenticatorAttestationServerResponse = await response.json();

                if (authenticatorAttestationServerResponse.success) {
                    this.handleSuccess(authenticatorAttestationServerResponse.msg || 'login success', this.config.authenticateSuccess);
                } else {
                    throw new Error(authenticatorAttestationServerResponse.msg);
                }
            } catch (err) {
                this.handleError(err, this.config.authenticateFail)
            }
        }
    }

    async register() {
        if (this.isSupportLibrary() && this.isSupportWebAuthn()) {
            try {
                const regArgs = await window.fetch(this.config.getRegisterArgsEndpoint, {method:'GET', cache:'no-cache'});
                const regArgsResponse = await regArgs.json();
                if (regArgsResponse.success === false) {
                    throw new Error(createArgs.msg || 'unknown error occured');
                }

                let regArgsResult = regArgsResponse.result;
                WebAuthn.recursiveBase64StrToArrayBuffer(regArgsResult);

                const credential = await navigator.credentials.create(regArgsResult);

                const authenticatorAttestationResponse = {
                    id: credential.id,
                    rawId: WebAuthn.arrayBufferToBase64(credential.rawId),
                    type: credential.type,
                    clientDataJSON: credential.response.clientDataJSON  ? WebAuthn.arrayBufferToBase64(credential.response.clientDataJSON) : null,
                    attestationObject: credential.response.attestationObject ? WebAuthn.arrayBufferToBase64(credential.response.attestationObject) : null
                };

                let storePasskey = await window.fetch(this.config.registerEndpoint, {
                    method  : 'POST',
                    body    : JSON.stringify(authenticatorAttestationResponse),
                    cache   : 'no-cache'
                });

                const authenticatorAttestationServerResponse = await storePasskey.json();

                if (authenticatorAttestationServerResponse.success) {
                    this.handleSuccess(authenticatorAttestationServerResponse.msg || 'Registration success', this.config.registerSuccess);
                } else {
                    throw new Error(authenticatorAttestationServerResponse.msg);
                }
            } catch (err) {
                this.handleError(err, this.config.registerFail);
            }
        }
    }

    /**
     *
     * @param {string} message
     * @param {function()} successFunc
     */
    handleSuccess(message, successFunc) {
        if (this.config.logSuccess) {
            console.log(message);
        }

        successFunc(message);
    }

    /**
     *
     * @param {string} error
     * @param {function()} errorFunc
     */
    handleError(error, errorFunc) {
        if (this.config.logErrors) {
            console.error(error);
        }

        errorFunc(error);
    }
}

export {WebAuthn};