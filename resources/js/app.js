import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-product-like]');
    if (!button || button.disabled) return;

    const container = button.closest('[data-product-engagement]');
    const copies = [...document.querySelectorAll('[data-product-engagement]')]
        .filter((element) => element.dataset.productEngagement === container.dataset.productEngagement);
    const error = container.querySelector('[data-like-error]');
    copies.forEach((element) => { element.querySelector('button').disabled = true; });
    error.classList.add('hidden');

    try {
        const response = await fetch(button.dataset.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ liked: button.getAttribute('aria-pressed') !== 'true' }),
        });
        if (!response.ok) throw new Error('Unable to update your like. Please refresh and try again.');
        const data = await response.json();
        copies.forEach((element) => {
            const likeButton = element.querySelector('button');
            likeButton.setAttribute('aria-pressed', String(data.liked));
            likeButton.classList.toggle('text-red-600', data.liked);
            likeButton.querySelector('i').classList.toggle('fa-solid', data.liked);
            likeButton.querySelector('i').classList.toggle('fa-regular', !data.liked);
            element.querySelector('[data-product-likes]').textContent = Number(data.likes_count).toLocaleString();
        });
    } catch (exception) {
        error.textContent = exception.message;
        error.classList.remove('hidden');
    } finally {
        copies.forEach((element) => { element.querySelector('button').disabled = false; });
    }
});
