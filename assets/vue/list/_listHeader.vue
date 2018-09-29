<template>
    <tr class="listListHeader">
        <th v-for="(colDataAttribute, colName) in cols" class="cell">
            <div class="headerRow headerRow1">
                <div class="headerRowLabel">{{colName}}</div>
                <button v-if="isSearchEnabled(colName)"
                        class="headerSearchButton fas fa-ellipsis-h"
                        v-on:click="toggleRowTwo(colDataAttribute)"></button>
                <div v-if="isSortEnabled(colName)" class="buttonGroup">
                    <button class="headerSortButtonUp fas fa-sort-up"
                            v-on:click="$emit('list-header-sort-up', colDataAttribute)"></button>
                    <button class="headerSortButtonDown fas fa-sort-down"
                            v-on:click="$emit('list-header-sort-down', colDataAttribute)"></button>
                </div>
            </div>
            <div class="headerRow headerRow2 headerRowFloating" v-if="isSearchEnabled(colName)"
                 :class="{headerRowHidden: !listDisplayRowTwo[colDataAttribute]}">
                <input type="text" :name="'search_'+getSearchName(colName)" placeholder="Rechercher...">
                <button :name="'submitSearch_'+getSearchName(colName)"
                        v-on:click="search"
                        class="fas fa-search"></button>
            </div>
        </th>
    </tr>
</template>

<script>
    export default {
        name: 'header',
        props: ['cols', 'colsProperties'],
        data() {
            return {
                listDisplayRowTwo: {}
            }
        },
        methods: {
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
            },
            toggleRowTwo: function (colDataAttribute) {
                if (!this.listDisplayRowTwo[colDataAttribute]) {
                    this.$set(this.listDisplayRowTwo, colDataAttribute, true);
                } else {
                    this.$set(this.listDisplayRowTwo, colDataAttribute, !this.listDisplayRowTwo[colDataAttribute]);
                }
            },
            search: function (e) {
                let searchParamName = e.target.name.split("submitSearch_")[1];
                let searchValue = this.$el.querySelector('input[name="search_' + searchParamName + '"]').value;
                this.$emit('list-header-search', searchParamName, searchValue);
            }
        }
    }
</script>

<style scoped lang="scss">
    .listListHeader {
        background-color: #d0c3a9;
    }

    .cell {
        height: 2rem;
        position: relative;
    }

    .headerRow {
        &.headerRowHidden {
            display: none;
        }

        &.headerRowFloating {
            position: absolute;
        }

        button {
            border: none;
            background: white;
        }
        > button {
            padding: 5px;
        }
    }

    .headerRow1 {
        display: flex;
        height: 100%;

        .headerRowLabel {
            flex-grow: 1;
            margin: auto;
        }

        .buttonGroup {
            height: 100%;
            > button {
                display: block;
                height: 50%;
            }
        }
    }
</style>