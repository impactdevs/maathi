@extends('layouts.pages.index')
@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Beneficiaries Management</h1>
    <p class="mb-4">Beneficiaries</a>.</p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Beneficiaries List</h6>
            <!-- Button trigger modal -->
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                Add
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($beneficiaries as $beneficiary)
                            {{-- if the beneficiary has account_type --}}
                            <tr class="@if($beneficiary->beneficiary_type=='cash_out') table-info @endif">
                                <td>{{ $beneficiary->id }}</td>
                                <td>{{ $beneficiary->name }}</td>
                                <td>
                                    <div style="display: flex; gap: 5px;">
                                        <button type="button" class="btn btn-primary btn-sm edit-beneficiary" data-toggle="modal"
                                            data-target="#edit-beneficiary" data-id="{{ $beneficiary->id }}"
                                            data-name="{{ $beneficiary->name }}"
                                            data-account-type="{{ $beneficiary->beneficiary_type }}">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('delete-beneficiary') }}">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $beneficiary->id }}">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="event.preventDefault(); this.closest('form').submit();">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Beneficiary</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/add-beneficiary" method="POST">
                    <div class="modal-body">
                        @method('POST')
                        @csrf
                        <ol>
                            <div class="form-group">
                                <label for="amount">
                                    <li>Full Name</li>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" placeholder="Enter the Beneficiary Full Name"
                                    value="{{ old('name') }}">
                                <small id="name" class="form-text text-muted">This should be the full name of a
                                    beneficiary</small>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="account_type">
                                    <li>Beneficiary Type</li>
                                </label>
                                <div class="btn-group-custom d-flex justify-content-between">
                                    <input type="radio" class="btn-check" name="account_type" id="add-client"
                                        autocomplete="off" value="client" checked>
                                    <label class="btn btn-outline-primary px-5" for="add-client">Client</label>

                                    <input type="radio" class="btn-check" name="account_type" id="add-cash_out"
                                        autocomplete="off" value="cash_out">
                                    <label class="btn btn-outline-primary px-5" for="add-cash_out">Cash Out</label>
                                </div>
                            </div>
                        </ol>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- edit --}}
    <!-- Modal -->
    <div class="modal fade" id="edit-beneficiary" tabindex="-1" role="dialog" aria-labelledby="edit-beneficiary"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Beneficiary</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/edit-beneficiary" method="POST" id="edit-beneficiary-form">
                    <div class="modal-body">
                        @method('POST')
                        @csrf
                        <div class="form-group">
                            {{-- hidden beneficiary id --}}
                            <input type="hidden" name="id" id="beneficiary_id">
                            <label for="amount">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" placeholder="Enter the Beneficiary Full Name" value="{{ old('name') }}">
                            <small id="name" class="form-text text-muted">This should be the full name of a
                                beneficiary</small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        {{-- account type --}}
                        <div class="form-group">
                            <label for="account_type">
                                <li>Account Type</li>
                            </label>
                            <div class="btn-group-custom d-flex justify-content-between">
                                <input type="radio" class="btn-check" name="account_type" id="edit-client"
                                    autocomplete="off" value="client" checked>
                                <label class="btn btn-outline-primary px-5" for="edit-client">Client</label>

                                <input type="radio" class="btn-check" name="account_type" id="edit-cash-out"
                                    autocomplete="off" value="cash_out">
                                <label class="btn btn-outline-primary px-5" for="edit-cash-out">Cash Out</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Page level plugins -->
    <script src="{{ asset('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="{{ asset('assets/js/demo/datatables-demo.js') }}"></script>

    <script>
        $(document).ready(function() {
            @if ($errors->any())
                $('#exampleModal').modal('show');
            @endif

            $('.edit-beneficiary').click(function() {
                // set the values to edit form
                //find the input field
                var beneficiary_id = $(this).data('id');
                var beneficiary_name = $(this).data('name');
                var account_type = $(this).data('account-type');

                $('#edit-beneficiary-form').find('input[name="id"]').val(beneficiary_id);
                $('#edit-beneficiary-form').find('input[name="name"]').val(beneficiary_name);
                // account type, its a radio button, so check the one that is selected
                $('#edit-beneficiary-form').find('input[name="account_type"]').each(function() {
                    if ($(this).val() == account_type) {
                        $(this).prop('checked', true);
                    }
                });
            });

        });
    </script>
@endpush
