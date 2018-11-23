<template>
    <VueTrix v-model="content"/>
</template>

<script>
    import VueTrix from 'vue-trix';

    export default {
        name: "wysiwygEditor",
        components: {VueTrix},
        data: function () {
            return {
                content: ''
            }
        },
        watch: {
            content: function (val, oldVal) {
                this.$emit('content-changed', val);
            }
        }
    }
</script>