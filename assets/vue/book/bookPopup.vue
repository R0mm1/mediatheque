<template>
    <div id="bookPopup">
        <div id="bookPopupHeader">
            <input-text ref="title" :element="{name:'title', placeholder:'Titre'}"
                        v-on:input-text-content-changed="dataChanged"></input-text>
            <input-button :element="{name: 'close', class: 'fas fa-times'}"
                          v-on:click.native="$emit('popup-wanna-close')"></input-button>
        </div>
        <div id="bookPopupBody">
            <div id="bookPopupPicture">
                <input-picture ref="picture" :element="{name: 'picture'}"
                               v-on:picture-changed="pictureChanged"></input-picture>
            </div>

            <wysiwyg-editor ref="wysiwyg" v-on:content-changed="summaryChanged"></wysiwyg-editor>

            <div id="bookPopupGeneralData">
                <input-entities ref="authors" :element="{name: 'authors', label: 'Auteurs'}"
                                :searchUrl="'/api/author/search'"
                                :searchParam="'search'" :labelFields="['firstname', 'lastname']"
                                v-on:entity-added="authorAdded"
                                v-on:entity-removed="authorRemoved"></input-entities>
                <br>
                <input-switch ref="switch" :element="{name:'isEBook', label: 'Livre électronique'}"
                              v-on:input-switch-state-changed="setTypeBook"></input-switch>
                <input-text ref="year" :element="{name:'year', label:'Année'}"
                            v-on:input-text-content-changed="dataChanged"></input-text>
                <input-text ref="pageCount" :element="{name:'pageCount', label:'Nombre de pages'}"
                            v-on:input-text-content-changed="dataChanged"></input-text>
                <input-text ref="isbn" :element="{name:'isbn', label:'ISBN'}"
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
    import WysiwygEditor from "../form/elements/_wysiwygEditor";
    import InputSwitch from "../form/elements/_inputSwitch";
    import InputPicture from "../form/elements/_inputPicture";
    import Xhr from './../../js/tools/xhr';
    import InputEntities from "../form/elements/_inputEntities";

    export default {
        name: "bookPopup",
        components: {InputEntities, InputPicture, InputButton, InputText, WysiwygEditor, InputSwitch},
        props: ['bookId'],
        data: function () {
            return {
                'hasChanged': false,
                'data': {
                    'isElectronic': 0,
                    'authors': []
                }
            };
        },
        methods: {
            dataChanged: function (field, value) {
                this.hasChanged = true;
                this.data[field] = value;
            },
            setTypeBook: function (field, value) {
                this.hasChanged = true;
                this.data.isElectronic = value.toString();
            },
            summaryChanged: function (value) {
                this.hasChanged = true;
                this.data['summary'] = value;
            },
            pictureChanged: function (src) {
                this.hasChanged = true;
                this.data['picture'] = src;
            },
            authorAdded: function (author) {
                this.data['authors'][author.id] = author.id;
            },
            authorRemoved: function (author) {
                delete this.data['authors'][author.id];
            },
            save: function () {
                if (this.hasChanged) {
                    var self = this;
                    Xhr.request({
                        url: '/api/book',
                        method: 'POST',
                        data: this.data,
                        success: function (xhr) {
                            self.$emit('popup-wanna-close', true)
                            self.clearAll();
                        },
                        error: function (xhr) {
                            alert('Une erreur est survenue');
                        }
                    });
                }
            },
            clearAll: function () {
                for (var refName in this.$refs) {
                    let ref = this.$refs[refName];
                    if (typeof ref.clear == 'function') {
                        ref.clear();
                    }
                }
            }
        }
    }
</script>

<style scoped lang="scss">
    #bookPopup {
        flex-direction: column;
        position: absolute;
        z-index: 3;
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

    #bookPopupBody {
        flex: 1;
        display: flex;
        overflow: auto;

        > div {
            flex: 1;
            padding: 10px;
        }

        #bookPopupPicture {
            max-width: 250px;
        }
    }

    #bookPopupFooter {
        height: 2.5rem;
        text-align: right;
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

    #bookPopupBody {
        .trix-container {
            display: flex;
            flex-direction: column;

            .trix-content {
                flex: 1;
            }
        }
    }

    #bookPopupFooter {
        .form_element_button {
            height: calc(100% - 7px);
        }
    }
</style>