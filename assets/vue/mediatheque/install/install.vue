<template>
    <div id="installer">
        <div id="installer-header">
            Installation de la Médiathèque
        </div>

        <div v-if="step === 0" class="installer-body">
            Bienvenue sur l'assistant d'installation de la Médiathèque.<br>
            Vous êtes prêt pour commencer? Cliquez sur suivant!<br>
            <input-button class="navButton" v-on:click.native="goStep1" :element="{value: 'Suivant'}"></input-button>
        </div>


        <div v-if="step === 1" class="installer-body">
            Nous allons créer le premier utilisateur de la Médiathèque. Cet utilisateur sera administrateur et vous
            permettra de créer d'autres utilisateurs ensuite pour partager vos livres et en découvrir plein
            d'autres!<br>

            <input-text v-on:input-text-content-changed="userDataChanged"
                        :element="{label: 'Login', name: 'username'}"></input-text>
            <input-password v-on:input-password-changed="userDataChanged"
                            :element="{label: 'Mot de passe', name: 'password'}"></input-password>
            <input-button class="navButton" v-on:click.native="goStep2" :element="{value: 'Suivant'}"></input-button>
        </div>

        <div v-if="step === 2" class="installer-body">
            Nous terminons de configurer certains points, veuillez ne pas fermer cette fenêtre.
        </div>

        <div v-if="step === 3" class="installer-body">
            Et voila! Prêt à découvrir la Médiathèque? Il ne vous reste plus qu'à vous connecter et à profiter!<br>
            <input-button class="navButton" v-on:click.native="goToLogin"
                          :element="{value: 'Me connecter'}"></input-button>
        </div>
    </div>
</template>

<script>
    import InputButton from "../../form/elements/_inputButton";
    import Xhr from "../../../js/tools/xhr";
    import InputText from "../../form/elements/_inputText";
    import Vue from 'vue';
    import InputPassword from "../../form/elements/_inputPassword";

    export default {
        name: "install",
        components: {InputPassword, InputText, InputButton},
        data: function () {
            return {
                step: 0,
                step1: {}
            }
        },
        methods: {
            goStep1: function () {
                Xhr.fetch('/api/install', {
                    method: 'GET'
                })
                    .then(() => {
                        this.step = 1;
                    });
            },

            goStep2: function () {
                let isFormFilled = (typeof this.step1.username != 'undefined' && typeof this.step1.password != 'undefined');
                if (this.step === 0 || !isFormFilled) {
                    //If step 1 is not complete or the user didn't filled the form yet, we wait.
                    //This method will be recalled automatically at the end of the step 1, or by the user at the form validation

                    if (isFormFilled) {
                        this.step = 2;
                    }

                    return;
                }

                Xhr.fetch('/api/install/user', {
                    method: 'POST',
                    body: JSON.stringify(this.step1)
                })
                    .then(() => {
                        this.step = 3
                    })
            },

            userDataChanged: function (data, value) {
                Vue.set(this.step1, data, value);
            },

            goToLogin: function () {
                window.location = '/login';
            }
        }
    }
</script>

<style scoped lang="scss">
    #installer {
        width: 750px;
        box-shadow: 0px 0px 10px #bbb;
        margin: auto;
        height: 500px;
    }

    #installer-header {
        background-color: #e4dccc;
        padding: 10px;
        font-size: 1.2rem;
    }

    .installer-body {
        padding: 10px;

        .navButton {
            float: right;
        }
    }
</style>