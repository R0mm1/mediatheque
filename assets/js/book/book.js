import Vue from 'vue';
import List from '../../vue/list/list.vue';
import BookPopup from '../../vue/book/bookPopup';

new Vue({
    el: "#appBody",
    template: '<div style="height: 100%; position: relative; overflow: auto;">' +
        '<bookPopup :style="{display: popupDisplayStyle}" :bookId="bookPopupElementId" v-on:popup-wanna-close="closePopup" v-on:book-saved="bookSaved"></bookPopup>' +
        '<List ref="list" :apiEndpoint="\'/api/book\'" :cols="listCols" :colsProperties="listColsProperties" v-on:list-action-add="newBook" v-on:list-action-set="setBook"/>' +
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
        setBook: function (bookId) {
            this.bookPopupElementId = bookId;
            this.popupDisplayStyle = 'flex';
        },
        bookSaved: function () {
            this.bookPopupElementId = null;
            this.closePopup();
        },
        closePopup: function () {
            this.$refs.list.load();
            this.popupDisplayStyle = 'none';
        }
    },
    components: {List, BookPopup}
})