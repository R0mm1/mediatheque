<template>
    <div id="vueHeader">
        <Menu></Menu>
    </div>
</template>

<script>

    import Menu from "./_menu";

    export default {
        name: "header",
        components: {Menu}
    }
</script>

<style scoped lang="scss">
    #vueHeader {
        display: flex;
    }
</style>