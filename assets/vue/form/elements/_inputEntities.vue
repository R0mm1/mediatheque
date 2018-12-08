<template>
    <div class="form_element form_element_entities">
        <div v-for="(entity, id) in entities" class="entity">
            <div class="entity_label">{{getEntityLabel(entity)}}</div>
            <div class="entity_delete fas fa-times" v-on:click="deleteEntity(id)"></div>
        </div>
        <div class="search_container">
            <input class="input_search" type="text" v-model="search" v-on:keyup="searchEntity"/>
            <ul class="list_proposals" :class="{opened: isProposalsDisplayed}">
                <li v-for="(proposal, id) in proposals" v-on:click="addEntity(id, proposal)">
                    {{getEntityLabel(proposal)}}
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    import Xhr from '../../../js/tools/xhr';
    import Vue from 'vue';

    export default {
        name: "inputEntities",
        props: ['element', 'searchUrl', 'searchParam', 'labelFields'],
        data: function () {
            return {
                search: '',
                entities: {},
                proposals: {},
                isProposalsDisplayed: false
            }
        },
        methods: {
            getEntityLabel: function (entity) {
                var label = '';
                this.labelFields.forEach(function (field) {
                    if (entity[field]) {
                        if (label.length > 0) label += ' ';
                        label += entity[field];
                    }
                });
                return label;
            },
            deleteEntity: function (entityId) {
                Vue.delete(this.entities, entityId);
                // console.log('hello');
                // let entities = this.entities
                // delete entities[entityId];
                // this.entities = entities;
            },
            addEntity: function (entityId, entity) {
                Vue.set(this.entities, entity.id, entity);
                this.isProposalsDisplayed = false;
                this.search = '';
            },
            searchEntity: function (e) {
                let search = e.target.value;
                if (search.length > 2) {
                    var self = this;

                    var data = {};
                    data[this.searchParam] = search;

                    Xhr.request({
                        url: this.searchUrl,
                        data: data,
                        success: function (xhr) {
                            self.proposals = {};
                            let data = JSON.parse(xhr.response);
                            data.forEach(function (entity) {
                                Vue.set(self.proposals, entity.id, entity);
                            });
                            self.isProposalsDisplayed = true;
                        },
                        error: function (xhr) {
                            alert('Une erreur est survenue');
                        }
                    });
                }
            }
        }
    }
</script>

<style scoped lang="scss">
    .form_element_entities {
        border: 1px solid #bbb;
        border-radius: 3px;
        padding: 0.4em 0.6em;

        > div {
            display: inline-block;
        }

        .entity {
            padding: 1px 6px;
            margin: 0px 5px 2px 0px;
            background-color: #e4dccc;
            height: 1.2rem;

            > div {
                display: inline-block;
                vertical-align: middle;
            }

            .entity_delete {
                width: 0px;
                overflow: hidden;
                transition: width .3s;
            }

            &:hover .entity_delete {
                width: 14px;
            }
        }

        .search_container{
            position: relative;
        }

        .input_search {
            border: none;
            background-color: #e4dccc;
            height: 1.2rem;
        }

        .list_proposals {
            position: absolute;
            margin: 0px;
            padding: 0;
            list-style-type: none;
            background-color: #f8f5ef;
            border: 1px solid #e4dccc;
            width: calc(100% - 2px);

            li {
                padding: 3px;

                &:hover {
                    background-color: #e4dccc;
                }
            }

            &:not(.opened) {
                display: none;
            }
        }
    }
</style>