@extends('laradmin::user.sub_layouts.settings')
@section('sub-content')
@include('laradmin::user.billing.partials.top')

<div class="panel panel-default">
    <div class="panel-heading">You have {{count($invoices)}} invoices</div>
    <div class="table-responsive">
        <table class="table table-striped">
            @foreach ($invoices as $invoice)
                <tr>
                    <td><small> {{ $invoice->date()->toFormattedDateString() }}</small></td>
                    <td><small>{{ $invoice->total() }}</small></td>
                    <td><a href="{{route('user-billing-invoice', $invoice->id)}}"><small><i class="fas fa-download"></i> Download</small></a></td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection
