import Vue from 'vue';
import List from '../../vue/list/list.vue'

new Vue({
    el: "#appBody",
    template: '<List :apiEndpoint="\'/api/book\'" :cols="listCols" :colsProperties="listColsProperties"/>',
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
    components: { List }
})