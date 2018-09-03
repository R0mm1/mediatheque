<template>
    <div>
        <div v-for="(colDataAttribute, colName) in cols" class="rowHeader">
            {{colName}}
        </div>
    </div>
</template>

<script>
    export default {
        name: 'header',
        props: ['cols']
    }
</script>

<style scoped>

</style>