<template>
    <div id="bookPopup">
        <div id="bookPopupHeader">
            <input-text :element="{name:'title', placeholder:'Titre'}"
                        v-on:input-text-content-changed="dataChanged"></input-text>
            <input-button :element="{name: 'close', class: 'fas fa-times'}"
                          v-on:click.native="$emit('popup-wanna-close')"></input-button>
        </div>
        <div id="bookPopupGeneralData">
            <input-switch :element="{name:'isEBook', label: 'Livre électronique'}"
                          v-on:input-switch-state-changed="setTypeBook"></input-switch>
            <input-text :element="{name:'year', label:'Année'}"
                        v-on:input-text-content-changed="dataChanged"></input-text>
            <input-text :element="{name:'pageCount', label:'Nombre de pages'}"
                        v-on:input-text-content-changed="dataChanged"></input-text>
            <input-text :element="{name:'isbn', label:'ISBN'}"
                        v-on:input-text-content-changed="dataChanged"></input-text>
        </div>
        <div id="bookPopupFooter">
            <input-button v-if="hasChanged" :element="{name: 'close', value: 'Sauvegarder'}"
                          v-on:click.native="save"></input-button>
        </div>
    </div>
</template>

<script>
    import InputText from "../form/elements/_inputText";
    import InputButton from "../form/elements/_inputButton";
    import InputSwitch from "../form/elements/_inputSwitch";
    import Xhr from './../../js/tools/xhr';

    export default {
        name: "bookPopup",
        components: {InputButton, InputText, InputSwitch},
        props: ['bookId'],
        data: function () {
            return {
                'hasChanged': false,
                'data': {}
            };
        },
        methods: {
            dataChanged: function (field, value) {
                this.hasChanged = true;
                this.data[field] = value;
            },
            setTypeBook: function (field, value) {

            },
            save: function () {
                Xhr.request({
                    url: '/api/book',
                    method: 'POST',
                    data: this.data,
                    success: function (xhr) {
                        alert('Les données ont été enregistrées');
                    },
                    error: function (xhr) {
                        alert('Une erreur est survenue');
                    }
                });
            }
        }
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