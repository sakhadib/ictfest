<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IUPC coach portal</title>
</head>
<body style="margin:0;background:#f6f4ef;color:#101216;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f4ef;padding:28px 14px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;overflow:hidden;border-radius:18px;background:#ffffff;border:1px solid rgba(16,18,22,.08);">
                    <tr>
                        <td style="background:#101216;padding:26px 28px;">
                            <img src="https://sakhadib.wordpress.com/wp-content/uploads/2026/06/logo-white.png" alt="{{ config('app.name') }}" style="height:44px;width:auto;display:block;">
                            <p style="margin:18px 0 0;color:rgba(255,255,255,.56);font-size:12px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;">IUPC coach access</p>
                            <h1 style="margin:8px 0 0;color:#ffffff;font-size:28px;line-height:1.18;font-weight:700;">{{ $coachLink->allocation?->name }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 28px;">
                            <p style="margin:0;font-size:16px;line-height:1.7;color:#374151;">Dear {{ $coachLink->coach?->name }},</p>
                            <p style="margin:14px 0 0;font-size:16px;line-height:1.7;color:#374151;">
                                Please use the private link below to review IUPC teams from your university, submit final registration information, T-shirt sizes, and payment details.
                            </p>
                            <div style="margin:26px 0;padding:22px;border-radius:14px;background:#d4574e;color:#ffffff;">
                                <p style="margin:0;font-size:12px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;opacity:.82;">University Slot Portal</p>
                                <p style="margin:8px 0 0;font-size:18px;line-height:1.5;font-weight:700;">This link is private to you. Do not share it publicly.</p>
                            </div>
                            <div style="margin-top:28px;text-align:center;">
                                <a href="{{ $url }}" style="display:inline-block;border-radius:10px;background:#101216;color:#ffffff;font-size:14px;font-weight:700;text-decoration:none;padding:13px 18px;">
                                    Open IUPC Coach Portal
                                </a>
                            </div>
                            <p style="margin:28px 0 0;font-size:13px;line-height:1.7;color:#6b7280;">
                                If the button does not work, open this link:
                                <br>
                                <a href="{{ $url }}" style="color:#d4574e;">{{ $url }}</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#101216;padding:18px 28px;color:rgba(255,255,255,.56);font-size:12px;line-height:1.6;">
                            {{ config('app.name') }}<br>
                            This is an automated email for IUPC final registration coordination.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
