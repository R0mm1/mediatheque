import Xhr from './xhr';

export default {
    downloadEBook: function (bookId) {
        let filename = 'book.epub'; //default file name

        Xhr.fetch('/api/book/' + bookId + '/ebook', {
            method: 'GET'
        })
            .then(response => {

                //Retrieving file name from Content-Disposition Header
                let contentDisposition = response.headers.get('Content-Disposition');
                let filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                let matches = filenameRegex.exec(contentDisposition);
                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }

                return response.blob();
            })
            .then(blob => {
                //Trigger file download
                let file = new File([blob], filename);
                let objectUrl = URL.createObjectURL(file);
                window.open(objectUrl);
            })
            .catch(error => {
                alert('here ' + error);
            });
    },
    deleteBook(bookId) {
        return Xhr.fetch('/api/book/' + bookId, {
            method: 'DELETE'
        });
    }
}