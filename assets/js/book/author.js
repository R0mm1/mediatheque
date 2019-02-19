import Vue from 'vue';
import List from '../../vue/list/list.vue';
import AuthorPopup from '../../vue/book/authorPopup';

new Vue({
    el: "#appBody",
    template: '<div style="height: 100%; position: relative; overflow: auto;">' +
        '<authorPopup :style="{display: popupDisplayStyle}" :authorId="bookPopupElementId" v-on:popup-wanna-close="closePopup" v-on:author-saved="authorSaved"></authorPopup>' +
        '<List ref="list" :apiEndpoint="\'/api/author\'" :cols="listCols" :colsProperties="listColsProperties" :rowActions="rowActions" v-on:custom-action-triggered="customActionTriggered" v-on:list-action-add="newAuthor" v-on:list-action-set="setAuthor"/>' +
        '</div>',
    data: function () {
        return {
            popupDisplayStyle: 'none',
            bookPopupElementId: null,
            listCols: {
                'Nom': 'lastname',
                'Prénom': 'firstname',
            },
            listColsProperties: {},
            rowActions: {}
        }
    },
    methods: {
        newAuthor: function () {
            this.bookPopupElementId = null;
            this.popupDisplayStyle = 'flex';
        },
        setAuthor: function (authorId) {
            this.bookPopupElementId = authorId;
            this.popupDisplayStyle = 'flex';
        },
        customActionTriggered: function () {
        },
        authorSaved: function () {
            this.bookPopupElementId = null;
            this.closePopup();
        },
        closePopup: function () {
            this.$refs.list.load();
            this.popupDisplayStyle = 'none';
        }
    },
    components: {List, AuthorPopup}
});