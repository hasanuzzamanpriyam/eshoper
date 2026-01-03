
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{\App\CPU\translate('customer_login_info_sub') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        /**
         * Google webfonts. Recommended to include the .woff version for cross-client compatibility.
         */
        @media screen {
            @font-face {
                font-family: 'Source Sans Pro';
                font-style: normal;
                font-weight: 400;
                src: local('Source Sans Pro Regular'), local('SourceSansPro-Regular'), url(https://fonts.gstatic.com/s/sourcesanspro/v10/ODelI1aHBYDBqgeIAH2zlBM0YzuT7MdOe03otPbuUS0.woff) format('woff');
            }

            @font-face {
                font-family: 'Source Sans Pro';
                font-style: normal;
                font-weight: 700;
                src: local('Source Sans Pro Bold'), local('SourceSansPro-Bold'), url(https://fonts.gstatic.com/s/sourcesanspro/v10/toadOcfmlt9b38dHJxOBGFkQc6VGVFSmCnC_l7QZG60.woff) format('woff');
            }
        }

        /**
         * Avoid browser level font resizing.
         * 1. Windows Mobile
         * 2. iOS / OSX
         */
        body {
            -ms-text-size-adjust: 100%; /* 1 */
            -webkit-text-size-adjust: 100%; /* 2 */
        }

        /**
         * Remove blue links for iOS devices.
         */
        a[x-apple-data-detectors] {
            font-family: inherit !important;
            font-size: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
            color: inherit !important;
            text-decoration: none !important;
        }

        /**
         * Fix centering issues in Android 4.4.
         */
        div[style*="margin: 16px 0;"] {
            margin: 0 !important;
        }

        body {
            width: 100% !important;
            height: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .text-info{
            color: #3B71CA;
        }

        .text-center{
            text-align: center;
        }

        .card {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2); /* this adds the "card" effect */
            padding: 16px;
            text-align: center;
            background-color: #f1f1f1;
        }
    </style>

</head>
    <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
<?php
    use App\Model\BusinessSetting;
    $company_phone =BusinessSetting::where('type', 'company_phone')->first()->value;
    $company_email =BusinessSetting::where('type', 'company_email')->first()->value;
    $company_name =BusinessSetting::where('type', 'company_name')->first()->value;
    $company_web_logo =BusinessSetting::where('type', 'company_web_logo')->first()->value;
    
    $customerData=explode(",",$customerDetails);
?>
    	<center>
        	<table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
            	<tr>
                	<td align="center" valign="top" id="bodyCell">
                    	<table border="0" cellpadding="0" cellspacing="0" id="templateContainer">
                        	<tr>
                            	<td align="center" valign="top">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateHeader">
                                        <tr>
                                            <td valign="top" align="center" class="headerContent">
                                                <br>
                                                <br>
                                                <br>
                                               @php($logo=\App\Model\BusinessSetting::where(['type'=>'company_web_logo'])->first()->value)
                                            	<a href="{{url('/')}}" target="_blank">
                                                <img src="{{asset('storage/company/'.$logo)}}" style="max-width:200px;" id="headerImage" mc:label="header_image" mc:edit="header_image" mc:allowdesigner mc:allowtext />
                                                 </a>
                                                <br>
                                                <br><br>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        	<tr>
                            	<td align="center" valign="top">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
                                        <tr>
                                            <td valign="top" class="bodyContent" mc:edit="body_content">
                                                <h2><span style="color: green;">{{\App\CPU\translate('customer_welcome') }} !</span> {{ $customerData['0'] }}</h2>
                                                <h3 align="center"><strong>{{\App\CPU\translate('customer_login_info_text') }}</strong></h3>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        	<tr>
                            	<td align="center" valign="top">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody2">
                                        <tr>
                                            <td align="center" valign="top" class="bodyContent" mc:edit="body_content">
                                                <span style="font-size: 18px; margin-bottom: 7px; font-weight: bold;">{{\App\CPU\translate('customer_login_url') }}</span>
                                            </td>
                                            <td style="width:20px;padding-right: 20px;" align="center">:</td>
                                            <td valign="top" class="bodyContent" mc:edit="body_content">
                                                <span style="font-size: 18px; margin-bottom: 8px;"><a href="{{url('/customer/auth/login')}}" target="_blank">{{url('/customer/auth/login')}}</a></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" valign="top" class="bodyContent" mc:edit="body_content">
                                                <span style="font-size: 18px; margin-bottom: 8px; font-weight: bold;">{{\App\CPU\translate('customer_login_email') }}</span>
                                            </td>
                                            <td style="width:20px;padding-right: 20px;" align="center">:</td>
                                            <td valign="top" class="bodyContent" mc:edit="body_content">
                                                <span style="font-size: 18px; margin-bottom: 8px;">
                                            @if($customerData['1'])
                                                {{ $customerData['1'] }}
                                            @endif
                                            @if($customerData['1'] && $customerData['2'])
                                                {{\App\CPU\translate('or') }}
                                            @endif
                                            @if($customerData['1'])
                                                {{ $customerData['2'] }}
                                            @endif
                                            </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" valign="top" class="bodyContent" mc:edit="body_content">
                                                <span style="font-size: 18px; margin-bottom: 8px; font-weight: bold;">{{\App\CPU\translate('customer_login_password') }}</span>
                                            </td>
                                            <td style="width:20px;padding-right: 20px;" align="center">:</td>
                                            <td valign="top" class="bodyContent" mc:edit="body_content">
                                                <span style="font-size: 18px; margin-bottom: 8px;">{{ $customerData['3'] }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                            	<td align="center" valign="top">
                            		<br><br>
                            		<div style="margin-top: 50px; margin-bottom:30px">
		                            <a style="background-color: #1a82e2; padding:20px;border:none;
		                              margin-top:20px;color:aliceblue;border-radius: 3px; font-size:18px;text-decoration: none; text-transform: capitalize;" href="{{url('/customer/auth/login')}}" target="_blank" rel="noopener noreferrer">
		                              {{\App\CPU\translate('customer_login_btn') }}
		                            </a>
		                          </div>
                            	</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <br>
        </center>
<hr>
<div style="padding:5px;width:650px;margin:auto;margin-top:5px; margin-bottom:50px;">

    <table style="margin:auto;width:90%; color:#777777;">
        <tbody>
            <tr>
                <th style="text-align: left;">
                    <h1>
                        {{$company_name = \App\Model\BusinessSetting::where('type', 'company_name')->first()->value}}
                    </h1>
                </th>
            </tr>
            <tr>
                <th style="text-align: left;">
                    <div> {{\App\CPU\translate('phone')}}
                        : {{\App\Model\BusinessSetting::where('type','company_phone')->first()->value}}</div>
                    <div> {{\App\CPU\translate('website')}}
                        : {{url('/')}}</div>
                    <div > {{\App\CPU\translate('email')}}
                        : {{$company_email}}</div>
                </th>

            </tr>
            <tr>
                @php($social_media = \App\Model\SocialMedia::where('active_status', 1)->get())

                @if(isset($social_media))
                    <th style="text-align: left; padding-top:20px;">
                        <div style="width: 100%;display: flex;
                        justify-content: flex-start;">
                          @foreach ($social_media as $item)

                            <div class="" >
                              <a href="{{$item->link}}" target=”_blank”>
                              <img src="{{asset('assets/back-end/img/'.$item->name.'.png')}}" alt="" style="height: 50px; width:50px; margin:10px;">
                              </a>
                            </div>

                          @endforeach
                        </div>
                    </th>
                @endif
            </tr>
        </tbody>
    </table>
</div>
    </body>

</html>


