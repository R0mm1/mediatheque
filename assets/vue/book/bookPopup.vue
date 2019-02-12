<template>
    <div id="bookPopup">
        <div id="bookPopupHeader">
            <input-text ref="title" :element="{name:'title', placeholder:'Titre'}" :value="data.title"
                        v-on:input-text-content-changed="dataChanged"></input-text>
            <input-button :element="{name: 'close', class: 'fas fa-times'}"
                          v-on:click.native="$emit('popup-wanna-close')"></input-button>
        </div>
        <div id="bookPopupBody">
            <div id="bookPopupPicture">
                <input-picture ref="picture" :element="{name: 'picture'}"
                               v-on:picture-changed="pictureChanged"></input-picture>
            </div>

            <wysiwyg-editor ref="wysiwyg" v-on:content-changed="summaryChanged" :value="data.summary"></wysiwyg-editor>

            <div id="bookPopupGeneralData">
                <input-entities ref="authors" :element="{name: 'authors', label: 'Auteurs'}"
                                :searchUrl="'/api/author/search'"
                                :searchParam="'search'" :labelFields="{'Prénom': 'firstname', 'Nom': 'lastname'}"
                                :create-url="'/api/author'"
                                v-on:entity-added="authorAdded"
                                v-on:entity-removed="authorRemoved"></input-entities>
                <br>

                <input-text ref="year" :element="{name:'year', label:'Année'}" :value="data.year"
                            v-on:input-text-content-changed="dataChanged"></input-text>

                <input-text ref="pageCount" :element="{name:'pageCount', label:'Nombre de pages'}"
                            :value="data.pageCount"
                            v-on:input-text-content-changed="dataChanged"></input-text>

                <input-text ref="isbn" :element="{name:'isbn', label:'ISBN'}" :value="data.isbn"
                            v-on:input-text-content-changed="dataChanged"></input-text>


                <input-switch ref="switch" :element="{name:'isEBook', label: 'Livre électronique'}"
                              v-on:input-switch-state-changed="setTypeBook"></input-switch>

                <input-files ref="eBooks" :element="{name: 'eBooks', label: '', maxFiles: 1}"
                             :class="{displayed: isElectronic}"
                             v-on:file-added="eBookAdded" v-on:file-removed="eBookRemoved"></input-files>
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
    import InputEntities from "../form/elements/_inputEntities";
    import InputFiles from "../form/elements/_inputFiles";
    import Xhr from './../../js/tools/xhr';
    import Vue from 'vue';

    export default {
        name: "bookPopup",
        components: {InputEntities, InputPicture, InputButton, InputText, WysiwygEditor, InputSwitch, InputFiles},
        props: {
            'bookId': {},
            'defaultData': {
                'default': {
                    'isElectronic': 0,
                    'authors': {}
                }
            }
        },
        data: function () {
            return {
                hasChanged: false,
                isElectronic: false,
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
            setTypeBook: function (field, value) {
                this.isElectronic = value;
                this.hasChanged = true;
                this.data.isElectronic = value.toString();
            },
            summaryChanged: function (value) {
                if (!this.data['summary'] || this.data['summary'] != value) {
                    this.hasChanged = true;
                    this.data['summary'] = value;
                }
            },
            pictureChanged: function (src) {
                this.hasChanged = true;
                this.data['picture'] = src;
            },
            authorAdded: function (author) {
                if (!this.data['authors'][author.id]) {
                    this.hasChanged = true;
                    this.data['authors'][author.id] = author.id;
                }
            },
            authorRemoved: function (author) {
                this.hasChanged = true;
                delete this.data['authors'][author.id];
            },
            eBookAdded: function (ebookId) {
                this.hasChanged = true;
                this.data['ebook'] = ebookId;
            },
            eBookRemoved: function (ebookId) {
                this.hasChanged = true;
                this.data['ebook'] = null;
            },
            save: function () {
                if (this.hasChanged) {
                    var self = this;

                    let method = (!self.bookId || self.bookId.length == 0) ? 'POST' : 'PUT';
                    let url = '/api/book' + (method == 'PUT' ? '/' + self.bookId : '');

                    Xhr.fetch(url, {
                        'method': method,
                        'body': JSON.stringify(this.data)
                    })
                        .then(() => {
                                self.$emit('book-saved');
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
            'bookId': function (val, oldval) {

                this.clearAll();

                if (val == null || val.length == 0) {
                    return;
                }

                var self = this;

                Xhr.request({
                    url: '/api/book/' + val,
                    method: 'GET',
                    success: function (xhr) {
                        let data = JSON.parse(xhr.response);

                        ['year', 'title', 'pageCount', 'isbn', 'summary'].forEach(function (element) {
                            Vue.set(self.data, element, data[element]);
                        });

                        if (Array.isArray(data.authors)) {
                            data.authors.forEach(function (author) {
                                self.$refs.authors.addEntity(author.id, author);
                            });
                        }

                        self.$refs.picture.load(data.picture);

                        if (typeof data.ebook != 'undefined') {
                            this.isElectronic = true;
                            self.$refs.switch.initTo(true);
                            self.$refs.eBooks.loadFile(data.ebook.file, data.title);

                            self.eBookAdded(data.ebook.file);
                        }
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

        .form_element_files:not(.displayed) {
            display: none;
        }
    }
</style>