<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invitation->title }}</title>
    
    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ $canonicalUrl ?? url()->current() }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Lato', sans-serif;
            background: #1a1a2e;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Partikel latar */
        .particles {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background: radial-gradient(ellipse at 20% 50%, rgba(201,169,110,.08) 0%, transparent 60%),
                        radial-gradient(ellipse at 80% 20%, rgba(201,169,110,.06) 0%, transparent 50%);
        }

        .cover-card {
            position: relative; z-index: 1;
            background: rgba(255,255,255,.04);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(201,169,110,.25);
            border-radius: 20px;
            padding: 60px 50px;
            max-width: 480px;
            width: 90%;
            text-align: center;
            box-shadow: 0 25px 60px rgba(0,0,0,.4);
        }

        .bismillah {
            font-size: 1.6rem;
            color: #c9a96e;
            margin-bottom: 24px;
            opacity: .9;
        }

        .label {
            color: rgba(255,255,255,.5);
            letter-spacing: 4px;
            text-transform: uppercase;
            font-size: .7rem;
            margin-bottom: 16px;
        }

        .couple-names {
            font-family: 'Playfair Display', serif;
            color: #fff;
            font-size: 2.2rem;
            line-height: 1.3;
        }

        .ampersand {
            display: block;
            font-style: italic;
            color: #c9a96e;
            font-size: 2.8rem;
            margin: 4px 0;
        }

        .divider {
            width: 50px; height: 1px;
            background: linear-gradient(to right, transparent, #c9a96e, transparent);
            margin: 24px auto;
        }

        .guest-label {
            color: rgba(255,255,255,.5);
            font-size: .8rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .guest-name {
            color: #fff;
            font-size: 1.15rem;
            font-weight: 300;
            margin-bottom: 32px;
        }

        .btn-open {
            display: inline-block;
            padding: 14px 40px;
            background: linear-gradient(135deg, #c9a96e, #a07840);
            color: #fff;
            text-decoration: none;
            border-radius: 50px;
            font-size: .9rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            border: none;
            cursor: pointer;
            transition: transform .2s, box-shadow .2s;
            box-shadow: 0 8px 24px rgba(201,169,110,.3);
        }

        .btn-open:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(201,169,110,.45);
            color: #fff;
            text-decoration: none;
        }

        .event-date {
            color: rgba(255,255,255,.4);
            font-size: .8rem;
            margin-top: 24px;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
<div class="particles"></div>

<div class="cover-card">
    <div class="bismillah">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم</div>

    <div class="label">Undangan Pernikahan</div>

    <div class="couple-names">
        {{ $data['groom_nickname'] ?? $data['groom_name'] ?? 'Mempelai Pria' }}
        <span class="ampersand">&</span>
        {{ $data['bride_nickname'] ?? $data['bride_name'] ?? 'Mempelai Wanita' }}
    </div>

    <div class="divider"></div>

    @if($guestName)
        <div class="guest-label">Kepada Yth.</div>
        <div class="guest-name">{{ $guestName }}</div>
    @endif

    <a href="{{ $invitationUrl }}" class="btn-open">
        Buka Undangan
    </a>

    @if(!empty($data['akad_date']))
        <div class="event-date">
            {{ \Carbon\Carbon::parse($data['akad_date'])->translatedFormat('d F Y') }}
        </div>
    @endif
</div>

</body>
</html>
