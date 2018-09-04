<template>
    <table id="vueList">
        <row-header :cols="cols" :colsProperties="colsProperties"></row-header>
        <row v-for="dataRow in data" :key="dataRow.id" :dataRow="dataRow" :cols="cols"></row>
    </table>
</template>

<script>
    import Row from './_row'
    import ListHeader from './_listHeader'

    export default {
        props: ['cols', 'data', 'colsProperties'],
        components: {
            Row, RowHeader: ListHeader
        }
    }
</script>

<style scoped lang="scss">
</style>

<style lang="scss">
    /*.cell {*/
        /*display: inline-block;*/
        /*width: 250px;*/
    /*}*/
</style>