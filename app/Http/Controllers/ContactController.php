<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Http\Requests\FileUploadRequest;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Contact::all();
        if ($data) {
            return response()->json([
                'hasError' => 'false',
                'statusCode' => 200,
                'message' => 'Contact Fetched Successfully',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'hasError' => 'true',
                'statusCode' => 404,
                'message' => 'Data Not Found.',
                'data' => null
            ], 404);
        }
    }


    /**
     * Display a listing of the resource with pagination.
     */
    public function getAllContacts(Request $request)
    {
        $length = $request->input('length', 10); // Number of records per page
        $start = $request->input('start', 0); // Offset for pagination
        $search = $request->input('search', ''); // Search value
        $orderColumnIndex = $request->input('order.0.column', 0); // Default sorting column index
        $orderDirection = $request->input('order.0.dir', 'asc'); // Sorting direction

        // Get column name for ordering
        $columns = ['first_name', 'last_name', 'phone_number', 'id']; // Define valid column names
        $orderColumn = $columns[$orderColumnIndex] ?? 'id'; // Ensure column exists

        // Start query
        $query = Contact::query();

        // Apply search if there's a search term
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('phone_number', 'like', '%' . $search . '%');
            });
        }

        // Get total count before pagination
        $totalContacts = Contact::count();
        $filteredContacts = $query->count(); // Count before applying pagination

        // Apply sorting
        $query->orderBy($orderColumn, $orderDirection);

        // Apply pagination
        $contacts = $query->skip($start)->take($length)->get();

        if ($contacts->isEmpty()) {
            return response()->json([
                'hasError' => false,
                'statusCode' => 200,
                'message' => 'No contacts found',
                'data' => []
            ], 200);
        } else {
            // Prepare response
            return response()->json([
                'hasError' => false,
                'statusCode' => 200,
                'message' => 'Contacts fetched successfully',
                'draw' => intval($request->input('draw', 1)), // Ensure draw is an integer
                'start' => $start,
                'length' => $length,
                'data' => $contacts,
                'recordsTotal' => $totalContacts, // Total records in DB
                'recordsFiltered' => $filteredContacts // Filtered records after search
            ], 200);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(ContactRequest $request)
    {
        $contact = Contact::create($request->validated());
        if ($contact) {
            return response()->json([
                'hasError' => 'false',
                'statusCode' => 201,
                'message' => 'Contact Added Successfully',
                'data' => $contact
            ], 201);
        } else {
            return response()->json([
                'hasError' => 'true',
                'statusCode' => 500,
                'message' => 'Failed To Add Contact',
                'data' => null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Contact::find($id);
        if ($data) {
            return response()->json([
                'hasError' => 'false',
                'statusCode' => 200,
                'message' => 'Contact Fetched Successfully',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'hasError' => 'true',
                'statusCode' => 404,
                'message' => 'Data Not Found.',
                'data' => null
            ], 404);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(ContactRequest $request, $id)
    {
        $contact = Contact::findOrFail($id);
        if ($contact) {
            $contact->update($request->validated());
            return response()->json([
                'hasError' => 'false',
                'statusCode' => 200,
                'message' => 'Contact Updated Successfully',
                'data' => $contact
            ], 200);
        } else {
            return response()->json([
                'hasError' => 'true',
                'statusCode' => 500,
                'message' => 'Failed To Update Contact',
                'data' => null
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Contact::findOrFail($id)->delete();
        if ($data) {
            return response()->json([
                'hasError' => 'false',
                'statusCode' => 200,
                'message' => 'Contact Deleted Successfully',
                'data' => null
            ], 200);
        } else {
            return response()->json([
                'hasError' => 'true',
                'statusCode' => 500,
                'message' => 'Something went wrong',
                'data' => null
            ], 500);
        }
    }


    public function importXML(FileUploadRequest $request)
    {
        $request->validated();
        $xmlFile = $request->file('xml_file');
        $xmlData = simplexml_load_file($xmlFile);

        if (!$xmlData || $xmlData->getName() !== 'Contacts') {
            return response()->json([
                'hasError' => true,
                'statusCode' => 400,
                'message' => 'Invalid XML file format',
                'data' => null
            ], 400);
        }

        $contactsToInsert = [];
        foreach ($xmlData->Contact as $item) {
            if (!isset($item->Name) || !isset($item->Phone)) {
                return response()->json([
                    'hasError' => true,
                    'statusCode' => 400,
                    'message' => 'Missing required fields in XML',
                    'data' => null
                ], 400);
            }

            $fullName = trim((string)$item->Name);
            $phone = preg_replace('/\s+/', '', (string)$item->Phone);

            if (empty($fullName) || empty($phone) || Contact::where('phone_number', $phone)->exists()) {
                continue; // Skip invalid entries
            }

            $nameParts = explode(' ', $fullName);
            $firstName = $nameParts[0];
            $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';

            // Collect data for batch insertion
            $contactsToInsert[] = [
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'phone_number' => $phone,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        // Insert all new contacts in a single query
        if (!empty($contactsToInsert)) {
            Contact::insert($contactsToInsert);
        }

        return response()->json([
            'hasError' => false,
            'statusCode' => 200,
            'message' => 'Contacts Imported Successfully',
            'data' => null
        ], 200);
    }
}
