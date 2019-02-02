<template>
    <div class="form_element form_element_button" :class="{no_auto_margin: !autoMargin}">
        <label :for="element.name" :class="classname" v-on:click="trigger">{{element.value}}</label>
        <input :type="type" :name="element.name" :value="element.value"/>
    </div>
</template>

<script>
    export default {
        name: "inputButton",
        props: {
            element: {default: {}},
            disabled: {default: false}
        },
        computed: {
            autoMargin: function () {
                return this.element.autoMargin ? this.element.autoMargin : true;
            },
            type: function () {
                return this.element.isSubmit ? 'submit' : 'button';
            },
            value: function () {
                return this.element.value ? this.element.value : 'Enregistrer';
            },
            classname: function () {
                let className = this.element.class;
                if (this.disabled) {
                    className += ' disabled';
                }
                return className;
            }
        },
        methods: {
            trigger: function (e) {
                if (!this.disabled) {
                    this.$el.querySelector('input').click();
                    e.stopPropagation();
                }
            }
        }
    }
</script>

<style scoped lang="scss">
    @import "../../../css/form/element";

    input {
        display: none;
    }

    .form_element_button {
        position: relative;
        display: inline-block;
        background: #f8f3ea;
        padding: 7px;
        font-size: .9rem;
        transition: all .3s;

        label.disabled {
            color: #6b6b6b;
        }
    }

    .form_element_button:hover {
        background-color: #e4dccc;
    }
</style>