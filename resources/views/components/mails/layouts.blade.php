<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
</head>

<body style="margin:0; padding:0; background:#f3f4f6; font-family: Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; margin:40px 0; padding:30px; border-radius:8px;">

                    <tr>
                        <td style="text-align:center; padding-bottom:20px;">
                            <h2 style="margin:0; color:#111;">
                                Alibubu - @yield('title', 'Notification')
                            </h2>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="color:#333; font-size:14px; line-height:1.6;">
                            @yield('content')
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="text-align:center; padding-top:30px; font-size:12px; color:#999;">
                            © {{ date('Y') }} Alibubu. All rights reserved.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
