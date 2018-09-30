@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
      
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    <router-view></router-view>

        </div>
    </div>
</div>
@endsection
