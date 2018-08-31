var app= {
    init: function() {
        console.log('Test avec le fichier JS');

        $('.js-remove-friend').on('click',$(this), app.confirmRemove);
        $('#orgForm').on('submit',$(this), app.confirmSubmit);

    },

    confirmRemove: function () {
        var result = window.confirm('Voulez-vous vraiment retirer cette personne de votre liste de contacts?');
        return result;
    },

    confirmSubmit: function () {
        var result = window.confirm('Voulez-vous vraiment envoyer cette réponse?');
        return result;
    },
}

document.addEventListener('DOMContentLoaded', app.init);