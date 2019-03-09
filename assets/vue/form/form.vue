<template>
    <div :id="containerId">
        <div class="formTitle" :class="{displayed: hasFormTitle}">{{description.title}}</div>
        <form :action="action" v-on:submit="submit">
            <template v-for="element in description.elements">
                <InputText class="form_element_text_container" v-if="element.type == 'text'"
                           :element="element"></InputText>
                <InputPassword v-if="element.type == 'password'" :element="element"></InputPassword>
                <InputButton v-if="element.type == 'button'" :element="element"></InputButton>
            </template>
        </form>
    </div>
</template>

<script>
    import InputText from './elements/_inputText';
    import InputButton from './elements/_inputButton';
    import InputPassword from './elements/_inputPassword';

    export default {
        name: "form",
        components: {InputButton, InputText, InputPassword},
        props: ['description'],
        computed: {
            action: function () {
                return (typeof this.description.action == 'string') ? this.description.action : '';
            },
            containerId: function () {
                return this.description.containerId ? this.description.containerId : '';
            },
            hasFormTitle: function () {
                return this.description.title && this.description.title.length > 0;
            }
        },
        methods: {
            submit: function (e) {
                let formData = new FormData(this.$el.querySelector('form'));
                if (typeof this.description.action == 'function') {
                    this.description.action(formData);
                }
                e.preventDefault();
            }
        }
    }
</script>

<style scoped lang="scss">
    .formTitle {
        background-color: #e4dccc;
        padding: 10px;
    }

    form {
        padding: 5px;
        border-left: 3px solid #e4dccc;

        .form_element_text_container {
            margin-bottom: 10px;
        }
    }
</style>