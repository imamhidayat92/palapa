@extends('layouts.full.full')

@section('body-class')
login
@stop

@section('breadcrumb-title')
    Login
@stop

@section('content')

    <div class="container-fluid" style="margin-top: 50px">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default pad-lg well">
                {{ BootForm::open(['action' => route('gapura.login')]) }}
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                {{ BootForm::text('Email', 'email') }}
                {{ BootForm::password('Password', 'password') }}
                {{ BootForm::submit('Login', 'btn-primary btn-block') }}
                {{--<a href="{{ route('gapura.register') }}">Register</a>--}}
                {{ BootForm::close() }}
            </div>
        </div>
    </div>

@stop