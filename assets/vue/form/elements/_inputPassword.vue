<template>
    <div class="form_element">
        <input type="password" :name="element.name" :placeholder="element.placeholder"/>
    </div>
</template>

<script>
    export default {
        name: "inputPassword",
        props: ['element']
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