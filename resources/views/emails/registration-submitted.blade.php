<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration received</title>
</head>
<body style="margin:0;background:#f6f4ef;color:#101216;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f4ef;padding:28px 14px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;overflow:hidden;border-radius:18px;background:#ffffff;border:1px solid rgba(16,18,22,.08);">
                    <tr>
                        <td style="background:#101216;padding:26px 28px;">
                            <img src="https://sakhadib.wordpress.com/wp-content/uploads/2026/06/logo-white.png" alt="{{ config('app.name') }}" style="height:44px;width:auto;display:block;">
                            <p style="margin:18px 0 0;color:rgba(255,255,255,.56);font-size:12px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;">Registration received</p>
                            <h1 style="margin:8px 0 0;color:#ffffff;font-size:28px;line-height:1.18;font-weight:700;">{{ $registration->event?->name }}</h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:30px 28px;">
                            <p style="margin:0;font-size:16px;line-height:1.7;color:#374151;">Hi {{ $registration->contact_name }},</p>
                            <p style="margin:12px 0 0;font-size:16px;line-height:1.7;color:#374151;">
                                We have received the registration for <strong>{{ $registration->team_name }}</strong>. Keep this registration code safe; you will need it to check updates.
                            </p>

                            <div style="margin:26px 0;padding:22px;border-radius:14px;background:#d4574e;color:#ffffff;">
                                <p style="margin:0;font-size:12px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;opacity:.82;">Registration Code</p>
                                <p style="margin:8px 0 0;font-size:34px;line-height:1;font-weight:800;letter-spacing:.04em;">{{ $registration->registration_code }}</p>
                            </div>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid rgba(16,18,22,.08);color:#6b7280;font-size:13px;">Team / Player</td>
                                    <td align="right" style="padding:12px 0;border-bottom:1px solid rgba(16,18,22,.08);font-size:13px;font-weight:700;">{{ $registration->team_name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid rgba(16,18,22,.08);color:#6b7280;font-size:13px;">Institution</td>
                                    <td align="right" style="padding:12px 0;border-bottom:1px solid rgba(16,18,22,.08);font-size:13px;font-weight:700;">{{ $registration->institution }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid rgba(16,18,22,.08);color:#6b7280;font-size:13px;">Registration Status</td>
                                    <td align="right" style="padding:12px 0;border-bottom:1px solid rgba(16,18,22,.08);font-size:13px;font-weight:700;text-transform:capitalize;">{{ $registration->status }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid rgba(16,18,22,.08);color:#6b7280;font-size:13px;">Payment Status</td>
                                    <td align="right" style="padding:12px 0;border-bottom:1px solid rgba(16,18,22,.08);font-size:13px;font-weight:700;text-transform:capitalize;">{{ $registration->payment_status }}</td>
                                </tr>
                                @if($registration->payment)
                                    <tr>
                                        <td style="padding:12px 0;border-bottom:1px solid rgba(16,18,22,.08);color:#6b7280;font-size:13px;">TRX ID</td>
                                        <td align="right" style="padding:12px 0;border-bottom:1px solid rgba(16,18,22,.08);font-size:13px;font-weight:700;">{{ $registration->payment->trx_id }}</td>
                                    </tr>
                                @endif
                            </table>

                            <div style="margin-top:28px;text-align:center;">
                                <a href="{{ route('registration.status', ['code' => $registration->registration_code]) }}" style="display:inline-block;border-radius:10px;background:#101216;color:#ffffff;font-size:14px;font-weight:700;text-decoration:none;padding:13px 18px;">
                                    Check Registration Status
                                </a>
                            </div>

                            <p style="margin:28px 0 0;font-size:13px;line-height:1.7;color:#6b7280;">
                                If the button does not work, open this link:
                                <br>
                                <a href="{{ route('registration.status', ['code' => $registration->registration_code]) }}" style="color:#d4574e;">{{ route('registration.status', ['code' => $registration->registration_code]) }}</a>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#101216;padding:18px 28px;color:rgba(255,255,255,.56);font-size:12px;line-height:1.6;">
                            {{ config('app.name') }}<br>
                            This is an automated confirmation email.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
