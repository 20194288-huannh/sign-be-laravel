@extends('beautymail::templates.sunny')

@section('content')

    @include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Hello!',
        'level' => 'h1',
    ])

    @include('beautymail::templates.sunny.contentStart')

    <div style="display: flex; justify-content: center">
        <div style="width: 90%;">
        <p>Hello gundamakp01@gmail.com, </p>
            <p>Huấn Nguyễn Hữu sent a copy of the LỊCH TRỰC NHẬT KIAI.xlsx - February_2024 (1)</p>
            <div style="background-color:#f8f8f8; display: flex; justify-content: center">
                <div style="display: flex; width: 90%; align-items:center; padding: 10px">
                    <img src="https://ci3.googleusercontent.com/meips/ADKq_NbZSR_i0fvEt7u2zfMzNtyBacCQESHm5WTl4-5QVWLIERYR0hp2_ZR9rtoBdtm9njWtpOSY-JTvvBlJ3bHBclFH-zEbn9NW3uCNLQLBQ61en4XKswcvmya5TGyAeai2=s0-d-e1-ft#https://cdns.sign.co/site/v1.0.1/sign/src/assets/images/document_icon.png" class="CToWUd" data-bit="iit">
                    <span style="float:left;color:#333;padding-left:20px;font-weight:600">LỊCH TRỰC NHẬT KIAI.xlsx - February_2024 (1) </span>
                </div>
            </div>
            <br/>
            <div style="border:1px solid transparent;font-family:Arial;color:#676767;text-align:left;font-size:14px;line-height:28px;font-weight:bold;height:28px;width:100%"> Do not share this email</div>
            <div style="border:1px solid transparent;font-family:Arial;color:#676767;text-align:left;font-size:13px;line-height:18px;height:36px;width:100%"> This email contains a secure link. Please do not forward this email, link, access code with others to prevent your document. </div>
            <button style="background-color:#f3f3f3; border: none; width: 100%; font-family:Arial;color:#333;font-size:13px; padding: 5px 5px"> Go paperless on a single click using </button>
        </div>
    </div>

    @include('beautymail::templates.sunny.contentEnd')
@stop