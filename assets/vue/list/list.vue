<template>
    <span id="vueListContainer">
        <left-action-bar id="vueListLeftActionBar" :hasAddButton="labHasAddButton"
                         :hasDeleteButton="labHasDeleteButton"
                         :customButtons="labCustomButtons"></left-action-bar>
        <div id="vueListContent">
            <table id="vueList">
            <list-header :cols="cols" :colsProperties="colsProperties"
                         v-on:list-header-sort-up="sortUp"
                         v-on:list-header-sort-down="sortDown"
                         v-on:list-header-search="search">
            </list-header>
            <row v-for="dataRow in listData" :key="dataRow.id" :dataRow="dataRow" :cols="cols"></row>
        </table>
        </div>

    </span>

</template>

<script>
    import Row from './_row'
    import ListHeader from './_listHeader'
    import Xhr from './../../js/tools/xhr';
    import LeftActionBar from "./leftActionBar/leftActionBar";

    export default {
        data: function () {
            return {
                'listData': [],
                'sort': {},
                'searchParams': {}
            };
        },
        props: {'leftActionBarProperties': {'default': {}}, 'cols': {}, 'colsProperties': {}, 'apiEndpoint': {}},
        components: {
            LeftActionBar, Row, ListHeader
        },
        computed: {
            labHasAddButton: function () {
                return (typeof this.leftActionBarProperties.hasAddButton != 'undefined' ? this.leftActionBarProperties.hasAddButton : true);
            },
            labHasDeleteButton: function () {
                return (typeof this.leftActionBarProperties.hasDeleteButton != 'undefined' ? this.leftActionBarProperties.hasDeleteButton : true);
            },
            labCustomButtons: function () {
                return (typeof this.leftActionBarProperties.customButtons != 'undefined' ? this.leftActionBarProperties.customButtons : {});
            }
        },
        methods: {
            'load': function () {
                self = this;

                let params = {};

                // Building sort params
                for (let prop in this.sort) {
                    params['sort_' + prop] = this.sort[prop];
                }

                //Building search param
                for (let prop in this.searchParams) {
                    params['search_' + prop] = this.searchParams[prop];
                }

                Xhr.request({
                    url: this.apiEndpoint,
                    data: params,
                    success: function (xhr) {
                        self.listData = JSON.parse(xhr.response);
                    },
                    error: function (xhr) {
                        alert('Une erreur est survenue');
                    }
                });
            },

            'sortUp': function (colName) {
                if (this.sort[colName] && this.sort[colName] == 'ASC') {
                    delete this.sort[colName];
                } else {
                    this.sort[colName] = 'ASC';
                }
                this.load();
            },

            'sortDown': function (colName) {
                if (this.sort[colName] && this.sort[colName] == 'DESC') {
                    delete this.sort[colName];
                } else {
                    this.sort[colName] = 'DESC';
                }
                this.load();
            },

            'search': function (searchParamName, searchValue) {
                if (searchValue == '') {
                    delete this.searchParams[searchParamName];
                } else {
                    this.searchParams[searchParamName] = searchValue;
                }
                this.load();
            }
        },
        created: function () {
            this.load();
        },
    }
</script>

<style scoped lang="scss">
</style>

<style lang="scss">
    #vueListContainer {
        display: flex;
        height: 100%;
    }

    #vueListLeftActionBar {
        width: 30px;
        display: flex;
        flex-direction: column;
        transition: width .3s;
        height: 100%;
        background-color: #eeeae1;

        &:hover{
            width: 170px;
        }
    }

    #vueListContent {
        flex-grow: 1;

        #vueList {
            border-collapse: collapse;
            width: 100%;

            td.cell {
                padding: 5px 3px 5px 5px;
            }

            tr:not(.listListHeader):nth-child(2) {
                background-color: #f8f5ef;
            }
        }
    }


</style>