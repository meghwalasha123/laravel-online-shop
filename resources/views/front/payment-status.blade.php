@extends('front.layouts.app')

@section('content')
    <section class="container">
        <div class="col-md-12 text-center py-5">
            @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
            <h1 class="mt-2">Thank You!</h1>
            <p>Your Order Id is : {{ $id }}</p>
            @endif         

            @if (Session::has('error'))
            <div class="alert alert-danger">
                {{ Session::get('error') }}
            </div>
            @endif 
        </div>
    </section>
@endsection