@extends('emails.layouts.email')

@section('title', 'Promo Code')

@section('content')
    <h3 style="font-size: 18px; color: black;">{{__('emails.Hello')}}. {{__('emails.InviteText', ['name' => $data->name, 'company_name' => $data->company_name])}} </h3>
    <p style="font-size: 16px; color: #333;">{{ __('emails.InviteRegisterTip') }} 🎉</p>
    <br>
    <a href="{{route('register') . '?utm_source=enterprise&utm_content=' . $data->id}}" style="background: #fe4801; padding: 15px 25px; color: white; font-weight: bold; text-decoration: none; border-radius: 5px;"> {{ __('emails.RegisterNow') }} </a>
    <br>
    <br>
    <p style="font-size: 16px;  color: #333;">{{ __('emails.SupportText') }}</p>
    <div style="display: flex; align-items:center;">
      <img src="{{ asset('imgs/Support.webp') }}" width="70px" alt="support">
      <div style="flex: 1">
        <p style="margin: 5px 10px 0; color: #333;">Jessica Johnson</p>
        <p style="margin: 0 10px; color: #333;">{{ __('emails.CustomerSuccessManager') }}</p>
        <p style="margin: 0 10px; color: #333;">Prophotos.AI</p>
      </div>
    </div>
@endsection