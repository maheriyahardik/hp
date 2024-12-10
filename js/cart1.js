document.addEventListener('DOMContentLoaded', function() {
    const cartButtons = document.querySelectorAll('.btn-primary, .btn-secondary, .btn-danger');

    cartButtons.forEach(button => {
        button.addEventListener('click', function() {
            alert(`${button.innerText} functionality to be implemented.`);
        });
    });
});
