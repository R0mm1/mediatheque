<template>
    <div class="form_element form_element_files">
        <label class="files_label">{{element.label}}</label>
        <div class="files_input">
            <template v-for="(fileName, fileId) in files">
                <div class="file_row">
                    <div class="name">{{fileName}}</div>
                    <input-button :element="{name: 'delete', class: 'fas fa-trash-alt'}"
                                  v-on:click.native="removeFile(fileId)"></input-button>
                </div>
            </template>
            <input-button :element="{name: 'add', value: 'Ajouter'}"
                          v-on:click.native="displayFileChooser" :disabled="maxFilesReached"></input-button>
            <input type="file" :name="element.name" v-on:change="addFile">
        </div>
    </div>
</template>

<script>
    import InputButton from "./_inputButton";
    import InputText from "./_inputText";
    import Xhr from './../../../js/tools/xhr';
    import Vue from 'vue';

    export default {
        name: "inputFiles",
        components: {InputButton, InputText},
        props: {element: {default: {}}, value: {default: {}}},
        data: function () {
            return {
                files: {}
            }
        },
        methods: {
            displayFileChooser: function () {
                if (!this.maxFilesReached) {
                    this.$el.querySelector('input[type="file"]').click();
                }
            },
            addFile: function (e) {
                //Add a new file
                var self = this;
                var name = e.target.files[0].name;
                var mime = e.target.files[0].type;

                let fileReader = new FileReader();
                fileReader.onload = function (e) {
                    var src = e.target.result;

                    Xhr.fetch('/api/general/uploadTempFile', {
                        method: 'POST',
                        body: JSON.stringify({
                            'src': src,
                            'mime': mime
                        })
                    }).then((response) => {
                        self.$emit('file-added', response.src);
                        self.loadFile(response.src, name);
                    }).catch((why) => console.error('Une erreur est survenue ', why));

                };


                fileReader.readAsDataURL(e.target.files[0]);
            },
            loadFile: function (src, name) {
                //load a new or existing file
                Vue.set(this.files, src, name);
            },
            removeFile: function (fileId) {
                Vue.delete(this.files, fileId);
                self.$emit('file-removed', fileId);
            },
            clear: function () {
                this.files = {};
            }
        },
        computed: {
            maxFilesReached: function () {
                if (typeof this.element.maxFiles == 'undefined') {
                    return false;
                }
                return Object.keys(this.files).length >= this.element.maxFiles;
            }
        }
    }
</script>

<style scoped lang="scss">
    @import "../../../css/form/element";

    .files_input {
        border: 1px solid #bbb;
        border-radius: 3px;
        padding: 0.4em 0.6em;
    }

    .file_row {
        display: flex;
        transition: all 0.3s;
        border-style: solid;
        border-width: 1px 0px 1px 0px;
        border-color: transparent;

        &:hover {
            border-color: #d0c3a9;
        }

        .name {
            flex: 1;
            display: flex;
            align-items: center;
        }
    }

    input[type="file"] {
        display: none;
    }

</style>