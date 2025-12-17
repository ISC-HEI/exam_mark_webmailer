<div style="background-color:#f2f4f6; padding:40px 0; font-family: 'Segoe UI', Arial, sans-serif;">

    <table align="center" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.1);">
        <tr>
            <td style="background-color:#0d6efd; text-align:center; padding:30px;">
                <h1 style="margin:0; color:#ffffff; font-size:24px; font-weight:600;">{{ $courseName }}</h1>
            </td>
        </tr>

        <tr>
            <td style="padding:30px; color:#495057; font-size:16px; line-height:1.6; text-align:center;">
                {!! nl2br(e($messageContent)) !!}
            </td>
        </tr>

        <tr>
            <td style="background-color:#f8f9fa; text-align:center; padding:20px; font-size:12px; color:#6c757d;">
                Ceci est un email automatique, merci de ne pas répondre.
            </td>
        </tr>
    </table>

</div>
