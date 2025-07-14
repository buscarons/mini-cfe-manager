document.addEventListener('DOMContentLoaded', () => {
    const processButtons = document.querySelectorAll('.button-process');

    processButtons.forEach(button => {
        button.addEventListener('click', () => {
            const cfeId = button.dataset.id;
            
            button.disabled = true;
            button.textContent = 'Processing...';

            fetch('process_cfe.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${cfeId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const statusCell = document.getElementById(`status-${cfeId}`);
                    statusCell.textContent = data.new_status;

                    const actionsCell = document.getElementById(`actions-${cfeId}`);
                    actionsCell.innerHTML = ''; 
                } else {
                    alert('An error occurred. Please try again.');
                    button.disabled = false;
                    button.textContent = 'Process';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('A network error occurred.');
                button.disabled = false;
                button.textContent = 'Process';
            });
        });
    });

    const alertBox = document.querySelector('.alert');
    if (alertBox) {
        const closeButton = alertBox.querySelector('.alert-close');
        if (closeButton) {
            closeButton.addEventListener('click', () => {
                alertBox.style.display = 'none';
            });
        }
    }
});