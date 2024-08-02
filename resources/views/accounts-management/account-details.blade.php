@extends('layouts.pages.index')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">{{$account->name}}'s Details({{ number_format($account->balance)}}/=)</h1>

<!-- Disbursements Section -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Disbursements</h6>
    </div>
    <div class="card-body">
        @if($account->disbursements->isEmpty())
            <p class="text-muted">No disbursements available.</p>
        @else
            <div class="list-group">
                @foreach ($account->disbursements as $disbursement)
                    <div class="list-group-item">
                        <h5 class="mb-1">Amount: {{ $disbursement->amount }}</h5>
                        <p class="mb-1">Description: {{ $disbursement->description }}</p>
                        <small>Date: {{ $disbursement->disbursement_date }}</small>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Top Ups Section -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Top Ups</h6>
    </div>
    <div class="card-body">
        @if($account->top_ups->isEmpty())
            <p class="text-muted">No top-ups available.</p>
        @else
            <div class="list-group">
                @foreach ($account->top_ups as $top_up)
                    <div class="list-group-item">
                        <h5 class="mb-1">Amount: {{ $top_up->amount }}</h5>
                        <p class="mb-1">Description: {{ $top_up->description }}</p>
                        <small>Date: {{ $top_up->top_up_date }}</small>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection
