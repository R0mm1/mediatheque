<template>
    <tr class="listListHeader">
        <th v-for="(colDataAttribute, colName) in cols" class="cell">
            <div class="headerRow headerRow1">
                <div class="headerRowLabel">{{colName}}</div>
                <button v-if="isSearchEnabled(colName)" class="headerSearchButton fas fa-search"></button>
                <div v-if="isSortEnabled(colName)" class="buttonGroup">
                    <button class="headerSortButtonUp fas fa-sort-up" v-on:click="$emit('list-header-sort-up', colDataAttribute)"></button>
                    <button class="headerSortButtonDown fas fa-sort-down"></button>
                </div>
            </div>
            <div class="headerRow headerRow2" v-if="isSearchEnabled(colName)">
                <input type="text" :name="'search_'+getSearchName(colName)">
                <input type="button" :name="'submitSearch_'+getSearchName(colName)" placeholder="Rechercher..." class="fas fa-search">
            </div>
        </th>
    </tr>
</template>

<script>
    export default {
        name: 'header',
        props: ['cols', 'colsProperties'],
        methods: {
            debug: function(){
                console.log('coucou')
            },
            isSearchEnabled: function (colName) {
                return !this.colsProperties[colName]
                    || (typeof this.colsProperties[colName]['search'] === 'undefined')
                    || this.colsProperties[colName]['search'] === true;
            },
            isSortEnabled: function (colName) {
                return !this.colsProperties[colName]
                    || (typeof this.colsProperties[colName]['sort'] === 'undefined')
                    || this.colsProperties[colName]['sort'] === true;
            },
            getSearchName: function (colName) {
                if (this.colsProperties[colName] && this.colsProperties[colName]['searchName']) {
                    return this.colsProperties[colName]['searchName'];
                } else {
                    return this.cols[colName];
                }
            }
        }
    }
</script>

<style scoped>

</style>