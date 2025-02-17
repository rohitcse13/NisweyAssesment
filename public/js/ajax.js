function loadContacts() {
    var url = $("#contact-table").data("url");

    if ($.fn.DataTable.isDataTable("#contact-table")) {
        $("#contact-table").DataTable().destroy(); // Destroy previous instance
    }

    $("#contact-table").DataTable({
        processing: true, // Show loading indicator
        serverSide: true, // Enable server-side processing
        ajax: {
            url: url, // Fetch data from server
            type: "GET",
            data: function (d) {
                d.search = d.search.value; // Get search input value
            },
            dataSrc: function (response) {
                if (response.data.length === 0) {
                    $(".dataTables_empty").html(
                        "🔍 No matching contacts found."
                    );
                }
                return response.data;
            },
        },
        pageLength: 10, // Default records per page
        lengthMenu: [10, 25, 50], // Dropdown for records per page
        columns: [
            { data: "first_name" },
            { data: "last_name" },
            { data: "phone_number" },
            {
                data: "id",
                render: function (data, type, row) {
                    return `
                        <div class="flex items-center space-x-2">
                            <!-- Edit Button -->
                            <button class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-3 rounded edit-contact"
                                    data-id="${data}"
                                    data-first_name="${row.first_name}"
                                    data-last_name="${row.last_name}"
                                    data-phone_number="${row.phone_number}">
                                <i class="fas fa-edit"></i> <!-- Edit Icon -->
                            </button>

                            <!-- Delete Button -->
                            <button class="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded delete-contact"
                                    data-id="${data}"
                                    data-url="${window.location.origin}/delete-contact/${data}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    `;
                },
            },
        ],
        order: [[0, "asc"]], // Default sorting by first name
        language: {
            emptyTable: "Loading contacts...",
            zeroRecords: "No matching contacts found.",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            info: "Showing _START_ to _END_ of _TOTAL_ contacts",
        },
        drawCallback: function (settings) {
            const totalRecords = settings.json.recordsTotal; // Total records
            const filteredRecords = settings.json.recordsFiltered; // Filtered records
            console.log("Total:", totalRecords, "Filtered:", filteredRecords);
        },
    });
}

// AJAX Form Submission
$("#contact-form").submit(function (event) {
    var url = $(this).data("url");
    event.preventDefault();

    let formData = $(this).serialize();

    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        success: function (response) {
            $("#success-message").text(response.message).removeClass("hidden");
            $("#error-message").addClass("hidden");
            $("#contact-form")[0].reset();
            loadContacts();
        },
        error: function (xhr) {
            let errors = xhr.responseJSON.errors;
            let errorMessage =
                "<strong>Whoops! Something went wrong.</strong><ul>";
            $.each(errors, function (key, value) {
                errorMessage += `<li>- ${value}</li>`;
            });
            errorMessage += "</ul>";
            $("#error-message").html(errorMessage).removeClass("hidden");
        },
    });
});

$("#update-form").submit(function (event) {
    var url = $(this).data("url");
    event.preventDefault();
    let formData = $(this).serialize();
    let id = $("#update_contact_id").val();

    $.ajax({
        url: url.replace(":id", id),
        type: "PUT",
        data: formData,
        success: function (response) {
            $("#success-message").text(response.message).removeClass("hidden");
            $("#updateModal").addClass("hidden");
            loadContacts();
        },
        error: function (xhr, status, error) {
            let errorMessage = "Error updating data.";
            if (xhr.status === 422) {
                // Validation error
                let errors = xhr.responseJSON?.data?.xml_file || []; // Get validation error messages
                errorMessage = errors.join(", "); // Convert array to a single string
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message; // Display server response message
            }

            $("#error-message")
                .text(errorMessage)
                .removeClass("hidden");
            $("#success-message").addClass("hidden");
            $("#updateModal").addClass("hidden");
            loadContacts();
        },
    });
});

//Delete Contact
$(document).on("click", ".delete-contact", function () {
    var url = $(this).data("url");
    var id = $(this).data("id");

    if (confirm("Are you sure you want to delete this contact?")) {
        $.ajax({
            url: url,
            type: "DELETE",
            success: function (response) {
                $("#success-message").text(response.message).removeClass("hidden");
                $("#error-message").addClass("hidden");
                loadContacts();
            },
            error: function (xhr, status, error) {
                let errorMessage = "Error deleting data.";
                if (xhr.status === 422) {
                    // Validation error
                    let errors = xhr.responseJSON?.data?.xml_file || []; // Get validation error messages
                    errorMessage = errors.join(", "); // Convert array to a single string
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message; // Display server response message
                }

                $("#error-message")
                    .text(errorMessage)
                    .removeClass("hidden");
                $("#success-message").addClass("hidden");
            },
        });
    }
});

// Handle form submission using jQuery AJAX
$(document).ready(function () {
    $("#xml-upload-form").submit(function (event) {
        var url = $(this).data("url");
        event.preventDefault(); // Prevent default form submission

        let formData = new FormData(this);

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $("#upload-success-message")
                    .text(response.message)
                    .removeClass("hidden");
                $("#upload-error-message").addClass("hidden");
                $("#xml-upload-form")[0].reset();
                $("#file-name").text("");
            },
            error: function (xhr, status, error) {
                let errorMessage = "Failed to upload XML. Please try again.";

                if (xhr.status === 422) {
                    // Validation error
                    let errors = xhr.responseJSON?.data?.xml_file || []; // Get validation error messages
                    errorMessage = errors.join(", "); // Convert array to a single string
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message; // Display server response message
                }

                $("#upload-error-message")
                    .text(errorMessage)
                    .removeClass("hidden");
                $("#upload-success-message").addClass("hidden");
            },
        });
    });
});
