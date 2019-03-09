<template>
    <div class="leftActionBarElement" :click="action">
        <div class="leftActionBarElementIcon" :class="icon"></div>
        <div class="leftActionBarElementText">{{this.text}}</div>
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
        z-index: 1;
        font-size: 0px;

        .leftActionBarElementIcon {
            width: 30px;
            text-align: center;
            display: inline-block;
            transition: background-color .3s, color .3s;
            font-size: 1rem;
            height: 30px;
            line-height: 30px;
        }

        .leftActionBarElementText {
            display: inline-block;
            width: calc(100% - 35px);
            transition: background-color .3s;
            font-size: 1rem;
            padding-left: 5px;
        }

        &:hover .leftActionBarElementIcon {
            background-color: #bbaf99;
            color: white;
        }

        &:hover .leftActionBarElementText {
            background-color: #e4dccc;
        }
    }
</style>