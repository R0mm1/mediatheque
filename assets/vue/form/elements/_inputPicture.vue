<template>
    <div class="form_element form_element_picture">
        <img class="picture_preview" v-on:click="displayFileChooser" :src="previewSrc"/>
        <div class="picture_buttons">
            <input-button :element="{class: 'fas fa-file-upload'}"
                          v-on:click.native="displayFileChooser"></input-button>
            <input-button :element="{class: 'fas fa-file-download'}"></input-button>
            <input-button :element="{class: 'fas fa-trash-alt'}"></input-button>
        </div>
        <input type="file" :name="element.name" v-on:change="reloadPreview">
    </div>
</template>

<script>
    import InputButton from './_inputButton';

    export default {
        name: "inputPicture",
        components: {InputButton},
        props: {element: {default: {}}},
        data: function () {
            return {
                previewSrc: ''
            }
        },
        methods: {
            displayFileChooser: function () {
                this.$el.querySelector('input[type="file"]').click();
            },
            reloadPreview: function (e) {
                var self = this;
                let fileReader = new FileReader();
                fileReader.onload = function (e) {
                    self.previewSrc = e.target.result;
                };
                fileReader.readAsDataURL(e.target.files[0]);


            }
        }
    }
</script>

<style scoped lang="scss">
    input[type="file"] {
        display: none;
    }

    .picture_preview {
        max-width: 250px;
    }
</style>