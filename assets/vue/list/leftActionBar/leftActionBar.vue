<template>
    <div id="leftActionBar">
        <div id="labColorBar"></div>
        <bar-element v-if="hasAddButton" :icon="'fas fa-plus'" :text="'Ajouter'"
                     v-on:click.native="$parent.$emit('list-action-add')"></bar-element>
        <bar-element v-if="hasDeleteButton" :icon="'fas fa-minus'" :text="'Supprimer'"
                     v-on:click.native="$parent.$emit('list-action-delete')"></bar-element>
    </div>
</template>

<script>
    import BarElement from './_barElement';

    export default {
        name: "leftActionBar",
        components: {BarElement},
        props: {
            customButtons: {'type': Object, 'default': {}},
            hasAddButton: {'type': Boolean, 'default': true},
            hasDeleteButton: {'type': Boolean, 'default': true}
        },
    }
</script>

<style scoped lang="scss">
    #labColorBar{
        height: 100%;
        width: 30px;
        position: fixed;
        background-color: #e4dccc;
        z-index: 0;
    }
</style>