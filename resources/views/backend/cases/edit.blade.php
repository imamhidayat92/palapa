@extends('layouts.admin.admin')

@section('content-admin')
    {{ BootForm::open()->put()->action(route('backend.cases.update', [$case->id])) }}
    <input type="hidden" name="_token" value="{{ csrf_token() }}"/>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>Edit Kasus <span class="label label-info">{{ $type['name'] }}</span></h4>
        </div>
        <div class="panel-body">
            @include('backend.cases.edit.' . $type['id'])
        </div>
        <div class="panel-footer text-right">
            {{ BootForm::submit('Simpan', 'btn-primary') }}
        </div>
    </div>


    {{ BootForm::close() }}

@stop
