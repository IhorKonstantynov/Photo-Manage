@extends('emails.layouts.email')

@section('title', 'Referrals pay Request.')

@section('content')
    <h3 style="font-size: 18px; color: black;">Hello. {{$user->name}} requested referrals payment</h3>
    <p style="font-size: 16px; color: #333;">Please review and make a payment. 🎉</p>
    <br>
    <a href="{{ route('admin.review.request', $user->id) }}" style="background: #fe4801; padding: 15px 25px; color: white; font-weight: bold; text-decoration: none; border-radius: 5px;"> Review Now</a>
    <br>
@endsection