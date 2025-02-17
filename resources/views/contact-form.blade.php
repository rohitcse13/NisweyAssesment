<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form & XML Upload</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">

    <!-- jQuery (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">


</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100">

    <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-lg">

        <!-- Title -->
        <h2 class="text-2xl font-semibold text-center text-gray-700">Contact Form & XML Upload</h2>

        <!-- Success and Error Messages -->
        <div id="success-message" class="hidden mb-4 p-4 bg-green-100 text-green-700 rounded-lg"></div>
        <div id="error-message" class="hidden mb-4 p-4 bg-red-100 text-red-700 rounded-lg"></div>

        <!-- Contact Form -->
        <form id="contact-form" data-url="{{ route('submit.form') }}" class="mt-4">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-600 text-sm font-medium">First Name</label>
                <input type="text" name="first_name" placeholder="John" required
                    class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="mb-4">
                <label class="block text-gray-600 text-sm font-medium">Last Name</label>
                <input type="text" name="last_name" placeholder="Doe"
                    class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="mb-4">
                <label class="block text-gray-600 text-sm font-medium">Phone Number</label>
                <input type="tel" name="phone_number" pattern="[+]{1}[0-9]{2}[0-9]{10}" placeholder="+917879434540" required
                    class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded-lg mt-2">
                Submit Form
            </button>
        </form>

        <!-- Separator -->
        <div class="text-center my-4 text-gray-600">OR</div>

        <!-- XML Upload Form -->
        <form id="xml-upload-form" data-url="{{ route('upload.xml') }}" enctype="multipart/form-data">
            @csrf
            <input type="file" id="xml_file" name="xml_file" accept=".xml" required class="hidden" onchange="updateFileName()">
            <div class="mb-4">
                <button type="button" onclick="document.getElementById('xml_file').click()"
                    class="w-full bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 rounded-lg">
                    Select XML File
                </button>
                <p id="file-name" class="text-gray-600 mt-2 text-sm text-center"></p>
            </div>

            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg">
                Upload XML File
            </button>
            <p id="upload-success-message" class="hidden text-green-600 mt-2 text-center"></p>
            <p id="upload-error-message" class="hidden text-red-600 mt-2 text-center"></p>
        </form>
    </div>

    <!-- Contacts Table with DataTable -->
    <div class="w-full max-w-2xl bg-white p-6 rounded-lg shadow-lg mt-6">
        <h2 class="text-2xl font-semibold text-center text-gray-700">Contact List</h2>
        <table id="contact-table" data-url="{{ route('contacts.view') }}" class="display w-full mt-4 border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">First Name</th>
                    <th class="border p-2">Last Name</th>
                    <th class="border p-2">Phone Number</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be dynamically loaded here -->
            </tbody>
        </table>
    </div>


    <!-- Update Contact Modal -->
    <div id="updateModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-xl font-semibold text-gray-700">Update Contact</h2>

            <form id="update-form" data-url="{{ route('update.contact', ":id") }}" class="mt-4">
                @csrf
                <input type="hidden" id="update_contact_id">
                <div class="mb-4">
                    <label class="block text-gray-600 text-sm font-medium">First Name</label>
                    <input type="text" id="update_first_name" name="first_name"
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-600 text-sm font-medium">Last Name</label>
                    <input type="text" id="update_last_name" name="last_name"
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-600 text-sm font-medium">Phone Number</label>
                    <input type="tel" id="update_phone_number" name="phone_number"
                        class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <div class="flex justify-between">
                    <button type="button" id="closeModal"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg">
                        Cancel
                    </button>
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/ajax.js') }}"></script>

</body>

</html>
