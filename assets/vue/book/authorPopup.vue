<template>
    <div id="bookPopup">
        <div id="bookPopupHeader">
            <input-button :element="{name: 'close', class: 'fas fa-times'}"
                          v-on:click.native="$emit('popup-wanna-close')"></input-button>
        </div>
        <div id="bookPopupBody">
            <div id="authorPopupGeneralData">
                <input-text ref="firstname" :element="{name:'firstname', label:'Prénom'}"
                            :value="data.firstname"
                            v-on:input-text-content-changed="dataChanged"></input-text>
                <input-text ref="lastname" :element="{name:'lastname', label:'Nom'}"
                            :value="data.lastname"
                            v-on:input-text-content-changed="dataChanged"></input-text>
            </div>
        </div>
        <div id="bookPopupFooter">
            <input-button :element="{name: 'close', value: 'Sauvegarder'}"
                          v-on:click.native="save"></input-button>
        </div>
    </div>
</template>

<script>
    import InputText from "../form/elements/_inputText";
    import InputButton from "../form/elements/_inputButton";
    import Xhr from './../../js/tools/xhr';
    import Vue from 'vue';

    export default {
        name: "authorPopup",
        components: {InputButton, InputText},
        props: {
            'authorId': {},
            'defaultData': {
                'default': {}
            }
        },
        data: function () {
            return {
                hasChanged: false,
                data: JSON.parse(JSON.stringify(this.defaultData))
            };
        },
        methods: {
            dataChanged: function (field, value) {
                if (!this.data[field] || this.data[field] !== value) {
                    this.hasChanged = true;
                    this.data[field] = value;
                }
            },
            save: function () {
                if (this.hasChanged) {
                    var self = this;

                    let method = (!self.authorId || self.authorId.length === 0) ? 'POST' : 'PUT';
                    let url = '/api/author' + (method === 'PUT' ? '/' + self.authorId : '');

                    Xhr.fetch(url, {
                        'method': method,
                        'body': JSON.stringify(this.data)
                    })
                        .then(() => {
                                self.$emit('author-saved');
                                self.clearAll();
                            }
                        )
                        .catch(() => alert('Une erreur est survenue'));
                }
            },
            clearAll: function () {
                for (var refName in this.$refs) {
                    let ref = this.$refs[refName];
                    if (typeof ref.clear == 'function') {
                        ref.clear();
                    }
                }
                this.data = JSON.parse(JSON.stringify(this.defaultData));
                this.hasChanged = false;
            }
        },
        watch: {
            authorId: function (val, oldval) {

                this.clearAll();

                if (val == null || val.length === 0) {
                    return;
                }

                let self = this;

                Xhr.fetch('/api/author/' + val, {
                    method: 'GET'
                })
                    .then(response => {
                        ['firstname', 'lastname'].forEach(function (element) {
                            Vue.set(self.data, element, response[element]);
                        });
                    })
                    .catch(error => {
                        console.error(error);
                        alert('Une erreur est survenue');
                    });
            }
        }
    }
</script>

<style scoped lang="scss">
    @import "./../../css/popup";
</style>

<style lang="scss">
    #bookPopupHeader {
        justify-content: end;

        .form_element_button {
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