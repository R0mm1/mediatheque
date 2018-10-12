<template>
    <div class="form_element form_element_text">
        <input type="text" :name="element.name" :value="element.value" :placeholder="element.placeholder"/>
    </div>
</template>

<script>
    export default {
        name: "inputText",
        props: ['element']
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