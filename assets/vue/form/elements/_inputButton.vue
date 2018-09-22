<template>
    <div class="form_element">
        <input :type="type" :name="element.name" :value="element.value"/>
    </div>
</template>

<script>
    export default {
        name: "inputButton",
        props: ['element'],
        computed: {
            type: function () {
                return this.element.isSubmit ? 'submit' : 'button';
            },
            value: function () {
                return this.element.value ? this.element.value : 'Enregistrer';
            }
        }
    }
</script>

<style scoped>

</style>