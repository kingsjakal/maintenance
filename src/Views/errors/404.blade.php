@extends('maintenance::layouts.public')

@section('title', '404 - Not Found')

@section('content')

    <div class="login-box">

        <div class="panel panel-danger">
            <div class="panel-heading text-center">
                @yield('title')
            </div>
            <div class="panel-body">
                <p class="text-center">
                    The page you tried to visit does not exist.
                </p>

                <a class="btn btn-primary btn-block" href="{{ URL::previous() }}">
                    <i class="fa fa-reply"></i> Go Back
                </a>
            </div>
        </div>

    </div>

@stop
