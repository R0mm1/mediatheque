import Vue from 'vue';
import List from '../../vue/list/list.vue'

new Vue({
    el: "#appBody",
    template: '<List :cols="listCols" :data="listData" :colsProperties="listColsProperties"/>',
    data: function(){
        return {
            listData: [],
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
    mounted: function(){
        self = this;

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function (event) {
            if (this.readyState === XMLHttpRequest.DONE) {
                if (this.status === 200) {
                    self.listData = JSON.parse(xhr.response);
                } else {
                    alert('Une erreur est survenue');
                }
            }
        };
        xhr.open('GET', '/api/book', true);
        xhr.send(null);
    },
    method: {
      // data: {},

    },
    components: { List }
})