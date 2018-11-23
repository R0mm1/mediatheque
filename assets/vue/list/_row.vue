<template>
    <tr class="listRow" :id="elementId">
        <td v-for="(col, index) in cols" :key="index" class="cell">
            {{getValueFromColDef(col)}}
        </td>
    </tr>
</template>

<script>
    export default {
        name: 'row',
        props: ['dataRow', 'cols'],
        computed: {
            elementId: function () {
                return 'el' + (this.dataRow['id'] ? this.dataRow['id'] : Math.random().toString());
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
            }
        }
    }
</script>

<style scoped lang="scss">
    .listRow {
        height: 1.5rem;
        transition: height 0.3s;
    }

    .listRow:hover {
        height: 2rem;
    }
</style>