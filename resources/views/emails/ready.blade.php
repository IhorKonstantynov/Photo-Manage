@extends('emails.layouts.email')

@section('title', 'Your Headshots are Ready!')

@section('content')
    {{-- <h3 style="font-size: 18px; color: black;">Dear.</h3> --}}
    <p style="font-size: 16px; color: #333;">{{-- Thank you for your patience, your{{ $flag == 1 ? ' additional' : '' }} headshots are ready!  --}}{{__('emails.ThanksPatience')}}</p>
    <br>
    <a href="{{route('photo.gallery', ['id' => $id, 'type' => 'result'])}}" style="background: #fe4801; padding: 15px 25px; color: white; font-weight: bold; text-decoration: none; border-radius: 5px;">{{__('emails.YourAIHeadshots')}}</a>
    <br>
    <br>
    <p style="font-size: 16px;  color: #333;">{{__('emails.Best')}},</p>
    <div style="display: flex; align-items:center;">
      <img src="{{ asset('imgs/Support.webp') }}" width="70px" alt="support">
      <div style="flex: 1">
        <p style="margin: 5px 10px 0; color: #333;">Jessica Johnson</p>
        <p style="margin: 0 10px; color: #333;">{{__('emails.CustomerSuccessManager')}}</p>
        <p style="margin: 0 10px; color: #333;">Prophotos.AI</p>
      </div>
    </div>
@endsection