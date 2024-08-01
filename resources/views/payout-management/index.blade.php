@extends('layouts.pages.index')
@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Payout Management</h1>
    <p class="mb-4">A list of all Payouts Made to beneficiaries</a>.</p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Payout List</h6>

        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Payout Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payouts as $payout)
                            <tr>
                                <td>{{ $payout->id }}</td>
                                <td>{{ $payout->name }}</td>
                                <td>{{ $payout->amount }}</td>
                                <td>{{ $payout->description }}</td>
                                <td>{{ $payout->created_at }}</td>
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
                <form action="/add-fund" method="POST">
                    <div class="modal-body">
                        @method('POST')
                        @csrf
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="text" class="form-control @error('amount') is-invalid @enderror" id="amount"
                                name="amount" placeholder="Enter the Top up Amount" value="{{ old('amount') }}">
                            <small id="emailHelp" class="form-text text-muted">This should be the amount you want to top up
                                in your records</small>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="desc">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="desc" rows="3"
                                name="description">{{ old('description') }}</textarea>
                            <small id="description" class="form-text text-muted">This should be a simple description of the
                                fund you are topping up</small>
                            @error('description')
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

    <!-- Page level custom scripts -->
    <script src="{{ asset('assets/js/demo/datatables-demo.js') }}"></script>

    <script>
        $(document).ready(function() {
            @if ($errors->any())
                $('#exampleModal').modal('show');
            @endif
        });
    </script>
@endpush
