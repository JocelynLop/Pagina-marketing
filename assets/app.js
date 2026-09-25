import './stimulus_bootstrap.js';
import './styles/app.css';

const searchInput = document.querySelector('[data-publication-search]');
const cards = [...document.querySelectorAll('[data-publication-card]')];
const noResults = document.querySelector('[data-no-results]');

if (searchInput && cards.length > 0) {
    searchInput.addEventListener('input', () => {
        const term = searchInput.value.trim().toLowerCase();
        let visibleCards = 0;

        cards.forEach((card) => {
            const isVisible = card.textContent.toLowerCase().includes(term);
            card.hidden = !isVisible;
            visibleCards += isVisible ? 1 : 0;
        });

        if (noResults) {
            noResults.hidden = visibleCards > 0;
        }
    });
}

const form = document.querySelector('[data-publication-form]');

if (form) {
    const titleInput = form.querySelector('[data-preview-title]');
    const categoryInput = form.querySelector('[data-preview-category]');
    const summaryInput = form.querySelector('[data-preview-summary]');
    const imageInput = form.querySelector('[data-preview-image]');
    const titleOutput = document.querySelector('[data-preview-title-output]');
    const categoryOutput = document.querySelector('[data-preview-category-output]');
    const summaryOutput = document.querySelector('[data-preview-summary-output]');
    const imageBox = document.querySelector('[data-preview-image-box]');

    const updatePreview = () => {
        titleOutput.textContent = titleInput.value.trim() || 'Titulo de la publicacion';
        categoryOutput.textContent = categoryInput.value.trim() || 'General';
        summaryOutput.textContent = summaryInput.value.trim() || 'El resumen aparecera aqui mientras escribes.';

        const imageUrl = imageInput.value.trim();
        imageBox.replaceChildren();

        if (imageUrl) {
            const image = document.createElement('img');
            image.src = imageUrl;
            image.alt = '';
            imageBox.append(image);

            return;
        }

        const icon = document.createElement('i');
        icon.className = 'bi bi-image';
        imageBox.append(icon);
    };

    form.addEventListener('input', updatePreview);
    form.addEventListener('reset', () => window.setTimeout(updatePreview, 0));
}

document.querySelectorAll('[data-confirm-delete]').forEach((deleteForm) => {
    deleteForm.addEventListener('submit', (event) => {
        if (!window.confirm('Deseas eliminar esta publicacion?')) {
            event.preventDefault();
        }
    });
});
