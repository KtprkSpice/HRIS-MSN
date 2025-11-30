@extends('layout.dashboard')

@section('header', 'Scan QR')

@section('content')
    <p>{{ $presences->name }}</p>
    <p>{{ $presences->description }}</p>
    <p>{{ $presences->status }}</p>
@endsection