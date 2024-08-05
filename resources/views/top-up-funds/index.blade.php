@extends('layouts.pages.index')
@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Fund Management</h1>
    <p class="mb-4">Top Up Funds List</a>.</p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Top Up Funds List</h6>
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
                            <th>Account Name</th>
                            <th>Top Up Amount(ugx)</th>
                            <th>Top Up Amount(usd)</th>
                            <th>Description</th>
                            <th>Top Up Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($top_funds as $top_fund)
                            <tr>
                                <td>{{ $top_fund->id }}</td>
                                <td>{{ $top_fund->name }}</td>
                                <td>{{ number_format($top_fund->amount_ugx) }}/=</td>
                                <td>${{ number_format($top_fund->amount_usd, 2) }}</td>
                                <td>{{ $top_fund->description }}</td>
                                <td>{{ $top_fund->top_up_date }}</td>
                                <td>
                                    <div style="display: flex; gap: 5px;">
                                        <form method="POST" action="{{ route('delete-top-up') }}">
                                            @csrf
                                            <input type="hidden" name="top_up_id" value="{{ $top_fund->id }}">
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
                        {{-- Drop down with accounts(to select one) --}}
                        <div class="form-group">
                            <ol>
                            <label for="account"><li>Account</li></label>
                            <select class="form-control shadow-none @error('account_id') is-invalid @enderror" id="account"
                                name="account_id">
                                <option value="">Select an Account</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                @endforeach
                            </select>
                            <small id="emailHelp" class="form-text text-muted">This should be the account you want to top up
                                funds</small>
                            @error('account_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="account_type"><li>Account Type</li></label>
                                <div class="btn-group-custom d-flex justify-content-between">
                                    <input type="radio" class="btn-check" name="account_type" id="ugx" autocomplete="off" value="ugx" checked>
                                    <label class="btn btn-outline-primary px-5" for="ugx">ugx</label>

                                    <input type="radio" class="btn-check" name="account_type" id="usd" autocomplete="off" value="usd">
                                    <label class="btn btn-outline-primary px-5" for="usd">usd</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="amount"><li>Amount</li></label>
                                <input type="text" class="form-control @error('amount') is-invalid @enderror"
                                    id="amount" name="amount" placeholder="Enter the Top up Amount"
                                    value="{{ old('amount') }}">
                                <small id="emailHelp" class="form-text text-muted">This should be the amount you want to top
                                    up
                                    in your records</small>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="desc"><li>Description</li></label>
                                <textarea class="form-control shadow-none @error('description') is-invalid @enderror" id="desc" rows="3"
                                    name="description">{{ old('description') }}</textarea>
                                <small id="description" class="form-text text-muted">This should be a simple description of
                                    the
                                    fund you are topping up</small>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="topUpDate"><li>Top Up Date</li></label>
                                <input type="date" class="form-control shadow-none @error('top_up_date') is-invalid @enderror"
                                    id="topUpDate" name="top_up_date" value="{{ old('top_up_date') }}">
                                @error('top_up_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            </ul>
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

            // Set the default value of the top up date to today
            var today = new Date().toISOString().split('T')[0];
            $('#topUpDate').val(today);
        });
    </script>
@endpush
