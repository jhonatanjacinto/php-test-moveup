(function (wp) {
    const { dispatch } = wp.data;

    const handleInputChange = ( inputField ) => {
        const { name, value } = inputField;
        dispatch('core/editor').editPost({ meta: { [name]: value } });
    }

    setupInputListeners = () => {
        const metaFields = ['_book_author', '_book_isbn', '_book_publication_date'];
        metaFields.forEach( (metaField) => {
            const inputField = document.querySelector(`input[name="${metaField}"]`);
            inputField.addEventListener('change', () => handleInputChange(inputField));
        });
    }

    setupInputListeners();
    
})(window.wp);