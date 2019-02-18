<template>
    <div class="rowCustomAction" v-if="isDisplayed">
        <input-button
                :element="{class: this.data.class}"
                v-on:click.native="triggerCustomAction"></input-button>

        <div class="customActionConfirm" v-if="confirm.length>0" :class="{isDisplayed: confirmDisplayed}">
            {{confirm}}
        </div>
    </div>

</template>

<script>
    import InputButton from "../../form/elements/_inputButton";

    export default {
        name: "customAction",
        components: {InputButton},
        props: {
            data: {},
            name: '',
            rowData: {}
        },
        data: function () {
            return {
                clickCounter: 0,
                confirmDisplayed: false
            }
        },
        computed: {
            confirm: function () {
                return (typeof this.data.confirm !== 'undefined') ? this.data.confirm : '';
            },
            isDisplayed: function () {
                let isDisplayed = true;
                if (typeof this.data.getIsDisplayed === 'function') {
                    isDisplayed = this.data.getIsDisplayed(this.rowData);
                }
                return isDisplayed;
            }
        },
        methods: {
            triggerCustomAction: function (event) {

                this.clickCounter++;

                let triggerEvent = true;
                if (typeof this.data.confirm !== 'undefined') {
                    if (this.clickCounter === 1) {
                        this.confirmDisplayed = true;
                        triggerEvent = false;
                    }
                }

                if (triggerEvent) {
                    this.$emit('custom-action-triggered', this.name);
                    this.confirmDisplayed = false;
                    this.clickCounter = 0;
                }

                event.stopPropagation();
            }
        }
    }
</script>

<style scoped lang="scss">
    .form_element_button {
        border-radius: 50%;
        width: 10px;
        height: 10px;
        text-align: center;
        font-size: .8rem;
    }

    .customActionConfirm {
        font-size: .8rem;
        background-color: black;
        color: white;
        padding: 2px;
        position: absolute;
        transform: translateX(-50%);

        &:not(.isDisplayed) {
            display: none;
        }
    }
</style>