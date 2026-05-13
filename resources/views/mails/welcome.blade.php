@extends('layouts.mail')
@section('subject')
    {{$subject ?? 'Account Setup'}}
@endsection 
@section('intro')
{{$intro ?? 'Welcome to the Fast Pace Portal!'}}
@endsection
@section('content')
@if(isset($content) && !empty($content))
{!! $content !!}
@else
@if(isset($email))
<p style="font-size: 16px; color: #2c3e50; margin: 0 0 12px 0;">{{$email}}</p>
@endif
@if(isset($password))
<p style="font-size: 16px; color: #2c3e50; margin: 0 0 12px 0;">{{$password}}</p>
@endif
@if(isset($link) && !empty($link))
<p style="font-size: 16px; color: #2c3e50; margin: 0 0 12px 0;">{{$link}}</p>
@endif
@endif
@endsection
@section('outro')
    {{$outro ?? "We're excited to have you on the team!"}}
@endsection

