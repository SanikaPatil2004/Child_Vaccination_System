document.addEventListener("DOMContentLoaded", function () {
    // Add any JS interactions if necessary
    console.log("Page loaded and ready.");
    
    // Example: Highlighting the row on click (optional feature)
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        row.addEventListener('click', () => {
            rows.forEach(r => r.classList.remove('highlight'));
            row.classList.add('highlight');
        });
    });
});
