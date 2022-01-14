@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">

                    {{ __('Dodaj nowych użytkowników poprzez zaimportowanie ich z pliku csv') }}
                    <form action="{{ route('users.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(Session::has('error'))
                            <div class="alert alert-danger">
                                {{ Session::get('error')}}
                            </div>
                        @endif
                        <div class="form-group" style="margin: 20px 0 20px 0;">
                            <input accept=".csv"  type="file" name="upload-file" class="form-control">
                        </div>
                        <input  class="btn btn-success" type="submit" value="Importuj" name="submit">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
