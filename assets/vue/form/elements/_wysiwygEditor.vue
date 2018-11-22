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
        }
    }
</script>