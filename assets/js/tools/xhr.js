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

        let queryParams = '';
        if (typeof params.data == 'object' && !(params.data instanceof FormData)) {
            queryParams += '?';
            Object.keys(params.data).forEach(function (paramName) {
                queryParams += (queryParams.length > 1 ? '&' : '') + paramName + '=' + params.data[paramName];
            });
        }

        xhr.open(params.method ? params.method : 'GET', params.url + queryParams, (typeof params.async != "undefined" ? params.async : true));

        let token = localStorage.getItem('token');
        if (token && token.length > 0) {
            xhr.setRequestHeader('Authorization', 'Bearer ' + token);
        }

        let sendParam = (params.data && params.data instanceof FormData) ? params.data : null;
        xhr.send(sendParam);
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