import Vue from 'vue';
import Form from './../../vue/form/form';
import Xhr from './../tools/xhr';

new Vue({
    el: "#appBody",
    template: '<Form :description="description"> </Form>',
    data: function () {
        return {
            description: {
                'action': this.callLogin,
                'containerId': 'formLogin',
                'title': 'Se connecter',
                'elements': [
                    {
                        'type': 'text',
                        'name': 'username',
                        'placeholder': 'Nom d\'utilisateur'
                    },
                    {
                        'type': 'password',
                        'name': 'password',
                        'placeholder': 'Mot de passe'
                    },
                    {
                        'type': 'button',
                        'isSubmit': true,
                        'name': 'submit',
                        'value': 'Connexion'
                    }
                ]
            }
        }
    },
    methods: {
        callLogin: function (formData) {
            Xhr.login(formData.get('username'), formData.get('password'));
        }
    },
    components: {Form}
});