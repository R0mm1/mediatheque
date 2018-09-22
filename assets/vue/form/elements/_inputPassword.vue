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

<style scoped>

</style>