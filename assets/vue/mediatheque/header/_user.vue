<template>
    <div id="headerUserElement">
        <input-button id="userAccount" :element="{class: 'fas fa-user-circle'}"
                      v-on:click.native="goToAccount"></input-button>
    </div>
</template>

<script>
    import InputButton from "../../form/elements/_inputButton";

    export default {
        name: "user",
        components: {InputButton},
        methods: {
            goToAccount: function () {
                window.location = '/account'
            }
        }
    }
</script>

<style scoped lang="scss">
    #headerUserElement {
        padding: 10px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;

        #userAccount {
            border-radius: 50%;
            height: 30px;
            width: 30px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
    }
</style>

<style lang="scss">
    #headerUserElement #userAccount label {
        font-size: 25px;
        color: #2b2b2b;
    }
</style>