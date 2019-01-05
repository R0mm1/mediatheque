export default {
    'config': false,
    'loadConf': function () {
        if (this.config === false) {
            var self = this;
            this.request({
                url: '/js/config.json',
                async: false,
                success: function (e) {
                    self.config = JSON.parse(e.responseText);
                },
                error: function () {
                    alert('Impossible de charger la configuration');
                }
            });
        }
    },

    'request': function (params) {
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

        let token = localStorage.getItem('token');
        if (token && token.length > 0) {
            xhr.setRequestHeader('Authorization', 'Bearer ' + token);
        }

        xhr.send(params.data);
    },

    'login': function (username, password) {
        var self = this;
        this.loadConf();

        let formData = new FormData();
        formData.append('grant_type', 'password');
        formData.append('client_id', this.config.auth.client_id);
        formData.append('client_secret', this.config.auth.client_secret);
        formData.append('username', username);
        formData.append('password', password);

        this.request({
            method: 'POST',
            url: '/oauth/v2/token',
            data: formData,
            success: function (event) {
                let response = JSON.parse(event.responseText);
                localStorage.setItem('token', response.access_token);
                window.location = self.config.default.page;
            },
            error: function (event) {
                if (event.status === 400) {
                    alert(JSON.parse(event.responseText).error_description);
                } else {
                    alert('Une erreur est survenue');
                }
            }
        });
    }
}