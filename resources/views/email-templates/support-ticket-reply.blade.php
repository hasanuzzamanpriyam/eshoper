<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{\App\CPU\translate('Support_Ticket_Reply')}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        body, table, td, a {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }
        table, td {
            mso-table-rspace: 0pt;
            mso-table-lspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
        }
        a[x-apple-data-detectors] {
            font-family: inherit !important;
            font-size: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
            color: inherit !important;
            text-decoration: none !important;
        }
        body {
            width: 100% !important;
            height: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        table {
            border-collapse: collapse !important;
        }
        a {
            color: #1a82e2;
        }
        img {
            height: auto;
            line-height: 100%;
            text-decoration: none;
            border: 0;
            outline: none;
        }
    </style>
</head>
<body style="background-color: #f4f5f7; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 0 auto; padding: 30px 15px;">
        <div style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
            <div style="padding: 35px 35px 20px; text-align: center;">
                <h1 style="margin: 0; font-size: 22px; font-weight: 700; color: #1a1a2e; letter-spacing: -0.3px;">
                    {{ \App\Model\BusinessSetting::where(['type' => 'company_name'])->first()->value }}
                </h1>
                <div style="width: 40px; height: 3px; background-color: #1a82e2; margin: 12px auto 0; border-radius: 2px;"></div>
            </div>

            <div style="padding: 10px 35px 30px;">
                <p style="font-size: 16px; color: #1a1a2e; font-weight: 600; margin: 0 0 5px;">{{\App\CPU\translate('hello')}}, {{ $user_name }}</p>
                <p style="font-size: 15px; color: #5a5a7a; line-height: 1.6; margin: 0;">{{\App\CPU\translate('admin_replied_to_your_support_ticket')}}</p>

                <div style="background-color: #f8f9fc; border: 1px solid #e8eaf0; border-radius: 6px; padding: 22px; margin: 22px 0;">
                    <p style="margin: 0 0 4px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #9a9ab0;">{{\App\CPU\translate('ticket_subject')}}</p>
                    <p style="margin: 0 0 20px; font-size: 16px; font-weight: 600; color: #1a1a2e;">{{ $subject }}</p>

                    <p style="margin: 0 0 4px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #9a9ab0;">{{\App\CPU\translate('admin_message')}}</p>
                    <p style="margin: 0; font-size: 15px; color: #3a3a5a; line-height: 1.7;">{{ $reply_message }}</p>
                </div>

                <div style="text-align: center; margin-top: 28px;">
                    <a href="{{ route('support-ticket.index', $ticket_id) }}" style="display: inline-block; background-color: #1a82e2; color: #ffffff; padding: 13px 34px; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600;">{{\App\CPU\translate('view_ticket')}}</a>
                </div>
            </div>

            <div style="border-top: 1px solid #e8eaf0; padding: 22px 35px; text-align: center;">
                <p style="margin: 0 0 3px; font-size: 14px; color: #1a1a2e; font-weight: 600;">{{ \App\Model\BusinessSetting::where(['type' => 'company_name'])->first()->value }}</p>
                <p style="margin: 0; font-size: 13px; color: #9a9ab0;">{{ \App\Model\BusinessSetting::where(['type' => 'company_email'])->first()->value }}</p>
            </div>
        </div>
    </div>
</body>
</html>
