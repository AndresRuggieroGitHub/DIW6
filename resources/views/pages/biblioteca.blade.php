@extends('layouts.site', ['title' => 'Lexi | Biblioteca', 'description' => 'Biblioteca de vocabulario y búsqueda de términos en Lexi.', 'activeNav' => 'library', 'showFooter' => false])

@section('content')
@include('partials.library-content')
@endsection