<template>
    <div class="form_element form_element_entities">
        <label :for="inputId" class="entities_label">{{element.label}}</label>
        <div class="entities_input">
            <div v-for="(entity, id) in entities" class="entity">
                <div class="entity_label">{{getEntityLabel(entity)}}</div>
                <div class="entity_delete fas fa-times" v-on:click="deleteEntity(id)"></div>
            </div>
            <div class="search_container">
                <div>
                    <input :id="inputId" class="input_search" type="text" v-model="search"
                           v-on:keyup="searchEntity"/>

                    <ul class="list_proposals" :class="{opened: isProposalsDisplayed}">
                        <li v-for="(proposal, id) in proposals" v-on:click="addEntity(id, proposal)">
                            {{getEntityLabel(proposal)}}
                        </li>
                    </ul>
                </div>

                <div v-if="createUrl.length>0">
                    <input-button class="openCreate" :element="{name:'openCreate', class: 'fas fa-plus'}"
                                  v-on:click.native="toggleFormCreation"></input-button>

                    <div class="form_creation" :class="{displayed: isFormCreationDisplayed}">
                        <input-text v-for="(fieldName, fieldLabel) in labelFields"
                                    :element="{name:fieldName, label:fieldLabel}"
                                    v-on:input-text-content-changed="setCreationParam"
                                    :ref="fieldName"></input-text>

                        <input-button :element="{name: 'create', value: 'Créer'}"
                                      v-on:click.native="createEntity"></input-button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    import Xhr from '../../../js/tools/xhr';
    import Vue from 'vue';
    import InputText from "./_inputText";
    import InputButton from "./_inputButton";

    export default {
        name: "inputEntities",
        components: {InputButton, InputText},
        props: ['element', 'searchUrl', 'searchParam', 'labelFields', 'createUrl'],
        data: function () {
            return {
                search: '',
                entities: {},
                proposals: {},
                isProposalsDisplayed: false,
                isFormCreationDisplayed: false,
                creationParams: {}
            }
        },
        computed: {
            inputId: function () {
                return 'input_search_' + this.element.name;
            }
        },
        methods: {
            getEntityLabel: function (entity) {
                var label = '';
                Object.values(this.labelFields).forEach(function (field) {
                    if (entity[field]) {
                        if (label.length > 0) label += ' ';
                        label += entity[field];
                    }
                });
                return label;
            },
            deleteEntity: function (entityId) {
                this.$emit('entity-removed', this.entities[entityId]);
                Vue.delete(this.entities, entityId);
            },
            addEntity: function (entityId, entity) {
                Vue.set(this.entities, entity.id, entity);
                this.isProposalsDisplayed = false;
                this.search = '';
                this.$emit('entity-added', entity);
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
            },
            createEntity: function () {
                var self = this;
                Xhr.request({
                    url: this.createUrl,
                    method: 'POST',
                    data: this.creationParams,
                    success: function (xhr) {
                        let newEntity = JSON.parse(xhr.response);
                        self.addEntity(newEntity.id, newEntity);
                        self.isFormCreationDisplayed = false;
                        Object.values(self.labelFields).forEach(function (fieldName) {
                            self.$refs[fieldName][0].clear();
                        });
                    },
                    error: function (xhr) {
                        alert('Une erreur est survenue');
                    }
                });
            },
            toggleFormCreation: function () {
                this.isFormCreationDisplayed = !this.isFormCreationDisplayed;
            },
            setCreationParam: function (elementName, value) {
                this.creationParams[elementName] = value;
            },
            clear: function () {
                this.entities = {};
            }
        }
    }
</script>

<style scoped lang="scss">
    @import "../../../css/form/element";

    .entities_input {
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

        .search_container {
            position: relative;

            > div {
                display: inline-block;
            }

            .form_creation {
                position: absolute;
                min-width: 150px;
                background-color: #e4dccc;
                padding: 5px;

                &:not(.displayed) {
                    display: none;
                }
            }
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

<style lang="scss">
    .form_element_entities .openCreate {
        padding: 2px 4px !important;
    }
</style>