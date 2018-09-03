<template>
    <div>
        <div v-for="(col, index) in cols" :key="index">
            {{dataRow[col]}}
        </div>
    </div>
</template>

<script>
    export default {
        name: 'row',
        props: ['dataRow', 'cols']
    }
</script>

<style scoped>

</style>