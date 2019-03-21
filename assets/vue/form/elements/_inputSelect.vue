<template>
    <div class="form_element form_element_select">
        <label v-on:click="displayOptions">{{label}}</label>
        <div class="selector" v-on:click="displayOptions">
            <div class="selectedValue" :class="{noValue: (cValueId===null)}">{{cValue}}</div>

            <span class="select_arrow fas fa-caret-down" :class="{is_up: optionsDisplayed}"></span>

            <ul class="options" :class="{displayed: optionsDisplayed}">
                <li v-for="(option, optionId) in unsourcedOptions" v-on:click="change(optionId)"
                    :class="{isSelected: optionId==defaultValue}">
                    {{option}}
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    export default {
        name: "inputSelect",
        props: {
            //options can be either an optionId:optionName object
            //or a method which return an object respecting the formatting above
            //or a method that return a promise which will give an object respecting the formatting above
            'options': {'default': {}},
            'name': {'default': ''},
            'label': {'default': ''},
            'placeholder': {'default': 'Sélectionnez une valeur'},
            'defaultValue': {'default': null}
        },
        data() {
            return {
                value: '',
                valueId: null,
                unsourcedOptions: {},
                optionsDisplayed: false
            };
        },
        methods: {
            change(optionId) {
                this.valueId = optionId;
                this.value = this.unsourcedOptions[optionId];
                this.$emit('select-changed', this.valueId, this.value);
            },
            clear() {
                this.valueId = null;
                this.value = '';
            },
            displayOptions() {
                this.optionsDisplayed = !this.optionsDisplayed;
            }
        },
        computed: {
            cValue() {
                let valueId = this.cValueId;
                //We use the value's label or the placeholder if we have no value to display
                return valueId === null ? this.placeholder : this.unsourcedOptions[valueId];
            },
            cValueId() {
                let valueId = this.valueId; //We take the selected value

                //If we have a value but it's not in the list, we switch back to null
                if (valueId !== null && typeof this.unsourcedOptions[valueId] === 'undefined') valueId = null;

                return valueId;
            }
        },
        watch: {
            defaultValue(newVal) {
                if (this.valueId === null) {
                    this.valueId = newVal;
                }
            }
        },
        mounted() {
            if (typeof this.options === 'function') {
                let returnValue = this.options();
                if (returnValue instanceof Promise) {
                    returnValue
                        .then(data => {
                            this.unsourcedOptions = data;
                        })
                        .catch(() => {
                            alert('Erreur: la liste des éléments du select n\'a pas pu être chargée');
                        });
                } else {
                    this.unsourcedOptions = returnValue;
                }
            } else {
                this.unsourcedOptions = this.options;
            }
        }
    }
</script>

<style scoped lang="scss">
    @import "../../../css/form/element";

    .form_element_select {
        display: flex;

        label {
            width: 25%
        }

        .selector {
            flex: 1;
            position: relative;

            .selectedValue {
                width: calc(100% - 2px);
                height: 100%;
                background-color: white;
                padding: 1px;

                &.noValue {
                    font-style: italic;
                    color: #8b8b8b;
                }
            }

            .select_arrow {
                position: absolute;
                top: 2px;
                right: 4px;
                transition: all .3s;

                &.is_up {
                    transform: rotate(180deg);
                }
            }

            .options {
                position: absolute;
                list-style-type: none;
                margin: 0;
                padding: 0;
                background-color: #f8f5ef;
                border: 1px solid #e4dccc;
                width: calc(100% - 2px);
                transition: all .3s;

                &:not(.displayed) {
                    transform-origin: top;
                    transform: scaleY(0);
                }

                li {
                    padding: 3px;

                    &:hover {
                        background-color: #e4dccc;
                    }
                }
            }
        }
    }
</style>