const Xhr = {
    config: false,
    loadConf() {
        if (this.config === false) {
            return fetch('/js/config.json', {
                method: 'GET'
            })
                .then(response => response.json())
                .then(response => {
                    this.config = response;
                    return Promise.resolve();
                })
                .catch(error => {
                    console.error(error);
                    alert('Impossible de charger la configuration')
                });
        } else {
            return Promise.resolve();
        }
    },

    request(params) {
        let method = params.method ? params.method : 'GET';

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function (event) {
            if (this.readyState === XMLHttpRequest.DONE) {
                if (this.status === 200) {
                    if (params.success && typeof params.success == 'function') {
                        params.success(this);
                    }
                } else {
                    if (params.error && typeof params.success == 'function') {
                        params.error(this);
                    }
                }
            }
        };

        if (typeof params.data == 'object' && !(params.data instanceof FormData) && (method == 'GET' || method == 'POST')) {
            let formData = new FormData();
            Object.keys(params.data).forEach(function (paramName) {
                if (Array.isArray(params.data[paramName])) {
                    params.data[paramName].forEach(function (value) {
                        formData.append(paramName + '[]', value);
                    });
                } else {
                    formData.append(paramName, params.data[paramName]);
                }
            });
            params.data = formData;
        }

        if (method == 'PUT') {
            params.data = (params.data) ? JSON.stringify(params.data) : '';
        }

        if (typeof params.data === 'undefined') {
            params.data = new FormData();
        }

        if (method === 'GET') {
            params.url += '?';
            params.data.forEach(function (value, key) {
                params.url += '&' + encodeURIComponent(key) + '=' + encodeURIComponent(value);
            });
        }

        xhr.open(method, params.url, (typeof params.async != "undefined" ? params.async : true));

        let token = localStorage.getItem('access_token');
        if (token && token.length > 0) {
            xhr.setRequestHeader('Authorization', 'Bearer ' + token);
        }

        xhr.send(params.data);
    },

    verifyToken() {
        let createdAt = parseInt(localStorage.getItem('created_at'));
        let expiresIn = parseInt(localStorage.getItem('expires_in'));
        let now = Math.floor(Date.now() / 1000);

        if (typeof createdAt !== 'number' || (createdAt + expiresIn - 60) < now) {
            return this.loadConf()
                .then(() => {

                    let formData = new FormData();
                    formData.append('grant_type', 'refresh_token');
                    formData.append('client_id', this.config.auth.client_id);
                    formData.append('client_secret', this.config.auth.client_secret);
                    formData.append('refresh_token', localStorage.getItem('refresh_token'));

                    let headers = new Headers();
                    headers.append('Authorization', 'Bearer ' + localStorage.getItem('access_token'));

                    return fetch('/oauth/v2/token', {
                        method: 'POST',
                        body: formData,
                        headers: headers
                    })
                })
                .then(response => this.handleFetchResponse(response))
                .then(response => response.json())
                .then(response => {
                    this.storeTokenData(response.access_token, response.refresh_token, response.expires_in);
                    return Promise.resolve();
                });
        } else {
            return Promise.resolve();
        }
    },

    fetch(url, params) {
        if (typeof params.headers == 'undefined') params.headers = new Headers();
        params.headers.append('Authorization', 'Bearer ' + localStorage.getItem('access_token'));

        return this.verifyToken()
            .then(() => {
                return fetch(url, params);
            })
            .then(response => {
                return this.handleFetchResponse(response);
            })
            .then(response => {
                if (response.headers.get('Content-Type') === 'application/json') {
                    return response.json();
                } else {
                    return Promise.resolve(response);
                }
            });
    },

    login(username, password) {
        this.loadConf()
            .then(() => {
                return Promise.resolve();
            })
            .then(() => {
                let formData = new FormData();
                formData.append('grant_type', 'password');
                formData.append('client_id', this.config.auth.client_id);
                formData.append('client_secret', this.config.auth.client_secret);
                formData.append('username', username);
                formData.append('password', password);

                fetch('/oauth/v2/token', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => this.handleFetchResponse(response))
                    .then(response => response.json())
                    .then(response => {
                        this.storeTokenData(response.access_token, response.refresh_token, response.expires_in);
                        window.location = this.config.default.page;
                    })
                    .catch(error => {
                        console.error(error);
                        alert('Une erreur est survenue');
                    })
            })
            .catch(error => {
                console.error(error);
                alert('Une erreur est survenue');
            });
    },

    handleFetchResponse(response) {
        if (response.status >= 200 && response.status < 300) {
            return Promise.resolve(response);
        } else {
            return Promise.reject(new Error(response.statusText));
        }
    },

    storeTokenData(accesToken, refreshToken, expiresIn) {
        localStorage.setItem('access_token', accesToken);
        localStorage.setItem('refresh_token', refreshToken);
        localStorage.setItem('expires_in', expiresIn);
        localStorage.setItem('created_at', Math.floor(Date.now() / 1000));
    },

    buildGetUrl(url, params) {
        url += '?';
        let isFirst = true;
        Object.keys(params).forEach((paramName) => {
            if (!isFirst) {
                url += '&';
                isFirst = false;
            }
            url += paramName + '=' + params[paramName];
        });
        return url;
    }
};

export default Xhr;