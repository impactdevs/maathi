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
                            <th>Beneficiary Name</th>
                            <th>Disbursed From:</th>
                            <th>Amount(ugx)</th>
                            <th>Amount(usd)</th>
                            <th>Reason</th>
                            <th>Disbursement Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payouts as $payout)
                            <tr>
                                <td>{{ $payout->id }}</td>
                                <td>{{ $payout->name }}</td>
                                <td>{{ $payout->account_name }}</td>
                                <td>{{ number_format($payout->amount_ugx) }}</td>
                                <td>{{ number_format($payout->amount_usd) }}</td>
                                <td>{{ $payout->description }}</td>
                                <td>{{ $payout->disbursement_date }}</td>
                                <td>
                                    <div style="display: flex; gap: 5px;">
                                        <form method="POST" action="{{ route('delete-payout') }}">
                                            @csrf
                                            <input type="hidden" name="disbursement_id" value="{{ $payout->id }}">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="event.preventDefault(); this.closest('form').submit();">Revert</button>
                                        </form>
                                    </div>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
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
@endpush
