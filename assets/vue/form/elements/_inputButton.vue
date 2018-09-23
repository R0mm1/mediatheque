<template>
    <div class="form_element">
        <label :for="element.name" v-on:click="trigger">{{element.value}}</label>
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
        position: relative;
    }

    label::after {
        content: ' ';
        height: 2px;
        background-color: #f8f3ea;
        position: absolute;
        bottom: 0;
        right: 0;
        left: 0;
        transition: background-color 0.3s;
    }

    label:hover::after {
        background-color: #e4dccc;
    }
</style>