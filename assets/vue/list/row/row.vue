<template>
    <tr class="listRow" :id="elementId">
        <td v-for="(col, index) in cols" :key="index" class="cell">
            {{getValueFromColDef(col)}}
        </td>

        <td v-if="hasRowAction" class="listRowCustomActions">
            <CustomAction v-for="(actionData, actionName) in rowActions"
                          :key="actionName"
                          :data="actionData"
                          :name="actionName"
                          :rowData="dataRow"
                          v-on:custom-action-triggered="customActionTriggered"></CustomAction>
        </td>
    </tr>
</template>

<script>
    import InputButton from "../../form/elements/_inputButton";
    import CustomAction from "./_customAction";

    export default {
        name: 'row',
        components: {CustomAction, InputButton},
        props: ['dataRow', 'cols', 'rowActions'],
        data: function () {
            return {
                'rowActionClicks': {}
            }
        },
        computed: {
            elementId: function () {
                return 'el' + (this.dataRow['id'] ? this.dataRow['id'] : Math.random().toString());
            },
            hasRowAction: function () {
                return Object.keys(this.rowActions).length > 0;
            }
        },
        methods: {
            getValueFromColDef: function (path, scope) {

                var self = this;
                var value = '';

                path = JSON.parse(JSON.stringify(path));

                if (typeof scope === 'undefined') scope = this.dataRow;
                scope = JSON.parse(JSON.stringify(scope));

                if (Array.isArray(path)) {
                    //If path is array, it's some properties we wan't to extract from the scope
                    var extractFromScope = function (scopeElement) {
                        var values = [];
                        path.forEach(function (pathElement) {
                            if (scopeElement[pathElement]) {
                                values.push(scopeElement[pathElement]);
                            }
                        });
                        return values.join(' ');
                    };

                    //The scope may be an array of object or just an object
                    if (Array.isArray(scope)) {
                        values = [];
                        scope.forEach(function (scopeElement) {
                            values.push(extractFromScope(scopeElement));
                        });
                        value = values.join(', ');
                    } else {
                        value = extractFromScope(scope);
                    }

                } else if (typeof path === 'object') {
                    //If path is an object, it indicate in value some properties to extract from an object in the scope
                    //having the same name as the key

                    var values = [];
                    Object.keys(path).forEach(function (element) {
                        values.push(self.getValueFromColDef(path[element], scope[element]));
                    });
                    value = values.join(' ');
                } else if (typeof path === 'string' && scope[path]) {
                    //If path is a string, it's a property name to extract from the scope

                    value = scope[path];
                }

                return value;
            },
            customActionTriggered: function (actionName) {
                this.$parent.$emit('custom-action-triggered', actionName, this.dataRow['id']);
            }
        }
    }
</script>

<style scoped lang="scss">
    .listRow {
        height: calc(1.5rem - 2px);
        transition: all 0.3s;
        border-style: solid;
        border-width: 1px 0px 1px 0px;
        border-color: transparent;
    }

    .listRow:hover {
        border-color: #d0c3a9;

        .listRowCustomActions {
            opacity: 1;
            visibility: visible;
        }
    }

    .listRowCustomActions {
        display: flex;
        opacity: 0;
        visibility: hidden;
        transition: all .3s;
    }
</style>