<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Confirm E-mail</title>

    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>

    <!-- Styles -->
    <style>
        html,
        body {
            margin: 10px auto 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            background: #f1f1f1;
        }
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }
        div[style*="margin: 16px 0"] {
            margin: 0 !important;
        }
        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }
        img {
            -ms-interpolation-mode: bicubic;
        }
        a {
            text-decoration: none;
        }

        *[x-apple-data-detectors],
        .unstyle-auto-detected-links *,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }
        .a6S {
            display: none !important;
            opacity: 0.01 !important;
        }
        .im {
            color: inherit !important;
        }
        img.g-img+div {
            display: none !important;
        }
        @media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
            u~div .email-container {
                min-width: 320px !important;
            }
        }
        @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
            u~div .email-container {
                min-width: 375px !important;
            }
        }
        @media only screen and (min-device-width: 414px) {
            u~div .email-container {
                min-width: 414px !important;
            }
        }
        .primary {
            color: #0FACEA !important;
            font-weight: 500 !important;
        }
        .light {
            color: #fff !important;
            font-weight: 500 !important;
        }
        .bg_white {
            background: #ffffff !important;
        }
        .bg_light {
            background: #F8F8F8;
        }
        .bg_primary {
            background: #0FACEA;
        }
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Poppins';
            color: #000000 !important;
            margin-top: 0;
            font-weight: 400;
        }
        body {
            font-family: 'Poppins';
            font-weight: 400;
            font-size: 15px;
            line-height: 1.8;
            color: rgba(0, 0, 0, .4);
        }
        a {
            color: #0FACEA;
        }
        .btn {
            border-radius: 1px;
            cursor: pointer;
            border: 0;
            background: #0FACEA;
            color: #FFF;
            font-weight: 600;
            font-size: 16px;
            padding: 10px 20px;
        }
    </style>
</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f1f1f1;">
    <br>
    <center style="width: 100%; background-color: #f1f1f1;">
        <div style="max-width: 600px; margin: 0 auto; border: 1px solid #D9D9D9;" class="email-container">
            <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
                <tr>
                    <td valign="top" class="bg_white" style="padding: 1em 2.5em 0 2.5em;">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td class="logo" style="text-align: center;">
                                    <img src="http://diamondtrader.co/assets/img/logo.png" width="128" style="margin-bottom: 20px">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="middle" class="hero bg_primary" style="padding: 1em 0 0 0; text-align: center">
                        <h1 class="light">Ative sua conta</h1>
                    </td>
                </tr>
                <tr>
                    <td valign="middle" class="hero bg_white" style="padding: 3em 0 0 0;">
                        <div class="text" style="text-align: center;">
                            <h1 class="primary">Olá {{ explode(' ', $user->name)[0] }}!</h1>
                            <p style="font-size: 16px;">Bem-vindo à BitcoinTrade!</p>
                            <p style="font-size: 16px;">Para começar a utilizar sua conta, clique no botão abaixo<br>para confirmar o seu e-mail e ativar sua conta.</p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td valign="middle" class="hero bg_white" style="padding: 1em 0 0 0;">
                        <div class="text" style="text-align: center;">
                            <a href="http://localhost:8000/api/activateAccount/{{ md5($user->code) }}" class="btn">Ativar minha conta</a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td valign="middle" class="hero bg_white" style="padding: 1em 0 0 0;">
                        <div class="text" style="text-align: center;">
                            <p style="font-size: 16px;">
                                Obrigado.<br>Equipe BitcoinTrade
                            </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td valign="middle" class="hero bg_white" style="padding: 1em 0 0 0;">
                        <div class="text" style="text-align: center;">
                            <p style="font-size: 16px;">
                                Caso você não tenha se registrado para esta conta,<br>ignore esta mensagem e a conta será removida.
                            </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td valign="middle" class="hero bg_white" style="padding: 1em 0 0 0;">
                        <div class="text" style="text-align: center;">
                            <p style="font-size: 16px;">
                                Em caso de dúvidas, envie um e-mail para <br><a href="mail:suporte@bitcointrade.com.br">suporte@bitcointrade.com.br</a>.
                            </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="bg_light" style="text-align: center;">
                        Cadastrado em: <b style="margin-left: 5px">{{ date('d M Y - H:i', strtotime($user->created_at)) }}</b>

                        @if ($location)

                            <br>Local: <b style="margin-left: 5px">{{ $location->cityName }} - {{ $location->regionName }} - {{ $location->countryName }}</b> <br>
                            IP: <b style="margin-left: 5px">{{ $location->ip }}</b>

                        @endif

                    </td>
                </tr>
            </table>
        </div>
    </center>
</body>

</html>
