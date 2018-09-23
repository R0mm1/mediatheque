<template>
    <div class="form_element">
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
        width: 100%;
        border: 1px solid #f8f3ea;
        padding: 2px;
        margin-bottom: 10px;
    }
</style>