<template>
    <table id="vueList">
        <list-header :cols="cols" :colsProperties="colsProperties"
                     v-on:list-header-sort-up="sortUp"></list-header>
        <row v-for="dataRow in listData" :key="dataRow.id" :dataRow="dataRow" :cols="cols"></row>
    </table>
</template>

<script>
    import Row from './_row'
    import ListHeader from './_listHeader'
    import Xhr from './../../js/tools/xhr';

    export default {
        data: function () {
            return {
                'listData': [],
                'sort': {}
            };
        },
        props: ['cols', 'colsProperties', 'apiEndpoint'],
        components: {
            Row, ListHeader
        },
        methods: {
            'load': function () {
                self = this;

                // Building sort params
                let sortParam = {};
                for (let prop in this.sort) {
                    sortParam['sort_' + prop] = this.sort[prop];
                }

                Xhr.request({
                    url: this.apiEndpoint,
                    data: sortParam,
                    success: function (xhr) {
                        self.listData = JSON.parse(xhr.response);
                    },
                    error: function (xhr) {
                        alert('Une erreur est survenue');
                    }
                });
            },

            'sortUp': function (colName) {
                this.sort[colName] = 'ASC';
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
    #vueList {
        border-collapse: collapse;
        width: 100%;
    }

    td.cell {
        padding: 5px 3px 5px 5px;
    }

    tr:not(.listListHeader):nth-child(2) {
        background-color: #f8f5ef;
    }
</style>