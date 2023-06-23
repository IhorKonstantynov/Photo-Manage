@extends('emails.layouts.email')

@section('title', 'Welcome')

@section('content')
    <h3 style="font-size: 18px; color: black;">{{ __('emails.Hey', ["name" => $data->name]) }}.</h3>
    <p style="font-size: 16px;  color: #333;">{{ __('emails.WelcomeText') }}</p>
    <p style="font-size: 16px;  color: #333;">
      {{ __('emails.SupportText') }}
    </p>
    <p style="font-size: 16px;  color: #333;">{{__('emails.HappyCreating')}}</p>
    <br>
    <a href="{{ route('home') }}" style="background: #fe4801; padding: 15px 25px; color: white; font-weight: bold; text-decoration: none; border-radius: 5px;">{{__('emails.YourAccount')}}</a>
    <br>
    <br>
    <p style="font-size: 16px;  color: #333;">{{__('emails.WarmRegards')}},</p>
    <div style="display: flex; align-items:center;">
      <img src="{{ asset('imgs/Support.webp') }}" width="70px" alt="support">
      <div style="flex: 1">
        <p style="margin: 5px 10px 0; color: #333;">Jessica Johnson</p>
        <p style="margin: 0 10px; color: #333;">{{__('emails.CustomerSuccessManager')}}</p>
        <p style="margin: 0 10px; color: #333;">Prophotos.AI</p>
      </div>
    </div>
@endsection