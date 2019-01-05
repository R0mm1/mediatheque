<template>
    <div class="form_element form_element_switch">
        <label :for="element.name">{{element.label}}</label>
        <div class="switch_cursor_container" :class="{switch_on: switchState}" v-on:click="toggleSwitch">
            <div class="switch_cursor"></div>
        </div>
        <input type="checkbox" v-model="switchState" :name="element.name">
    </div>

</template>

<script>
    export default {
        name: "inputSwitch",
        props: {element: {default: {label: ''}}, state: {default: false}},
        data: function () {
            return {switchState: null}
        },
        methods: {
            toggleSwitch: function () {
                this.switchState = !this.switchState;
                this.$emit('input-switch-state-changed', this.element.name, this.switchState);
            }
        },
        mounted: function () {
            this.switchState = this.state;
        }
    }
</script>

<style scoped lang="scss">
    @import "../../../css/form/element";

    input[type="checkbox"] {
        display: none;
    }

    .switch_cursor_container {
        width: 4rem;
        height: 1.5rem;
        border: 1px solid #d0c3a9;
        position: relative;
    }

    .switch_cursor_container {
        transition: .3s;

        .switch_cursor {
            position: absolute;
            left: 0px;
            width: calc(50% - 2px);
            height: calc(100% - 2px);
            border: 1px solid #d0c3a9;
            transition: .3s;
            background-color: white;
        }

        &.switch_on {
            background-color: #d0c3a9;

            .switch_cursor {
                left: 50%;
            }
        }
    }
</style>