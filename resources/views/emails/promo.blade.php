@extends('emails.layouts.email')

@section('title', 'Promo Code')

@section('content')
    <h3 style="font-size: 18px; color: black;">{{ __('emails.Dear', ['name' => $data->name]) }}</h3>
    <p style="font-size: 16px; color: #333;">{{ __('emails.PromocodeText') }}</p>
    <p style="font-size: 16px; color: black;">👉 @php echo __('emails.UsePromoText', ['promo' => '<b>FRIENDS20</b>']); @endphp 👈
    </p>
    <p style="font-size: 16px;  color: #333;">{{ __('emails.DiscountTimeTip', ['percent' => 20, 'time' => 24]) }} </p>
    <br>
    <a href="{{ route('profile.edit') }}"
        style="background: #fe4801; padding: 15px 25px; color: white; font-weight: bold; text-decoration: none; border-radius: 5px;">{{ __('emails.UsePromoCode') }}</a>
    <br>
    <br>
    <p style="font-size: 16px;  color: #333;">{{ __('emails.SupportText') }}</p>
    <p style="font-size: 16px;  color: #333;">{{ __('emails.HappyCreating') }}</p>
    <div style="display: flex; align-items:center;">
        <img src="{{ asset('imgs/Support.webp') }}" width="70px" alt="support">
        <div style="flex: 1">
            <p style="margin: 5px 10px 0; color: #333;">Jessica Johnson</p>
            <p style="margin: 0 10px; color: #333;">{{ __('emails.CustomerSuccessManager') }}</p>
            <p style="margin: 0 10px; color: #333;">Prophotos.AI</p>
        </div>
    </div>
@endsection
