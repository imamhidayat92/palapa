@extends('layouts.admin.admin')

@section('breadcrumb')
    @parent
    <span class="trail"><i class="fa fa-angle-right"></i></span>
    <span class="trail">Konfigurasi</span>
@stop

@section('content-admin')
    <h2 class="page-title">Konfigurasi</h2>

    {{ BootForm::openHorizontal(3,9)->action(route('setting.store')) }}
        <input type="hidden" name="_token" value="{{ csrf_token() }}"/>

        <fieldset>
            <h4>Kepala Kejaksaan Negeri</h4>
            <hr/>
            {{ BootForm::text('Nama', 'kajari_name')->value(array_get($setting, 'kajari_name', '')) }}
            {{ BootForm::text('NIP', 'kajari_nip')->value(array_get($setting, 'kajari_nip', '')) }}
            {{ BootForm::text('Jabatan', 'kajari_jabatan')->value(array_get($setting, 'kajari_jabatan', '')) }}
        </fieldset>

        <h4>Lainnya</h4>
        <hr />
        <fieldset>
            {{ BootForm::text('Kepala Kejaksaan Jawa Timur', 'kepala_kejaksaan_provinsi')->value(array_get($setting, 'kepala_kejaksaan_provinsi', '')) }}
            {{ BootForm::text('Kepala Kepolisian', 'kepala_kepolisian')->value(array_get($setting, 'kepala_kepolisian', '')) }}
        </fieldset>
        {{ BootForm::submit('Simpan', 'btn-primary') }}
    {{ BootForm::close() }}
@stop

