@extends('layouts.pages.index')
@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Disbursement</h1>
    <p class="mb-4">Select the beneficiaries you want to assign funds.</p>

    <!-- DataTables Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Disbursed funds (The current balance is: UGx: <span
                    id="totalFunds">{{ $total_funds }}</span>)</h6>
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="lstBox1">Available Beneficiaries</label>
                        <select multiple="multiple" id="lstBox1" class="form-control form-select-lg">
                            @foreach ($beneficiaries as $beneficiary)
                                <option value="{{ $beneficiary->id }}">{{ $beneficiary->id . '.' . $beneficiary->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-2 d-flex flex-column align-items-center justify-content-center">
                    <button type="button" id="btnAllRight" class="btn btn-primary mb-2">&gt;&gt;</button>
                    <button type="button" id="btnRight" class="btn btn-primary mb-2">&gt;</button>
                    <button type="button" id="btnLeft" class="btn btn-primary mb-2">&lt;</button>
                    <button type="button" id="btnAllLeft" class="btn btn-primary">&lt;&lt;</button>
                </div>

                <div class="col-md-5">
                    <div class="form-group">
                        <label for="lstBox2">Selected Beneficiaries</label>
                        <select multiple="multiple" id="lstBox2" class="form-control"></select>
                    </div>
                    <div class="form-group" id="amountSection" style="display: none;">
                        <label for="fundsAmount">Enter Amount for Each Selected Beneficiary:</label>
                        <input type="text" id="fundsAmount" class="form-control mb-2" placeholder="Amount">
                        <label for="reason">Reason for Disbursement:</label>
                        <input type="text" id="reason" class="form-control mb-2" placeholder="Reason">
                        <button type="button" id="btnAddFunds" class="btn btn-success">Add Fund(s)</button>
                    </div>
                    <div class="form-group" id="saveSection" style="margin-top: 20px;">
                        <button type="button" id="btnSave" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <style>
        select {
            height: 50vh !important;
        }

        .btn {
            width: 100%;
        }
    </style>
    <script>
        $(document).ready(function() {
            function moveOptions(from, to) {
                var selectedOpts = $(from + ' option:selected');
                if (selectedOpts.length == 0) {
                    alert("Nothing to move.");
                }
                $(to).append($(selectedOpts).clone());
                $(selectedOpts).remove();
                checkSelectedUsers();
            }

            function moveAllOptions(from, to) {
                var selectedOpts = $(from + ' option');
                if (selectedOpts.length == 0) {
                    alert("Nothing to move.");
                }
                $(to).append($(selectedOpts).clone());
                $(selectedOpts).remove();
                checkSelectedUsers();
            }

            function checkSelectedUsers() {
                if ($('#lstBox2 option:selected').length > 0) {
                    $('#amountSection').show();
                } else {
                    $('#amountSection').hide();
                }
            }

            function updateTotalFunds(amount, add = true) {
                var totalFunds = parseFloat($('#totalFunds').text().replace('UGx: ', ''));
                if (add) {
                    totalFunds -= parseFloat(amount);
                } else {
                    totalFunds += parseFloat(amount);
                }
                $('#totalFunds').text('UGx: ' + totalFunds.toFixed(2));
            }

            function removeAllocatedFundsText() {
                $('#lstBox1 option').each(function() {
                    var text = $(this).text();
                    // Remove the " - UGx: [amount]" part if it exists
                    var newText = text.replace(/ - UGx: \d+(\.\d{1,2})?/, '').trim();
                    $(this).text(newText);
                    $(this).removeData('amount');
                });
            }

            $('#btnRight').click(function(e) {
                e.preventDefault();
                moveOptions('#lstBox1', '#lstBox2');
            });

            $('#btnAllRight').click(function(e) {
                e.preventDefault();
                moveAllOptions('#lstBox1', '#lstBox2');
            });

            $('#btnLeft').click(function(e) {
                e.preventDefault();
                var selectedOpts = $('#lstBox2 option:selected');
                if (selectedOpts.length == 0) {
                    alert("Nothing to move.");
                } else {
                    selectedOpts.each(function() {
                        var amount = $(this).data('amount');
                        updateTotalFunds(amount, false);
                        $(this).remove();
                        $('#lstBox1').append($(this).clone().text($(this).text().replace(
                            / - UGx: \d+(\.\d{1,2})?/, '').trim()));
                    });
                    checkSelectedUsers();
                }
            });

            $('#btnAllLeft').click(function(e) {
                e.preventDefault();
                var allOpts = $('#lstBox2 option');
                if (allOpts.length == 0) {
                    alert("Nothing to move.");
                } else {
                    allOpts.each(function() {
                        var amount = $(this).data('amount');
                        updateTotalFunds(amount, false);
                        $(this).remove();
                        $('#lstBox1').append($(this).clone().text($(this).text().replace(
                            / - UGx: \d+(\.\d{1,2})?/, '').trim()));
                    });
                    checkSelectedUsers();
                }
            });

            $('#btnAddFunds').click(function(e) {
                e.preventDefault();
                if (!checkTotalAmount()) {
                    return;
                }
                var amount = $('#fundsAmount').val();
                var reason = $('#reason').val();
                if (isNaN(amount) || amount <= 0) {
                    alert("Please enter a valid amount.");
                    return;
                }

                $('#lstBox2 option:selected').each(function() {
                    var optionText = $(this).text();
                    var currentAmount = $(this).data('amount');
                    if (currentAmount) {
                        $(this).text(optionText.replace(/UGx: \d+(\.\d{1,2})?/, 'UGx: ' + amount));
                    } else {
                        $(this).text(optionText + ' - UGx: ' + amount + ' (Reason: ' + reason + ')');
                    }
                    $(this).data('amount', amount);
                    $(this).data('reason', reason);
                });

                updateTotalFunds(amount); // Reduce the total funds by the added amount

                alert("Funds have been added to selected beneficiaries.");
                $('#fundsAmount').val('');
                $('#reason').val('');
                $('#amountSection').hide();
            });

            $('#btnSave').click(function(e) {
                e.preventDefault();
                if (!checkAmount()) {
                    return;
                }

                var beneficiaries = [];
                $('#lstBox2 option').each(function() {
                    beneficiaries.push({
                        value: $(this).val(),
                        amount: $(this).data('amount'),
                        reason: $(this).data('reason')
                    });
                });

                console.log("Saving the following beneficiaries:", beneficiaries);

                //disburse funds
                $.ajax({
                    url: "{{ route('disburse-funds') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        beneficiaries: beneficiaries
                    },
                    success: function(response) {
                        console.log("Response:", response);
                    },

                    error: function(xhr, status, error) {
                        console.error("Error:", error);
                    }
                });
                // After saving, clear listbox2 and reset total funds
                var totalAmount = 0;
                $('#lstBox2 option').each(function() {
                    totalAmount += parseFloat($(this).data('amount')) || 0;
                });
                updateTotalFunds(totalAmount, false); // Add back the total funds of saved allocations

                alert("Data saved successfully.");

                $('#lstBox2').empty();
                $('#amountSection').hide();
            });

            function checkAmount() {
                var noAmount = false;
                $('#lstBox2 option').each(function() {
                    if (!$(this).data('amount')) {
                        noAmount = true;
                        return false;
                    }
                });

                if (noAmount) {
                    alert("Please add funds to all selected beneficiaries.");
                    return false;
                }

                return true;
            }

            function checkTotalAmount() {
                var totalAmount = 0;
                $('#lstBox2 option').each(function() {
                    var amount = $(this).data('amount');
                    if (amount) {
                        totalAmount += parseFloat(amount);
                    }
                });

                var fundsAmount = $('#fundsAmount').val();

                if (fundsAmount) {
                    totalAmount += parseFloat(fundsAmount);
                }

                if (totalAmount > parseFloat($('#totalFunds').text().replace('UGx: ', ''))) {
                    alert("The total amount being allocated is greater than the total funds.");
                    return false;
                }

                return true;
            }

            $('#lstBox2').change(checkSelectedUsers);
        });
    </script>
@endpush
