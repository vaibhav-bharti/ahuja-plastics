<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Ahuja Plastics — Precision Plastic Manufacturing</title>
        <meta name="description" content="Ahuja Plastics — precision injection molding and custom plastic components, engineered for durability and scale.">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <style>
            :root{
                --bg: #121417;
                --bg-soft: #171a1e;
                --surface: #1c2025;
                --surface-2: #23282e;
                --border: #2e343b;
                --text: #f2efe9;
                --muted: #9098a1;
                --amber: #e8843b;
                --amber-deep: #c96a2a;
                --amber-glow: rgba(232,132,59,0.35);
            }
            *{ box-sizing:border-box; margin:0; padding:0; }
            html{ scroll-behavior:smooth; }
            body{
                background:var(--bg);
                color:var(--text);
                font-family:'Inter', system-ui, sans-serif;
                min-height:100vh;
                overflow-x:hidden;
                position:relative;
            }
            /* subtle material grain */
            body::before{
                content:"";
                position:fixed; inset:0;
                pointer-events:none;
                opacity:.035;
                z-index:1;
                background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            }
            .wrap{
                position:relative; z-index:2;
                max-width:1180px;
                margin:0 auto;
                padding:32px 28px 0;
            }
            /* ---- Nav ---- */
            nav{
                display:flex;
                align-items:center;
                justify-content:space-between;
                padding-bottom:56px;
            }
            .brand{
                display:flex;
                flex-direction:column;
                gap:2px;
            }
            .brand-name{
                font-family:'Space Grotesk', sans-serif;
                font-weight:700;
                font-size:19px;
                letter-spacing:.02em;
                color:var(--text);
            }
            .brand-name span{ color:var(--amber); }
            .brand-tag{
                font-family:'JetBrains Mono', monospace;
                font-size:10.5px;
                letter-spacing:.14em;
                text-transform:uppercase;
                color:var(--muted);
            }
            .nav-links{
                display:flex;
                align-items:center;
                gap:28px;
            }
            .nav-links a.plain{
                font-size:14px;
                color:var(--muted);
                text-decoration:none;
                transition:color .2s ease;
            }
            .nav-links a.plain:hover{ color:var(--text); }

            /* Smooth premium login button */
            .btn-login{
                position:relative;
                display:inline-flex;
                align-items:center;
                gap:8px;
                font-family:'Inter', sans-serif;
                font-weight:600;
                font-size:14px;
                color:var(--text);
                text-decoration:none;
                padding:10px 22px;
                border-radius:999px;
                background:linear-gradient(180deg, var(--surface-2), var(--surface));
                border:1px solid var(--border);
                box-shadow:0 1px 0 rgba(255,255,255,0.03) inset, 0 8px 20px -12px rgba(0,0,0,0.6);
                transition:border-color .35s ease, box-shadow .35s ease, transform .25s ease;
            }
            .btn-login::before{
                content:"";
                position:absolute; inset:-1px;
                border-radius:999px;
                padding:1px;
                background:linear-gradient(120deg, transparent, var(--amber-glow), transparent);
                -webkit-mask:linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
                -webkit-mask-composite:xor;
                mask-composite:exclude;
                opacity:0;
                transition:opacity .35s ease;
            }
            .btn-login:hover{
                transform:translateY(-1px);
                border-color:transparent;
                box-shadow:0 10px 28px -10px var(--amber-glow);
            }
            .btn-login:hover::before{ opacity:1; }
            .btn-login svg{ width:14px; height:14px; opacity:.8; }

            .btn-primary{
                display:inline-flex;
                align-items:center;
                gap:10px;
                font-weight:600;
                font-size:15px;
                color:#161311;
                text-decoration:none;
                padding:13px 26px;
                border-radius:10px;
                background:linear-gradient(135deg, #f3a668, var(--amber) 55%, var(--amber-deep));
                box-shadow:0 14px 30px -12px var(--amber-glow);
                transition:transform .25s ease, box-shadow .25s ease;
            }
            .btn-primary:hover{
                transform:translateY(-2px);
                box-shadow:0 18px 38px -12px var(--amber-glow);
            }
            .btn-secondary{
                display:inline-flex;
                align-items:center;
                font-weight:500;
                font-size:15px;
                color:var(--text);
                text-decoration:none;
                padding:13px 24px;
                border-radius:10px;
                border:1px solid var(--border);
                transition:border-color .25s ease, background .25s ease;
            }
            .btn-secondary:hover{ border-color:#3c434c; background:var(--surface); }

            /* ---- Hero ---- */
            .hero{
                padding:36px 0 80px;
                display:grid;
                grid-template-columns:1.15fr 0.85fr;
                gap:56px;
                align-items:center;
            }
            .eyebrow{
                display:inline-flex;
                align-items:center;
                gap:8px;
                font-family:'JetBrains Mono', monospace;
                font-size:11px;
                letter-spacing:.16em;
                text-transform:uppercase;
                color:var(--amber);
                margin-bottom:22px;
            }
            .eyebrow .dot{
                width:6px; height:6px; border-radius:50%;
                background:var(--amber);
                box-shadow:0 0 0 4px var(--amber-glow);
            }
            h1{
                font-family:'Space Grotesk', sans-serif;
                font-weight:600;
                font-size:clamp(38px, 5vw, 58px);
                line-height:1.06;
                letter-spacing:-0.01em;
                margin-bottom:22px;
            }
            h1 em{
                font-style:normal;
                color:var(--amber);
            }
            .hero p.lede{
                font-size:16.5px;
                line-height:1.65;
                color:var(--muted);
                max-width:480px;
                margin-bottom:34px;
            }
            .hero-ctas{ display:flex; gap:14px; margin-bottom:44px; flex-wrap:wrap; }

            .stat-row{
                display:flex;
                gap:36px;
                padding-top:26px;
                border-top:1px solid var(--border);
            }
            .stat b{
                display:block;
                font-family:'Space Grotesk', sans-serif;
                font-size:24px;
                font-weight:700;
                color:var(--text);
            }
            .stat span{
                font-family:'JetBrains Mono', monospace;
                font-size:10.5px;
                letter-spacing:.08em;
                text-transform:uppercase;
                color:var(--muted);
            }

            /* ---- Signature: extrusion panel ---- */
            .extrusion{
                position:relative;
                aspect-ratio:1/1.05;
                border-radius:20px;
                background:radial-gradient(120% 100% at 20% 0%, #23282e 0%, #171a1e 60%, #121417 100%);
                border:1px solid var(--border);
                overflow:hidden;
                display:flex;
                align-items:center;
                justify-content:center;
            }
            .granule-grid{
                position:absolute; inset:0;
                display:grid;
                grid-template-columns:repeat(9, 1fr);
                grid-template-rows:repeat(9, 1fr);
                padding:26px;
                gap:0;
            }
            .granule{
                width:5px; height:5px;
                margin:auto;
                border-radius:50%;
                background:#3a4048;
            }
            .granule.lit{ background:var(--amber); box-shadow:0 0 8px 1px var(--amber-glow); }

            .extrusion-line{
                position:absolute;
                left:0; right:0; top:50%;
                height:2px;
                background:linear-gradient(90deg, transparent, var(--amber), transparent);
                transform:translateY(-50%);
                filter:blur(.3px);
                animation:flow 3.2s ease-in-out infinite;
            }
            @keyframes flow{
                0%,100%{ opacity:.25; transform:translateY(-50%) scaleX(.7); }
                50%{ opacity:.9; transform:translateY(-50%) scaleX(1); }
            }
            .extrusion-label{
                position:relative; z-index:2;
                text-align:center;
                font-family:'JetBrains Mono', monospace;
                font-size:11px;
                letter-spacing:.14em;
                text-transform:uppercase;
                color:var(--muted);
                background:rgba(18,20,23,0.55);
                backdrop-filter:blur(6px);
                padding:14px 20px;
                border-radius:10px;
                border:1px solid var(--border);
            }
            .extrusion-label b{
                display:block;
                font-family:'Space Grotesk', sans-serif;
                font-size:15px;
                color:var(--text);
                letter-spacing:0;
                text-transform:none;
                margin-bottom:4px;
            }

            /* ---- Capability strip ---- */
            .strip{
                border-top:1px solid var(--border);
                padding:56px 0;
                display:grid;
                grid-template-columns:repeat(3, 1fr);
                gap:1px;
                background:var(--border);
            }
            .strip-item{
                background:var(--bg);
                padding:30px 34px;
            }
            .strip-item .num{
                font-family:'JetBrains Mono', monospace;
                font-size:11px;
                color:var(--amber);
                letter-spacing:.1em;
                margin-bottom:14px;
                display:block;
            }
            .strip-item h3{
                font-family:'Space Grotesk', sans-serif;
                font-size:18px;
                font-weight:600;
                margin-bottom:10px;
            }
            .strip-item p{
                font-size:14px;
                line-height:1.6;
                color:var(--muted);
            }

            footer{
                border-top:1px solid var(--border);
                padding:26px 0 34px;
                display:flex;
                justify-content:space-between;
                align-items:center;
                font-size:13px;
                color:var(--muted);
            }
            footer a{ color:var(--muted); text-decoration:none; }
            footer a:hover{ color:var(--amber); }

            @media (max-width: 860px){
                .hero{ grid-template-columns:1fr; }
                .strip{ grid-template-columns:1fr; }
                .nav-links{ gap:14px; }
                nav{ flex-wrap:wrap; gap:16px; }
            }
        </style>
    </head>
    <body>
        <div class="wrap">
            <nav>
                <div class="brand">
                    <div class="brand-name">AHUJA <span>PLASTICS</span></div>
                    <div class="brand-tag">Precision Injection Molding</div>
                </div>

                @if (Route::has('filament.admin.auth.login'))
                    <div class="nav-links">
                        <a href="#capabilities" class="plain">Capabilities</a>
                        @auth
                            <a href="{{ url('/admin') }}" class="btn-login">
                                Dashboard
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </a>
                        @else
                            <a href="{{ route('filament.admin.auth.login') }}" class="btn-login">
                                Log in
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </a>
                        @endauth
                    </div>
                @endif
            </nav>

            <section class="hero">
                <div>
                    <div class="eyebrow"><span class="dot"></span> Manufacturing since day one</div>
                    <h1>Molded for <em>precision</em>,<br>built to last.</h1>
                    <p class="lede">
                        Ahuja Plastics engineers and manufactures custom plastic components at scale —
                        from tooling and injection molding to finishing, delivered with consistent,
                        production-grade quality.
                    </p>
                    <div class="hero-ctas">
                        @if (Route::has('filament.admin.auth.login'))
                            @auth
                                <a href="{{ url('/admin') }}" class="btn-primary">
                                    Go to dashboard
                                </a>
                            @else
                                <a href="{{ route('filament.admin.auth.login') }}" class="btn-primary">
                                    Login
                                </a>
                            @endauth
                        @endif
                        <a href="#capabilities" class="btn-secondary">See capabilities</a>
                    </div>

                    <div class="stat-row">
                        <div class="stat"><b>25+</b><span>Years in operation</span></div>
                        <div class="stat"><b>500+</b><span>Molds delivered</span></div>
                        <div class="stat"><b>ISO</b><span>Certified process</span></div>
                    </div>
                </div>

                <div class="extrusion">
                    <div class="granule-grid" id="granuleGrid"></div>
                    <div class="extrusion-line"></div>
                    <div class="extrusion-label">
                        <b>Consistent tolerance</b>
                        Every batch, every run
                    </div>
                </div>
            </section>

            <section class="strip" id="capabilities">
                <div class="strip-item">
                    <span class="num">01</span>
                    <h3>Custom Molding</h3>
                    <p>End-to-end tooling and injection molding tailored to your product spec and volume.</p>
                </div>
                <div class="strip-item">
                    <span class="num">02</span>
                    <h3>Quality Control</h3>
                    <p>In-house inspection and testing to hold tight tolerances across every production run.</p>
                </div>
                <div class="strip-item">
                    <span class="num">03</span>
                    <h3>Bulk Supply</h3>
                    <p>Reliable, scheduled fulfillment for distributors and industrial clients at scale.</p>
                </div>
            </section>

            <footer>
                <span>&copy; {{ date('Y') }} Ahuja Plastics. All rights reserved.</span>
                <a href="mailto:contact@ahujaplastics.com">contact@ahujaplastics.com</a>
            </footer>
        </div>

        <script>
            // Light up a few granules randomly for a subtle "material" feel
            const grid = document.getElementById('granuleGrid');
            if (grid) {
                for (let i = 0; i < 81; i++) {
                    const g = document.createElement('div');
                    g.className = 'granule';
                    grid.appendChild(g);
                }
                const nodes = grid.querySelectorAll('.granule');
                setInterval(() => {
                    nodes.forEach(n => n.classList.remove('lit'));
                    const count = 5 + Math.floor(Math.random() * 4);
                    const used = new Set();
                    while (used.size < count) {
                        used.add(Math.floor(Math.random() * nodes.length));
                    }
                    used.forEach(i => nodes[i].classList.add('lit'));
                }, 1400);
            }
        </script>
    </body>
</html>