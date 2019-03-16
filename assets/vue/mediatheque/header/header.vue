<template>
    <div id="vueHeader">
        <Menu class="headerMenu"></Menu>
        <User></User>
    </div>
</template>

<script>

    import Menu from "./_menu";
    import User from "./_user";

    export default {
        name: "header",
        components: {User, Menu}
    }
</script>

<style scoped lang="scss">
    #vueHeader {
        display: flex;
        flex: 1;

        .headerMenu {
            flex: 1;
        }
    }
</style>