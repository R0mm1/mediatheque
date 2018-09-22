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

    Xhr.request();

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

                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function (event) {
                    if (this.readyState === XMLHttpRequest.DONE) {
                        if (this.status === 200) {
                            self.listData = JSON.parse(xhr.response);
                        } else {
                            alert('Une erreur est survenue');
                        }
                    }
                };

                //Building sort params
                let sortParam = '';
                for (let prop in this.sort) {
                    if (sortParam != '') {
                        sortParam += '&';
                    }
                    sortParam += 'sort_' + prop + '=' + this.sort[prop];
                }

                xhr.open('GET', this.apiEndpoint + '?' + sortParam, true);
                xhr.send(sortParam);
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
</style>