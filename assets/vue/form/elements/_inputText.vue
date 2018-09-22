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

<style scoped>

</style>