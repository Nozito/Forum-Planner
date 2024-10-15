document.addEventListener('DOMContentLoaded', () => {
    const stars = document.querySelectorAll('#rating span');
    const ratingValueInput = document.getElementById('ratingValue');

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const rating = star.getAttribute('data-value');
            ratingValueInput.value = rating; // Set the hidden input value
            stars.forEach(s => {
                s.classList.toggle('text-yellow-500', s.getAttribute('data-value') <= rating);
                s.classList.toggle('text-gray-400', s.getAttribute('data-value') > rating);
            });
        });
    });
});