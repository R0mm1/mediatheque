import Vue from 'vue';
import Form from './../../vue/form/form';
import Xhr from './../tools/xhr';

new Vue({
    el: "#appBody",
    template: '<Form :description="description"> </Form>',
    data: function () {
        return {
            description: {
                'action': this.setPassword,
                'containerId': 'formPassword',
                'title': 'Changer son mot de passe',
                'elements': [
                    {
                        'type': 'password',
                        'name': 'password',
                        'label': 'Nouveau mot de passe'
                    },
                    {
                        'type': 'password',
                        'name': 'confirmPassword',
                        'label': 'Confirmer le mot de passe'
                    },
                    {
                        'type': 'button',
                        'isSubmit': true,
                        'name': 'submit',
                        'value': 'Changer'
                    }
                ]
            }
        }
    },
    methods: {
        setPassword: function (formData) {
            Xhr.fetch('/api/user/password', {
                method: 'PUT',
                body: JSON.stringify({
                    password: formData.get("password"),
                    confirmation: formData.get("confirmPassword")
                })
            })
                .then(() => alert('Votre mot de passe a été modifié'))
                .catch(() => alert("Une erreur s'est produite et votre mot de passe n'a pas pu être modifié"));
        }
    },
    components: {Form}
});