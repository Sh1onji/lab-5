// Ask for confirmation before any delete form is submitted
document.addEventListener('DOMContentLoaded', function () {
    var deleteForms = document.querySelectorAll('.js-delete-form');

    deleteForms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            var ok = confirm('Delete this task? This cannot be undone.');
            if (!ok) {
                event.preventDefault();
            }
        });
    });
});
