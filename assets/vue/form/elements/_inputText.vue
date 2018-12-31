<template>
    <div class="form_element form_element_text">
        <label v-if="typeof element.label != 'undefined'" :for="element.name">{{element.label}}</label>
        <input type="text" :name="element.name" v-model="value" :placeholder="element.placeholder"
               v-on:change="$emit('input-text-content-changed', element.name, value)"/>
    </div>
</template>

<script>
    export default {
        name: "inputText",
        props: ['element'],
        data: function () {
            return {
                'value': this.element.value
            }
        },
        methods: {
            clear: function () {
                this.value = '';
            }
        }
    }
</script>

<style scoped lang="scss">

    input {
        width: calc(100% - 6px);
        height: calc(100% - 6px);
        border: 1px solid #f8f3ea;
        padding: 2px;
    }
</style>