<template>
    <img :src="'/images/loaders/'+type+'.svg'">
</template>

<script>
    export default {
        name: "loader",
        props: {
            type: {
                default: 'b'
            }
        }
    }
</script>

<style scoped>
</style>