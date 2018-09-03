<template>
    <div>
        <row-header :cols="cols"></row-header>
        <row v-for="dataRow in data" :key="dataRow.id" :dataRow="dataRow" :cols="cols"></row>
    </div>
</template>

<script>
    import Row from './_row'
    import ListHeader from './_listHeader'

    export default {
        props: ['cols', 'data'],
        components: {
            Row, RowHeader: ListHeader
        }
    }
</script>

<style scoped>

</style>