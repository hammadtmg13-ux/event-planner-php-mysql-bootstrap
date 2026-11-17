// EventHub - small front-end helpers

function confirmDelete() {
    return confirm('Are you sure you want to delete this event?');
}

document.addEventListener('DOMContentLoaded', function () {
    // Default the date picker to today if it is empty
    var dateInput = document.querySelector('input[name="event_date"]');
    if (dateInput && !dateInput.value) {
        dateInput.value = new Date().toISOString().split('T')[0];
    }
});
