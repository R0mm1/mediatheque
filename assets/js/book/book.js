import Vue from 'vue';
import List from '../../vue/list/list.vue';
import BookPopup from '../../vue/book/bookPopup';

new Vue({
    el: "#appBody",
    template: '<div style="height: 100%; position: relative">' +
        '<bookPopup :style="{display: popupDisplayStyle}" :bookId="bookPopupElementId" v-on:popup-wanna-close="closePopup"></bookPopup>' +
        '<List :apiEndpoint="\'/api/book\'" :cols="listCols" :colsProperties="listColsProperties" v-on:list-action-add="newBook"/>' +
        '</div>',
    data: function () {
        return {
            popupDisplayStyle: 'none',
            bookPopupElementId: null,
            listCols: {
                'Titre': 'title',
                'Année': 'year',
                'Langue': 'language',
                'Auteur': {'authors': ['firstname', 'lastname']}
            },
            listColsProperties: {
                'Auteur': {'sort': false, 'searchName': ['author']}
            }
        }
    },
    methods: {
        newBook: function () {
            this.bookPopupElementId = null;
            this.popupDisplayStyle = 'flex';
        },
        closePopup: function () {
            this.popupDisplayStyle = 'none';
        }
    },
    components: {List, BookPopup}
})