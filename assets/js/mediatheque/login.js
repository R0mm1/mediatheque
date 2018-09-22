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
                'elements': [
                    {
                        'type': 'text',
                        'name': 'username',
                        'placeholder': 'Nom d\'utilisateur'
                    },
                    {
                        'type': 'text',
                        'name': 'password',
                        'placeholder': 'Mot de passe'
                    },
                    {
                        'type': 'button',
                        'isSubmit': true,
                        'name': 'submit',
                        'value': 'Se connecter'
                    }
                ]
            }
        }
    },
    methods: {
        callLogin: function (formData) {
            console.log(formData.get('username'));
            console.log(formData.get('password'));

            Xhr.login(formData.get('username'), formData.get('password'));
        }
    },
    components: {Form}
});