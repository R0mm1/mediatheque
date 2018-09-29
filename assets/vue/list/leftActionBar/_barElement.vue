<template>
    <div class="leftActionBarElement" :click="action">
        <div class="leftActionBarElementIcon" :class="icon"></div>
        <div class="leftACtionBarElementText">{{this.text}}</div>
    </div>
</template>

<script>
    export default {
        name: "barElement",
        props: ['icon', 'text', 'action']
    }
</script>

<style scoped lang="scss">
    .leftActionBarElement {
        line-height: 30px;
        height: 30px;
        overflow: hidden;
        cursor: pointer;
    }

    .leftActionBarElementIcon {
        width: 30px;
        text-align: center;
        display: inline-block;
    }

    .leftACtionBarElementText {
        display: inline-block;
    }
</style>