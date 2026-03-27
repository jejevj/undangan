<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invitation->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Lato', sans-serif; background: #fafafa; color: #333; }
        .container { max-width: 680px; margin: 0 auto; background: #fff; }

        /* Hero */
        .hero { background: linear-gradient(135deg, #f5f0eb 0%, #ede8e3 100%); padding: 80px 40px; text-align: center; }
        .hero .bismillah { font-size: 2rem; color: #8b7355; margin-bottom: 20px; }
        .hero h1 { font-family: 'Playfair Display', serif; font-size: 2.8rem; color: #5a4a3a; line-height: 1.2; }
        .hero .ampersand { font-family: 'Playfair Display', serif; font-style: italic; font-size: 3.5rem; color: #c9a96e; display: block; margin: 10px 0; }
        .hero .subtitle { color: #8b7355; margin-top: 15px; letter-spacing: 3px; text-transform: uppercase; font-size: 0.8rem; }

        /* Section */
        .section { padding: 60px 40px; text-align: center; }
        .section-title { font-family: 'Playfair Display', serif; font-size: 1.8rem; color: #5a4a3a; margin-bottom: 10px; }
        .divider { width: 60px; height: 2px; background: #c9a96e; margin: 15px auto; }

        /* Couple */
        .couple-grid { display: grid; grid-template-columns: 1fr auto 1fr; gap: 30px; align-items: center; margin-top: 40px; }
        .couple-card { text-align: center; }
        .couple-photo { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #c9a96e; margin: 0 auto 15px; display: block; }
        .couple-photo-placeholder { width: 150px; height: 150px; border-radius: 50%; background: #f0ebe5; border: 4px solid #c9a96e; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: #c9a96e; }
        .couple-name { font-family: 'Playfair Display', serif; font-size: 1.5rem; color: #5a4a3a; }
        .couple-parents { color: #8b7355; font-size: 0.85rem; margin-top: 8px; line-height: 1.6; }
        .couple-separator { font-family: 'Playfair Display', serif; font-style: italic; font-size: 3rem; color: #c9a96e; }

        /* Event */
        .event-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 40px; }
        .event-card { background: #f9f5f0; border-radius: 12px; padding: 30px; text-align: center; }
        .event-card h3 { font-family: 'Playfair Display', serif; color: #5a4a3a; margin-bottom: 15px; }
        .event-date { font-size: 1.1rem; font-weight: 700; color: #c9a96e; }
        .event-time { color: #8b7355; margin: 5px 0; }
        .event-venue { font-weight: 600; color: #5a4a3a; margin-top: 10px; }
        .event-address { color: #8b7355; font-size: 0.85rem; margin-top: 5px; }

        /* Maps */
        .maps-btn { display: inline-block; margin-top: 20px; padding: 12px 30px; background: #c9a96e; color: #fff; text-decoration: none; border-radius: 30px; font-size: 0.9rem; letter-spacing: 1px; }

        /* Love Story */
        .love-story { background: #f9f5f0; }
        .love-story p { max-width: 500px; margin: 20px auto 0; line-height: 1.8; color: #666; }

        /* Footer */
        .footer { background: #5a4a3a; color: #c9a96e; text-align: center; padding: 30px; font-size: 0.85rem; }

        @media (max-width: 600px) {
            .couple-grid { grid-template-columns: 1fr; }
            .couple-separator { display: none; }
            .event-grid { grid-template-columns: 1fr; }
            .hero h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>
<div class="container">

    {{-- Hero --}}
    <div class="hero">
        <div class="bismillah">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم</div>
        <p class="subtitle">Undangan Pernikahan</p>
        <h1>
            {{ $data['groom_nickname'] ?? $data['groom_name'] ?? 'Mempelai Pria' }}
            <span class="ampersand">&</span>
            {{ $data['bride_nickname'] ?? $data['bride_name'] ?? 'Mempelai Wanita' }}
        </h1>
    </div>

    {{-- Mempelai --}}
    <div class="section">
        <h2 class="section-title">Mempelai</h2>
        <div class="divider"></div>
        <div class="couple-grid">
            {{-- Pria --}}
            <div class="couple-card">
                @if(!empty($data['groom_photo']))
                    <img src="{{ asset('storage/' . $data['groom_photo']) }}" class="couple-photo" alt="">
                @else
                    <div class="couple-photo-placeholder">♂</div>
                @endif
                <div class="couple-name">{{ $data['groom_name'] ?? '-' }}</div>
                <div class="couple-parents">
                    Putra dari<br>
                    {{ $data['groom_father'] ?? '' }}
                    @if(!empty($data['groom_father']) && !empty($data['groom_mother'])) & @endif
                    {{ $data['groom_mother'] ?? '' }}
                </div>
            </div>
            <div class="couple-separator">&</div>
            {{-- Wanita --}}
            <div class="couple-card">
                @if(!empty($data['bride_photo']))
                    <img src="{{ asset('storage/' . $data['bride_photo']) }}" class="couple-photo" alt="">
                @else
                    <div class="couple-photo-placeholder">♀</div>
                @endif
                <div class="couple-name">{{ $data['bride_name'] ?? '-' }}</div>
                <div class="couple-parents">
                    Putri dari<br>
                    {{ $data['bride_father'] ?? '' }}
                    @if(!empty($data['bride_father']) && !empty($data['bride_mother'])) & @endif
                    {{ $data['bride_mother'] ?? '' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Acara --}}
    <div class="section" style="background:#f9f5f0">
        <h2 class="section-title">Acara</h2>
        <div class="divider"></div>
        <div class="event-grid">
            <div class="event-card">
                <h3>Akad Nikah</h3>
                @if(!empty($data['akad_date']))
                    <div class="event-date">{{ \Carbon\Carbon::parse($data['akad_date'])->translatedFormat('l, d F Y') }}</div>
                @endif
                @if(!empty($data['akad_time']))
                    <div class="event-time">{{ $data['akad_time'] }} WIB</div>
                @endif
                <div class="event-venue">{{ $data['akad_venue'] ?? '' }}</div>
                <div class="event-address">{{ $data['akad_address'] ?? '' }}</div>
            </div>
            <div class="event-card">
                <h3>Resepsi</h3>
                @if(!empty($data['reception_date']))
                    <div class="event-date">{{ \Carbon\Carbon::parse($data['reception_date'])->translatedFormat('l, d F Y') }}</div>
                @endif
                @if(!empty($data['reception_time']))
                    <div class="event-time">{{ $data['reception_time'] }} WIB</div>
                @endif
                <div class="event-venue">{{ $data['reception_venue'] ?? '' }}</div>
                <div class="event-address">{{ $data['reception_address'] ?? '' }}</div>
            </div>
        </div>
        @if(!empty($data['maps_url']))
            <a href="{{ $data['maps_url'] }}" target="_blank" class="maps-btn">📍 Lihat di Google Maps</a>
        @endif
    </div>

    {{-- Love Story --}}
    @if(!empty($data['love_story']))
    <div class="section love-story">
        <h2 class="section-title">Cerita Kami</h2>
        <div class="divider"></div>
        <p>{{ $data['love_story'] }}</p>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p>{{ $invitation->title }}</p>
        <p style="margin-top:8px;opacity:.7">Dibuat dengan ❤ menggunakan sistem undangan digital</p>
    </div>

</div>
</body>
</html>
