document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('form[action*="update-permissions"]');

    forms.forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'PUT', // Ensure this matches the HTTP method
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Permissions updated successfully!');
                    location.reload(); // Reload the page to reflect changes
                } else {
                    alert('An error occurred while updating permissions.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
