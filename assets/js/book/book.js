import Vue from 'vue';
import List from '../../vue/list/list.vue';
import BookPopup from '../../vue/book/bookPopup';

new Vue({
    el: "#appBody",
    template: '<div>' +
        '<bookPopup></bookPopup>' +
        '<List :apiEndpoint="\'/api/book\'" :cols="listCols" :colsProperties="listColsProperties"/>' +
        '</div>',
    data: function(){
        return {
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
    components: { List, BookPopup }
})