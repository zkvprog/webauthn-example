async function registerUser(event) {
    try {
        let form = event.target.closest('form');
        let errorPanel = form.querySelector('.form-error')

        if (form.checkValidity()) {
            let register = await window.fetch('reg_user.php', {
                method  : 'POST',
                body    : new FormData(form),
                cache   : 'no-cache'
            });
            register = await register.json();

            if (register.success) {
                window.location.reload();
            } else {
                throw new Error(register.msg);
            }
        } else {
            errorPanel.textContent = 'Check your form';
        }
    } catch(err) {
        console.error(err);
    }
}

async function authUser(event) {
    try {
        const form = event.target.closest('form');
        const errorPanel = form.querySelector('.form-error')

        if (form.checkValidity()) {
            const auth = await window.fetch('auth_user.php', {
                method  : 'POST',
                body    : new FormData(form),
                cache   : 'no-cache'
            });
            const authResponse = await auth.json();

            if (authResponse.success) {
                window.location.reload();
            } else {
                throw new Error(authResponse.msg);
            }
        } else {
            errorPanel.textContent = 'Check your form';
        }
    } catch(err) {
        console.error(err);
    }
}

async function logout(event) {
    try {
        let logout = await window.fetch('logout.php', {
            method  : 'GET',
            cache   : 'no-cache'
        });
        logout = await logout.json();
        if (logout.success) {
            window.location.reload();
        } else {
            throw new Error(logout.msg);
        }
    } catch(err) {
        console.error(err);
    }
}
/*

async function verifyPasskey() {
    try {
        if (!window.fetch || !navigator.credentials || !navigator.credentials.create) {
            throw new Error('Browser not supported.');
        }

        let verifyArgs = await window.fetch('get_args.php?cmd=getArgs', {
            method: 'Post',
            cache:'no-cache',
        });

        verifyArgs = await verifyArgs.json();
        if (verifyArgs.success === false) {
            throw new Error(createArgs.msg || 'unknown error occured');
        }

        recursiveBase64StrToArrayBuffer(verifyArgs);

        const credential = await navigator.credentials.get(verifyArgs);

        const authenticatorAttestationResponse = {
            id: credential.rawId ? arrayBufferToBase64(credential.rawId) : null,
            clientDataJSON: credential.response.clientDataJSON  ? arrayBufferToBase64(credential.response.clientDataJSON) : null,
            authenticatorData: credential.response.authenticatorData ? arrayBufferToBase64(credential.response.authenticatorData) : null,
            signature: credential.response.signature ? arrayBufferToBase64(credential.response.signature) : null,
            userHandle: credential.response.userHandle ? arrayBufferToBase64(credential.response.userHandle) : null
        };

        let response = await window.fetch('get_passkey.php', {
            method:'POST',
            body: JSON.stringify(authenticatorAttestationResponse),
            cache:'no-cache'
        });
        const authenticatorAttestationServerResponse = await response.json();

        if (authenticatorAttestationServerResponse.success) {
            console.log(authenticatorAttestationServerResponse.msg || 'login success');
            window.location.reload();
        } else {
            throw new Error(authenticatorAttestationServerResponse.msg);
        }
    } catch (err) {
        console.error(err);
    }
}

async function registerPasskey() {
    try {
        if (!window.fetch || !navigator.credentials || !navigator.credentials.create) {
            throw new Error('Browser not supported.');
        }
        let regArgs = await window.fetch('get_args.php?cmd=regArgs', {method:'GET', cache:'no-cache'});
        const createArgs = await regArgs.json();
        if (createArgs.success === false) {
            throw new Error(createArgs.msg || 'unknown error occured');
        }

        recursiveBase64StrToArrayBuffer(createArgs);

        const credential = await navigator.credentials.create(createArgs);

        const authenticatorAttestationResponse = {
            id: credential.id,
            rawId: arrayBufferToBase64(credential.rawId),
            type: credential.type,
            clientDataJSON: credential.response.clientDataJSON  ? arrayBufferToBase64(credential.response.clientDataJSON) : null,
            attestationObject: credential.response.attestationObject ? arrayBufferToBase64(credential.response.attestationObject) : null
        };

        let storePasskey = await window.fetch('store_passkey.php', {
            method  : 'POST',
            body    : JSON.stringify(authenticatorAttestationResponse),
            cache   : 'no-cache'
        });

        const authenticatorAttestationServerResponse = await storePasskey.json();

        if (authenticatorAttestationServerResponse.success) {
            console.log(authenticatorAttestationServerResponse.msg || 'Registration success');
        } else {
            throw new Error(authenticatorAttestationServerResponse.msg);
        }
    } catch (err) {
        console.error(err);
    }
}

async function registerInternalPasskey() {
    try {
        if (!window.fetch || !navigator.credentials || !navigator.credentials.create) {
            throw new Error('Browser not supported.');
        }

        if (window.PublicKeyCredential &&
            PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable &&
            PublicKeyCredential.isConditionalMediationAvailable) {
                let regArgs = await window.fetch('get_args.php?cmd=regArgs', {method:'GET', cache:'no-cache'});
                const createArgs = await regArgs.json();
                if (createArgs.success === false) {
                    throw new Error(createArgs.msg || 'unknown error occured');
                }

                recursiveBase64StrToArrayBuffer(createArgs);

                const credential = await navigator.credentials.create(createArgs);

                // create object
                const authenticatorAttestationResponse = {
                    clientDataJSON: credential.response.clientDataJSON  ? arrayBufferToBase64(credential.response.clientDataJSON) : null,
                    attestationObject: credential.response.attestationObject ? arrayBufferToBase64(credential.response.attestationObject) : null
                };

                // register on server side
                let storePasskey = await window.fetch('store_passkey.php', {
                    method  : 'POST',
                    body    : JSON.stringify(authenticatorAttestationResponse),
                    cache   : 'no-cache'
                });

                const authenticatorAttestationServerResponse = await storePasskey.json();

                if (authenticatorAttestationServerResponse.success) {
                    console.log(authenticatorAttestationServerResponse.msg || 'Registration success');
                } else {
                    throw new Error(authenticatorAttestationServerResponse.msg);
                }

                /!*const publicKeyCredentialCreationOptions = {
                    challenge: new Uint8Array(32),
                    rp: {
                        name: "Localhost",
                        id: "localhost",
                    },
                    user: {
                        id: Uint8Array.from("UZSL85T9AFC", c => c.charCodeAt(0)),
                        name: "username",
                        displayName: "",
                    },
                    pubKeyCredParams: [{alg: -7, type: "public-key"}, {alg: -257, type: "public-key"}],
                    authenticatorSelection: {
                        //authenticatorAttachment: "cross-platform",
                        authenticatorAttachment: "platform",
                        requireResidentKey: true,
                    },
                    timeout: 30000
                };

                const credential = await navigator.credentials.create({
                    publicKey: publicKeyCredentialCreationOptions
                });*!/
        } else {
            throw new Error('Platform not available');
        }
    } catch (err) {
        console.error(err);
    }
}

/!**
 * convert RFC 1342-like base64 strings to array buffer
 * @param {mixed} obj
 * @returns {undefined}
 *!/
function recursiveBase64StrToArrayBuffer(obj) {
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
                recursiveBase64StrToArrayBuffer(obj[key]);
            }
        }
    }
}

/!**
 * Convert a ArrayBuffer to Base64
 * @param {ArrayBuffer} buffer
 * @returns {String}
 *!/
function arrayBufferToBase64(buffer) {
    let binary = '';
    let bytes = new Uint8Array(buffer);
    let len = bytes.byteLength;
    for (let i = 0; i < len; i++) {
        binary += String.fromCharCode( bytes[ i ] );
    }
    return window.btoa(binary);
}

function detectSupport() {
    if (window.PublicKeyCredential) {
        PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable()
            .then((available) => {
                if (available) {
                    console.log("Supported.");
                } else {
                    console.log(
                        "WebAuthn supported, Platform Authenticator *not* supported."
                    );
                }
            })
            .catch((err) => console.log("Something went wrong."));
    } else {
        console.log("Not supported.");
    }
}*/
