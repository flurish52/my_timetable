<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <title>Welcome to myUniAlly</title>
    <!--[if mso]>
    <noscript>
    <xml>
        <o:OfficeDocumentSettings>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    </noscript>
    <![endif]-->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #f3f4f6;
            color: #111827;
            -webkit-font-smoothing: antialiased;
        }

        .wrapper {
            width: 100%;
            background-color: #f3f4f6;
            padding: 40px 16px;
        }

        .container {
            max-width: 560px;
            margin: 0 auto;
        }

        /* Top logo bar */
        .logo-bar {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo {
            width: 180px;
            max-width: 100%;
            height: auto;
        }

        /* Hero card */
        .hero {
            background: #01629c;
            border-radius: 20px 20px 0 0;
            padding: 44px 40px 36px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 180px;
            height: 180px;
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -60px;
            left: -30px;
            width: 220px;
            height: 220px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .hero-icon {
            width: 64px;
            height: 64px;
            background: rgba(255,255,255,0.15);
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 30px;
            line-height: 1;
        }

        .hero h1 {
            font-size: 26px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
            line-height: 1.25;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .hero p {
            font-size: 14px;
            color: rgba(255,255,255,0.80);
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }

        /* Body card */
        .body-card {
            background: #ffffff;
            border-radius: 0 0 20px 20px;
            padding: 36px 40px 40px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        }

        .greeting {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 14px;
        }

        .body-text {
            font-size: 14.5px;
            color: #374151;
            line-height: 1.75;
            margin-bottom: 14px;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: #f3f4f6;
            margin: 28px 0;
        }

        /* Quick start section */
        .section-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9ca3af;
            margin-bottom: 16px;
        }

        .action-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 0;
        }

        .action-item {
            gap: 12px;
            padding: 12px 14px;
            background: #f9fafb;
            border-radius: 12px;
            border: 1px solid #f3f4f6;
        }

        .action-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .action-icon.purple { background: #ede9fe; }
        .action-icon.blue   { background: #dbeafe; }
        .action-icon.green  { background: #dcfce7; }
        .action-icon.amber  { background: #fef3c7; }
        .action-icon.rose   { background: #ffe4e6; }

        .action-text {
            font-size: 13.5px;
            font-weight: 500;
            color: #1f2937;
        }

        /* Motivational quote box */
        .quote-box {
            margin: 28px 0;
            padding: 20px 22px;
            background: linear-gradient(135deg, #ede9fe, #e0e7ff);
            border-radius: 14px;
            border-left: 4px solid #6366f1;
        }

        .quote-text {
            font-size: 14px;
            font-style: italic;
            color: #4338ca;
            line-height: 1.65;
            font-weight: 500;
        }

        /* CTA button */
        .cta-wrap {
            text-align: center;
            margin: 28px 0 24px;
        }

        .cta-btn {
            display: inline-block;
            background: linear-gradient(135deg, #01629c, #6366f1);
            color: #ffffff !important;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            padding: 14px 36px;
            border-radius: 12px;
            letter-spacing: 0.2px;
            box-shadow: 0 4px 14px rgba(99,102,241,0.35);
        }

        .sign-off {
            margin-top: 28px;
            font-size: 14px;
            color: #374151;
            line-height: 1.7;
        }

        .sign-off strong {
            color: #111827;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 28px;
            padding: 0 8px;
        }

        .footer p {
            font-size: 12px;
            color: #9ca3af;
            line-height: 1.6;
        }

        .footer a {
            color: #6366f1;
            text-decoration: none;
        }

        @media (max-width: 480px) {
            .hero { padding: 32px 24px 28px; }
            .body-card { padding: 28px 24px 32px; }
            .hero h1 { font-size: 22px; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container">

{{--        <!-- Logo -->--}}
{{--        <div class="logo-bar">--}}
{{--            <img--}}
{{--                src="{{ config('app.url') }}/logo.png"--}}
{{--                alt="myUniAlly"--}}
{{--                class="logo"--}}
{{--            >--}}
{{--        </div>--}}

        <!-- Hero -->
        <div class="hero">
            <div class="hero-icon">
                <img
                    src="https://myunially.com.ng/logo.png"
                    alt="myUniAlly"
                    class="logo"
                ></div>
            <h1>You made it in,<br>{{ explode(' ', trim($user->name))[0] }}!</h1>
            <p>Your account is ready. Your academic journey just got a way easier.</p>
        </div>

        <!-- Body -->
        <div class="body-card">

            <p class="greeting">Hi {{ $user->name }},</p>

            <p class="body-text">
                Welcome to <strong>myUniAlly</strong>. We are genuinely glad you are here.
            </p>

            <p class="body-text">
                Today is more than just creating an account. It is the first step toward studying smarter,
                staying organized, and getting one step closer to the version of yourself you are working hard to become.
            </p>

            <p class="body-text">
                University life is not easy. The deadlines pile up, the exams come fast, and sometimes it feels
                like everyone else has it figured out except you. We built myUniAlly because we have been there too
                and we wanted something that actually helps.
            </p>

            <div class="divider"></div>

            <p class="section-label">Get started right away</p>

            <ul class="action-list">
                <li class="action-item">
                    <div class="action-icon purple">📅</div>
                    <span class="action-text">Stay updated with your timetable and never miss a lecture</span>
                </li>
                <li class="action-item">
                    <div class="action-icon blue">📝</div>
                    <span class="action-text">Practice past exam questions for your courses</span>
                </li>
                <li class="action-item">
                    <div class="action-icon green">🤝</div>
                    <span class="action-text">Connect with other students in your school via the community</span>
                </li>
{{--                <li class="action-item">--}}
{{--                    <div class="action-icon amber">🔔</div>--}}
{{--                    <span class="action-text">Stay updated on academic news and announcements</span>--}}
{{--                </li>--}}
                <li class="action-item">
                    <div class="action-icon rose">🎯</div>
                    <span class="action-text">Track your progress and keep your goals in sight</span>
                </li>
            </ul>

            <div class="quote-box">
                <p class="quote-text">
                    "Success does not come from one big decision. It comes from small actions taken every single day.
                    You have already taken yours."
                </p>
            </div>

            <div class="cta-wrap">
                <a href="{{ config('app.url') }}/dashboard" class="cta-btn">
                    Go to your dashboard &rarr;
                </a>
            </div>

            <div class="divider"></div>

            <div class="sign-off">
                <p>We are excited to be part of your journey, and we genuinely cannot wait to see what you achieve.</p>
                <br>
                <p>Keep learning. Keep growing. Keep moving forward.</p>
                <br>
                <p>
                    See you inside,<br>
                    <strong>The myUniAlly Team</strong> 👋
                </p>
            </div>

        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                You are receiving this because you just created an account at
                <a href="{{ config('app.url') }}">myunially.com.ng</a>.<br>
                If this was not you, please <a href="mailto:support@myunially.com.ng">contact us</a> immediately.
            </p>
            <p style="margin-top: 10px;">
                &copy; {{ date('Y') }} myUniAlly. Made with ❤️ for Nigerian students.
            </p>
        </div>

    </div>
</div>
</body>
</html>
