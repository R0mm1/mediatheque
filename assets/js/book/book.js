import Vue from 'vue';
import List from '../../vue/list/list.vue';
import BookPopup from '../../vue/book/bookPopup';
import Book from '../tools/book';

new Vue({
    el: "#appBody",
    template: '<div style="height: 100%; position: relative; overflow: auto;">' +
        '<bookPopup :style="{display: popupDisplayStyle}" :bookId="bookPopupElementId" v-on:popup-wanna-close="closePopup" v-on:book-saved="bookSaved"></bookPopup>' +
        '<List ref="list" :apiEndpoint="\'/api/book\'" :cols="listCols" :colsProperties="listColsProperties" :rowActions="rowActions" v-on:custom-action-triggered="customActionTriggered" v-on:list-action-add="newBook" v-on:list-action-set="setBook"/>' +
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
            },
            rowActions: {
                'download': {
                    'label': '',
                    'class': 'fas fa-file-download',
                    'getIsDisplayed': function (book) {
                        return (typeof book.ebook != 'undefined');
                    }
                },
                'delete': {
                    'label': '',
                    'class': 'far fa-trash-alt',
                    'confirm': 'Re-cliquez pour confirmer la suppression'
                }
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
        customActionTriggered: function (action, bookId) {
            switch (action) {
                case 'download':
                    Book.downloadEBook(bookId);
                    break;
                case 'delete':
                    Book.deleteBook(bookId).then(() => this.$refs.list.load());
                    break;
            }
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
});