@extends('layouts.app')

@section('content')
   
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
               {{$dataTable->table()}}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{$dataTable->scripts()}}
@endpush