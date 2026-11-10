<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="FMZ India - Premium at-home skincare and aesthetic wellness. Coming soon.">
    <meta name="theme-color" content="#7a2e2e">
    <title>FMZ India | Coming Soon</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #fff8f5;
            --maroon: #7a2e2e;
            --maroon-dark: #52201f;
            --red: #b60a0a;
            --ink: #2b1514;
            --muted: #6e5957;
            --tan: #d9b48f;
            --border: #f1dfdc;
            --serif: "Cormorant Garamond", Georgia, serif;
            --sans: "Montserrat", system-ui, -apple-system, sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: var(--bg);
            color: var(--ink);
            font-family: var(--sans);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 40px 24px;
            text-align: center;
            overflow-x: hidden;
            position: relative;
        }

        /* Subtle glowing background aura */
        body::before {
            content: "";
            position: absolute;
            top: -150px;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(217, 180, 143, 0.22) 0%, rgba(255, 248, 245, 0) 70%);
            z-index: 0;
            pointer-events: none;
        }

        header, main, footer {
            position: relative;
            z-index: 1;
            max-width: 680px;
            width: 100%;
        }

        .logo-wrap img {
            max-width: 170px;
            height: auto;
            display: inline-block;
            filter: drop-shadow(0 2px 8px rgba(122, 46, 46, 0.08));
        }

        .badge {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: var(--maroon);
            background: rgba(122, 46, 46, 0.07);
            border: 1px solid rgba(122, 46, 46, 0.15);
            padding: 7px 18px;
            border-radius: 999px;
            margin-bottom: 24px;
        }

        h1 {
            font-family: var(--serif);
            font-size: clamp(2.4rem, 6vw, 4rem);
            font-weight: 500;
            line-height: 1.15;
            color: var(--maroon-dark);
            margin-bottom: 20px;
            letter-spacing: -0.01em;
        }

        h1 em {
            font-style: italic;
            color: var(--red);
        }

        .desc {
            font-size: clamp(0.95rem, 2vw, 1.1rem);
            line-height: 1.7;
            color: var(--muted);
            margin-bottom: 36px;
            font-weight: 400;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            justify-content: center;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 32px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            letter-spacing: 0.02em;
            transition: all 0.25s ease;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--red);
            color: #ffffff;
            border: 1.5px solid var(--red);
            box-shadow: 0 4px 16px rgba(182, 10, 10, 0.22);
        }

        .btn-primary:hover {
            background: var(--maroon);
            border-color: var(--maroon);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(122, 46, 46, 0.28);
        }

        .btn-outline {
            background: transparent;
            color: var(--maroon);
            border: 1.5px solid var(--maroon);
        }

        .btn-outline:hover {
            background: var(--maroon);
            color: #fff;
            transform: translateY(-2px);
        }

        footer {
            font-size: 0.8rem;
            color: var(--muted);
            padding-top: 40px;
        }

        footer a {
            color: var(--maroon);
            text-decoration: none;
            font-weight: 600;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo-wrap">
            <img src="/images/FMZ logo.jpg" alt="FMZ India" onerror="this.style.display='none'">
        </div>
    </header>

    <main>
        <div class="badge">Coming Soon</div>
        <h1>Something Extraordinary <em>is on the Way</em></h1>
        <p class="desc">
            We are crafting a transformative personal care and wellness experience. 
            In the meantime, you can explore our premier at-home skincare service: <strong>FMZ Glow</strong>.
        </p>

        <div class="actions">
            <a href="/glow" class="btn btn-primary">
                Explore FMZ Glow &rarr;
            </a>
            <a href="https://wa.me/919999999999" target="_blank" rel="noopener noreferrer" class="btn btn-outline">
                WhatsApp Us
            </a>
        </div>
    </main>

    <footer>
        &copy; {{ date('Y') }} FMZ India. All rights reserved. &bull; <a href="/glow">Visit FMZ Glow</a>
    </footer>
</body>
</html>

