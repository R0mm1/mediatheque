<template>
    <div class="form_element">
        <label v-if="typeof element.label != 'undefined'" :for="element.name">{{element.label}}</label>
        <input type="password" :name="element.name" :placeholder="element.placeholder" v-model="password"
               v-on:change="$emit('input-password-changed', element.name, password)"/>
    </div>
</template>

<script>
    export default {
        name: "inputPassword",
        props: ['element'],
        data: function () {
            return {
                password: ''
            }
        }
    }
</script>

<style scoped lang="scss">
    @import "../../../css/form/element";

    input {
        width: 100%;
        border: 1px solid #f8f3ea;
        padding: 2px;
        margin-bottom: 10px;
    }
</style>