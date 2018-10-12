<template>
    <div id="bookPopup">
        <div id="bookPopupHeader">
            <input-text :element="{name:'title', placeholder:'Titre'}"></input-text>
            <input-button :element="{name: 'close', class: 'fas fa-times'}" v-on:click.native="$emit('popup-wanna-close')"></input-button>
        </div>
    </div>
</template>

<script>
    import InputText from "../form/elements/_inputText";
    import InputButton from "../form/elements/_inputButton";

    export default {
        name: "bookPopup",
        components: {InputButton, InputText},
        props: ['bookId'],
    }
</script>

<style scoped lang="scss">
    #bookPopup {
        position: absolute;
        z-index: 2;
        width: calc(100% - 20px);
        height: calc(100% - 20px);
        margin: 10px;
        box-shadow: 1px 1px 5px black;
        background-color: #f8f5ef;
    }

    #bookPopupHeader {
        height: 4rem;
        display: flex;
    }
</style>

<style lang="scss">

    #bookPopupHeader {
        .form_element_text {
            flex: 1;
            height: 100%;
            margin: auto;

            input[type="text"] {
                font-size: 2.5rem !important;
            }
        }
        .form_element_button {
            height: 100%;
            width: 4rem;
            text-align: center;

            &::after {
                bottom: 0 !important;
            }

            label {
                font-size: 2rem !important;
                line-height: 3rem;
            }
        }
    }
</style>