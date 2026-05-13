@extends('layouts.mail')
@section('subject')
    {{$subject}}
@endsection 
@section('intro')
{{$intro}}
@endsection
@section('content')
{!! $content !!}
@endsection
@section('outro')
    {{$outro}}
@endsection

