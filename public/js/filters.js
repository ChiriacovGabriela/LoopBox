window.onload = () => {
    if (document.getElementById('filters')) {
        updateContent('filters', 'content', 1);
    }
    if (document.getElementById('filtersPopup')) {
        updateContent('filtersPopup', 'contentPopup', 2);
    }
}
function updateContent(formId, contentId, id) {
    const form = document.querySelector(`#${formId}`);
    const content = document.querySelector(`#${contentId}`);

    form.querySelectorAll('input').forEach(input => {
        input.addEventListener('change', () => {
            const formData = new FormData(form);
            const params = new URLSearchParams();

            formData.forEach((value, key) => {
                params.append(key, value);
            });

            const url = new URL(window.location.href);
            fetch(`${url.pathname}?${params.toString()}&ajax=${id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    content.innerHTML = data.content;
                    console.log(content.innerHTML);
                })
                .catch(error => {
                    console.error(error);
                });
        });
    });
}