<template>
    <div class="form_element form_element_picture">
        <div class="picture_preview" v-on:click="displayFileChooser" :style="'background-image: url('+src+')'">
            <div class="preview_default" v-if="displayDefault"><i class="far fa-image"></i></div>
        </div>
        <div class="picture_buttons">
            <input-button :element="{class: 'fas fa-file-upload'}"
                          v-on:click.native="displayFileChooser"></input-button>
            <input-button :element="{class: 'fas fa-file-download'}" v-if="false"></input-button> <!--todo: à faire -->
            <input-button :element="{class: 'fas fa-trash-alt'}" v-on:click.native="clear"></input-button>
        </div>
        <input type="file" :name="element.name" v-on:change="reloadPreview">
    </div>
</template>

<script>
    import InputButton from './_inputButton';
    import Xhr from './../../../js/tools/xhr';

    export default {
        name: "inputPicture",
        components: {InputButton},
        props: {element: {default: {}}, value: {default: {}}},
        data: function () {
            return {
                src: '',
                mime: '',
                tempUrl: '',
                displayDefault: true
            }
        },
        methods: {
            displayFileChooser: function () {
                this.$el.querySelector('input[type="file"]').click();
            },
            reloadPreview: function (e) {
                var self = this;

                self.mime = e.target.files[0].type;

                let fileReader = new FileReader();
                fileReader.onload = function (e) {
                    self.src = e.target.result;

                    Xhr.request({
                        url: '/api/general/uploadTempFile',
                        method: 'POST',
                        data: {
                            'src': self.src,
                            'mime': self.mime
                        },
                        success: function (xhr) {
                            let data = JSON.parse(xhr.response);
                            if (data.src) {
                                self.tempUrl = data.src;
                                self.displayDefault = false;
                            }
                            self.$emit('picture-changed', self.tempUrl);
                        },
                        error: function (xhr) {
                            alert('Une erreur est survenue');
                        }
                    });

                };


                fileReader.readAsDataURL(e.target.files[0]);
            },
            clear: function () {
                this.src = '';
                this.mime = '';
                this.tempUrl = '';
                this.displayDefault = true;
            },
            load: function (url) {
                this.src = (typeof url != 'string') ? '' : url;
                this.displayDefault = (this.src.length == 0);
            }
        }
    }
</script>

<style scoped lang="scss">
    @import "../../../css/form/element";

    input[type="file"] {
        display: none;
    }

    .picture_preview {
        display: flex;
        width: 160px;
        height: 251px;
        margin: auto;
        background-repeat: no-repeat;
        background-size: contain;
        background-position: center;

        .preview_default {
            margin: auto;
            font-size: 5rem;
            color: #4d4d4d;
        }
    }

    .picture_buttons {
        text-align: center;
    }
</style>