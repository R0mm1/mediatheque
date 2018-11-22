<template>
    <VueTrix v-model="hello"/>
</template>

<script>
    import VueTrix from 'vue-trix';

    export default {
        name: "wysiwygEditor",
        components: {VueTrix},
        data: function () {
            return {
                hello: 'world'
            }
        }
    }
</script>