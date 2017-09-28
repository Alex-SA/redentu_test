@extends('layouts.app')

@section('content')
    <div class="flex-center position-ref full-height" id="app">

        <div class="content">

            <upload csrf="{{ csrf_token() }}" action="/upload" filetypes="image/*"></upload>

            <image-list url="/list"></image-list>

        </div>
    </div>

@endsection
