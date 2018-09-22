import config from './../../../config';

let token = '';

export default {
    'request': function () {
        if (token == '') {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function (event) {
                if (this.readyState === XMLHttpRequest.DONE) {
                    if (this.status === 200) {
                        self.listData = JSON.parse(xhr.response);
                    } else {
                        alert('Une erreur est survenue');
                    }
                }
            };

            xhr.open('GET', this.apiEndpoint + '?' + sortParam, true);
            xhr.send(sortParam);
        }
    },

    'login': function (username, password) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function (event) {
            try {
                var response = JSON.parse(this.responseText);
            } catch (e) {
                var response = {'error_description': 'Une errreur est survenue'};
            }

            if (this.readyState === XMLHttpRequest.DONE) {
                if (this.status === 200) {
                    console.log(reponse);
                } else if (this.status === 400) {
                    alert(response.error_description);
                }
            }
        };

        let formData = new FormData();
        formData.append('grant_type', 'password');
        formData.append('client_id', config.auth.client_id);
        formData.append('client_secret', config.auth.client_secret);
        formData.append('username', username);
        formData.append('password', password);

        xhr.open('POST', '/oauth/v2/token?' /*+ params*/, true);
        xhr.send(formData);
    }
}