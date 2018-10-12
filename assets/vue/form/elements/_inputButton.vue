<template>
    <div class="form_element form_element_button">
        <label :for="element.name" :class="element.class" v-on:click="trigger">{{element.value}}</label>
        <input :type="type" :name="element.name" :value="element.value"/>
    </div>
</template>

<script>
    export default {
        name: "inputButton",
        props: ['element'],
        computed: {
            type: function () {
                return this.element.isSubmit ? 'submit' : 'button';
            },
            value: function () {
                return this.element.value ? this.element.value : 'Enregistrer';
            }
        },
        methods: {
            trigger: function () {
                this.$el.querySelector('input').click();
            }
        }
    }
</script>

<style scoped lang="scss">
    input {
        display: none;
    }

    label {
        background: #f8f3ea;
        padding: 7px;
        font-size: .9rem;
    }

    .form_element_button {
        position: relative;
        display: inline-block;
    }

    .form_element_button::after {
        content: ' ';
        height: 2px;
        background-color: #f8f3ea;
        transition: background-color 0.3s;
        position: absolute;
        bottom: -7px;
        left: 0;
        right: 0;
    }

    .form_element_button:hover::after {
        background-color: #e4dccc;
    }
</style>