@extends('rapidez::layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="container mx-auto">
        <paynl-success>
            @include('rapidez::checkout.steps.success')
        </paynl-success>
    </div>
@endsection
