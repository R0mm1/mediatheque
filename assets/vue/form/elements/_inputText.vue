<template>
    <div class="form_element form_element_text">
        <label v-if="typeof element.label != 'undefined'" :for="element.name">{{element.label}}</label>
        <input type="text" :name="element.name" v-model="element.value" :placeholder="element.placeholder"
               v-on:change="$emit('input-text-content-changed', element.name, element.value)"/>
    </div>
</template>

<script>
    export default {
        name: "inputText",
        props: ['element'],
        data: function () {
            return {
                content: ''
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