<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employee') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
               
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Update Employee Details</h2>
                </div> 
                <div class="d-flex justify-content-center align-items-center vh-100">
                    <div class="card" style="width: 45rem; ">
                        <div class="card-body">
                            <form action="{{ route('employees.update', $employee->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $employee->first_name }}">
                                </div>
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $employee->last_name }}">
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address" value="{{ $employee->address }}">
                                </div>
                                <div class="mb-3">
                                    <label for="Position" class="form-label">Position</label>
                                    <input type="text" class="form-control" id="Position" name="position" value="{{ $employee->position }}">
                                </div>
                                <button type="submit" class="btn btn-outline-primary">Update</button>
                            </form>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>