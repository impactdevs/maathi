@extends('layouts.pages.index')
@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Accounts Management</h1>
    <p class="mb-4">Accounts List</a>.</p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Accounts List</h6>
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
                            <th>Balance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($accounts as $account)
                            <tr>
                                <td>{{ $account->id }}</td>
                                <td><a href="{{ route('account-details', $account->id) }}">{{ $account->name }}</a></td>
                                <td>{{ number_format($account->balance ?? 0) }}/=</td>
                                <td>
                                    <div style="display: flex; gap: 5px;">
                                        <button type="button" class="btn btn-primary edit-account" data-toggle="modal" data-target="#edit-account" data-id="{{ $account->id }}" data-name="{{ $account->name }}">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('delete-account') }}">
                                            @csrf
                                            <input type="hidden" name="account_id" value="{{ $account->id }}">
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
                    <h5 class="modal-title" id="exampleModalLabel">Top up Funds</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/add-account" method="POST">
                    <div class="modal-body">
                        @method('POST')
                        @csrf
                        <div class="form-group">
                            <label for="amount">Account Name</label>
                            <input type="text" class="form-control @error('account_name') is-invalid @enderror"
                                id="amount" name="account_name" placeholder="Enter the account name"
                                value="{{ old('amount') }}">
                            <small id="emailHelp" class="form-text text-muted">This should be the name of the account you
                                want to add</small>
                            @error('account_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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

    {{-- edit  --}}
    <!-- Modal -->
    <div class="modal fade" id="edit-account" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Top up Funds</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/edit-account" method="POST" id="edit-account-form">
                    <div class="modal-body">
                        @method('POST')
                        @csrf
                        <div class="form-group">
                            {{-- hidden account_id --}}
                            <input type="hidden" name="account_id" id="account_id">
                            <label for="amount">Account Name</label>
                            <input type="text" class="form-control @error('account_name') is-invalid @enderror"
                                id="amount" name="account_name" placeholder="Enter the account name"
                                value="{{ old('amount') }}">
                            <small id="emailHelp" class="form-text text-muted">This should be the name of the account you
                                want to add</small>
                            @error('account_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
        //   on click of edit button, fill the form with the data of the row
    $(document).on('click', '.edit-account', function(){
        console.log('clicked');
        var id = $(this).data('id');
        var name = $(this).data('name');
        console.log(id, name);

        //set the id and name in the form
        $('#edit-account-form').find('input[name="account_name"]').val(name);

        $('#edit-account-form').find('input[name="account_id"]').val(id);
    });
        </script>
@endpush
