<template>
    <span>
        <form :action="action" v-on:submit="submit">
            <template v-for="element in description.elements">
                <InputText v-if="element.type == 'text'" :element="element"></InputText>
                <InputButton v-if="element.type == 'button'" :element="element"></InputButton>
            </template>
        </form>
    </span>
</template>

<script>
    import InputText from './elements/_inputText';
    import InputButton from './elements/_inputButton';

    export default {
        name: "form",
        components: {InputButton, InputText},
        props: ['description'],
        computed: {
            action: function () {
                return (typeof this.description.action == 'string') ? this.description.action : '';
            }
        },
        methods: {
            submit: function (e) {
                var formData = new FormData(this.$el.querySelector('form'));
                if (typeof this.description.action == 'function') {
                    this.description.action(formData);
                }
                e.preventDefault();
            }
        }
    }
</script>

<style scoped>

</style>