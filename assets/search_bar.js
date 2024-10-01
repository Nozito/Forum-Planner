document.getElementById('searchInput').addEventListener('keyup', function() {
    var searchValue = this.value.toLowerCase();
    var rows = document.querySelectorAll('#userTableBody tr');
    rows.forEach(function(row) {
        var email = row.cells[0].textContent.toLowerCase();
        var firstName = row.cells[1].textContent.toLowerCase();
        var lastName = row.cells[2].textContent.toLowerCase();
        if (email.includes(searchValue) || firstName.includes(searchValue) || lastName.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});