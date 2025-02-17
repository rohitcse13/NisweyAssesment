// Load contacts when the page loads
$(document).ready(function() {
    loadContacts();
});

//
function updateFileName() {
    let fileInput = document.getElementById('xml_file');
    let fileNameDisplay = document.getElementById('file-name');

    if (fileInput.files.length > 0) {
        fileNameDisplay.textContent = fileInput.files[0].name;
    } else {
        fileNameDisplay.textContent = "";
    }
}

//Open Update Modal
$(document).on('click', '.edit-contact', function () {
    $('#update_contact_id').val($(this).data('id'));
    $('#update_first_name').val($(this).data('first_name'));
    $('#update_last_name').val($(this).data('last_name'));
    $('#update_phone_number').val($(this).data('phone_number'));
    $('#updateModal').removeClass('hidden');
});

//Close Update Modal
$('#closeModal').click(function () {
    $('#updateModal').addClass('hidden');
});

// 🔍 Trigger search on keyup
$('#custom-search').on('keyup', function() {
    loadContacts(); // Reload table with new search input
});
