<template>
    <div class="form_element form_element_button" :class="{no_auto_margin: !autoMargin}">
        <label :for="element.name" :class="element.class" v-on:click="trigger">{{element.value}}</label>
        <input :type="type" :name="element.name" :value="element.value"/>
    </div>
</template>

<script>
    export default {
        name: "inputButton",
        props: ['element'],
        computed: {
            autoMargin: function () {
                return this.element.autoMargin ? this.element.autoMargin : true;
            },
            type: function () {
                return this.element.isSubmit ? 'submit' : 'button';
            },
            value: function () {
                return this.element.value ? this.element.value : 'Enregistrer';
            }
        },
        methods: {
            trigger: function (e) {
                this.$el.querySelector('input').click();
                e.stopPropagation();
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
    }

    .form_element_button:hover {
        background-color: #e4dccc;
    }
</style>