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
                <div class="headerRowContent">
                    <input-text
                            :element="{name: 'search_'+getSearchName(colName), placeholder: 'Rechercher...'}"></input-text>
                    <button :name="'submitSearch_'+getSearchName(colName)"
                            v-on:click="search"
                            class="fas fa-search"></button>
                </div>
            </div>
        </th>
    </tr>
</template>

<script>
    import InputText from '../form/elements/_inputText';

    export default {
        name: 'header',
        components: {InputText},
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
            .headerRowContent {
                margin-top: -35px;
            }
        }

        &.headerRowFloating {
            overflow: hidden;
            position: absolute;
            right: 0px;

            .headerRowContent {
                transition: margin-top .3s;
                display: flex;
                background-color: #d0c3a9;
                padding: 5px;
            }
        }

        button {
            border: none;
            background: white;
            transition: background-color .3s, color .3s;

            &:hover {
                background: #bbaf99;
                color: white;
            }
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
            border-radius: 0px 5px 0px 0px;
            overflow: hidden;
            > button {
                display: block;
                height: 50%;
            }
        }
    }
</style>

<style lang="scss">
    .headerRow2 {
        .form_element input {
            margin-bottom: initial !important;
            height: 19px;
        }
    }
</style>