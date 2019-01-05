<template>
    <div class="form_element form_element_text" :class="{no_auto_margin: !autoMargin}">
        <label v-if="typeof element.label != 'undefined'" :for="element.name">{{element.label}}</label>
        <input type="text" :name="element.name" v-model="elValue" :placeholder="element.placeholder"
               v-on:change="$emit('input-text-content-changed', element.name, elValue)"/>
    </div>
</template>

<script>
    export default {
        name: "inputText",
        props: ['element', 'value'],
        data: function () {
            return {
                'elValue': ''
            }
        },
        computed: {
            autoMargin: function () {
                return (typeof this.element.autoMargin != 'undefined') ? this.element.autoMargin : true;
            }
        },
        methods: {
            clear: function () {
                this.elValue = '';
            }
        },
        watch: {
            value: function (newValue) {
                this.elValue = newValue;
            }
        }
    }
</script>

<style scoped lang="scss">
    @import "../../../css/form/element";

    input {
        width: calc(100% - 6px);
        height: calc(100% - 6px);
        border: 1px solid #f8f3ea;
        padding: 2px;
    }
</style>