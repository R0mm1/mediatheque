<template>
    <div>
        <trix-editor v-on:trix-change="contentChanged" ref="trix"></trix-editor>
    </div>
</template>

<script>
    import Trix from 'trix';

    export default {
        name: "wysiwygEditor",
        components: {Trix},
        props: ['value'],
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
        watch: {
            value: function (newVal) {
                this.$refs.trix.value = newVal;
            }
        }
    }
</script>

<style scoped lang="scss">
    @import "~trix/dist/trix.css";
</style>