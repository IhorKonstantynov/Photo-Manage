@extends('emails.layouts.email')

@section('title', 'Refund Mail')

@section('content')
    <h3 style="font-size: 18px; color: black;">{{ __('emails.Hey', ["name" => $data->name]) }}</h3>
    <p style="font-size: 16px; color: #333;">{{__('emails.RefundSorryText')}}</p>
    <p style="font-size: 16px;  color: #333;">{{__('emails.RefundTip')}}</p>
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