<template>
    <trix-editor v-on:trix-change="contentChanged" ref="trix"></trix-editor>
</template>

<script>
    import Trix from 'trix';

    export default {
        name: "wysiwygEditor",
        components: {Trix},
        data: function () {
            return {
                content: ''
            }
        },
        methods: {
            clear: function () {
                this.$refs.trix.value = '';
            },
            contentChanged: function (e) {
                this.$emit('content-changed', e.target.innerHTML);
            }
        },

    }
</script>

<style scoped lang="scss">
    @import "~trix/dist/trix.css";
</style>