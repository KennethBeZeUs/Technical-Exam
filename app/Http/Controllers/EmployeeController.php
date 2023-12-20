<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return view('employees.index', compact('employees'));
    }

    // Create a new employee
    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'position' => 'required|string|max:255',
        ]);
    
        // Create and store the employee in the database
        $employee = Employee::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'address' => $request->input('address'),
            'position' => $request->input('position'),
        ]);
    
        // Redirect or perform any other action after storing the employee
        return redirect()->route('employees.index')->with('success', 'Employee created successfully');
    }

    // // Display the specified employee
    // public function show($id)
    // {
    //     $employee = Employee::findOrFail($id);
    //     return view('employees.show', compact('employee'));
    // }

    // // Update the specified employee
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'position' => 'required|string|max:255',
        ]);
    
        // Find the employee
        $employee = Employee::findOrFail($id);
    
        // Update the employee with the validated data
        $employee->update([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'address' => $request->input('address'),
            'position' => $request->input('position'),
        ]);
    
        // Optionally, you can return a response or redirect
        return redirect()->route('employees.index')->with('success', 'Employee updated successfully');
    }

    // // Remove the specified employee
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();
        return redirect()->route('employees.index');
    }


     // API methods
     public function apiIndex()
     {
        $employees = Employee::all();
        $data = $employees->toArray();
        $xml = $this->arrayToXml($data);

        // Return XML response
        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
     }

     public function apiStore(Request $request)
     {
         try {

             // Validate the XML data
             $validatedData = $this->validateXml($request->getContent());

             // Log the validated data
             Log::info('Validated data:', ['data' => $validatedData]);

             // Create and save the employee
             $employee = Employee::create($validatedData);

             return response()->json($employee, 201);
         } catch (\Exception $e) {
             // Log any exceptions that may occur
             Log::error('Error saving employee:', ['error' => $e->getMessage()]);

             return response()->json(['error' => 'Internal Server Error'], 500);
         }
     }
 
     public function apiUpdate(Request $request, $id)
     {
        try{
            $validatedData = $this->validateXml($request->getContent());

            // Find the employee by ID
            $employee = Employee::find($id);
    
            if (!$employee) {
                return response()->json(['error' => 'Employee not found'], 404);
            }
    
            $employee->update($validatedData);
    
            return response()->json(['message' => 'Employee updated successfully'], 200);
        }catch (\Exception $e){
            Log::error('Error updating employee:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
        
     }
     public function apiDestroy($id)
     {
         $employee = Employee::findOrFail($id);
         $employee->delete();
 
         return response()->json(['message' => 'Employee deleted successfully'], 200)
         ->header('Content-Type', 'application/xml');
     }

     private function arrayToXml($array, $rootElement = null, $xml = null)
        {
            $_xml = $xml;

            // If no root element is passed, create root element
            if ($_xml === null) {
                $_xml = new \SimpleXMLElement($rootElement !== null ? $rootElement : '<root/>');
            }

            // Loop through array and add elements to XML
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    // Recursively call the function for nested arrays
                    $this->arrayToXml($v, $k, $_xml->addChild($k));
                } else {
                    // Add the key-value pair to the XML
                    $_xml->addChild($k, $v);
                }
            }

            return $_xml->asXML();
        }

        protected function validateXml($xml)
        {
            $data = [];

            try {
                $xmlObject = simplexml_load_string($xml);

                if ($xmlObject !== false) {
                    $data = json_decode(json_encode($xmlObject), true);

                    // Extract the values you need
                    $data = [
                        'first_name' => $data['first_name'] ?? null,
                        'last_name' => $data['last_name'] ?? null,
                        'address' => $data['address'] ?? null,
                        'position' => $data['position'] ?? null,
                    ];
                } else {
                    // Log an error if XML parsing fails
                    Log::error('Error parsing XML:', ['error' => 'Invalid XML format']);
                }
            } catch (\Exception $e) {
                // Log an error if an exception occurs during XML parsing
                Log::error('Error parsing XML:', ['error' => $e->getMessage()]);
            }

            return $data;
        }
}
