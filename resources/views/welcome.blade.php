<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,520;9..144,640&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #14221f;
            --paper: #f3efe6;
            --card: #fffdf8;
            --teal: #0f6e6a;
            --teal-deep: #0b4f4c;
            --copper: #c46b3a;
            --line: rgba(20, 34, 31, 0.12);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ink);
            background:
                radial-gradient(900px 420px at 100% -10%, rgba(196, 107, 58, 0.18), transparent 60%),
                radial-gradient(700px 380px at -10% 110%, rgba(15, 110, 106, 0.16), transparent 55%),
                var(--paper);
            font-family: Outfit, sans-serif;
        }
        main {
            max-width: 1040px;
            margin: 0 auto;
            padding: 48px 24px 72px;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }
        .mark {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            font-size: 13px;
        }
        .mark span {
            width: 14px;
            height: 14px;
            border-radius: 4px;
            background: var(--teal);
            box-shadow: 8px 8px 0 var(--copper);
        }
        a.enter {
            color: var(--paper);
            background: var(--ink);
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 500;
        }
        h1 {
            font-family: Fraunces, serif;
            font-weight: 520;
            font-size: clamp(48px, 8vw, 92px);
            line-height: 0.92;
            letter-spacing: -0.04em;
            margin: 72px 0 20px;
            max-width: 12ch;
        }
        .lede {
            max-width: 46ch;
            font-size: 18px;
            line-height: 1.5;
            margin: 0 0 36px;
        }
        .actions { display: flex; gap: 12px; flex-wrap: wrap; }
        a.primary, a.secondary {
            text-decoration: none;
            border-radius: 999px;
            padding: 12px 18px;
            font-weight: 600;
        }
        a.primary { background: var(--teal); color: white; }
        a.secondary { background: transparent; color: var(--ink); border: 1px solid var(--line); }
        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 72px;
        }
        article {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 20px;
        }
        article strong { display: block; font-family: Fraunces, serif; font-size: 22px; margin-bottom: 8px; }
        article p { margin: 0; color: #3d4a46; line-height: 1.45; }
        footer { margin-top: 48px; color: #5c6a66; font-size: 14px; }
        @media (max-width: 800px) {
            .grid { grid-template-columns: 1fr; }
            h1 { margin-top: 48px; }
        }
    </style>
</head>
<body>
    <main>
        <header>
            <div class="mark"><span></span> Aruna Inventory</div>
            <a class="enter" href="{{ url('/admin/login') }}">Open admin</a>
        </header>
        <h1>Stock with a ledger, not a spreadsheet.</h1>
        <p class="lede">A Laravel and Filament starter for a small catalog: categories, products, and the movements that change what is on hand.</p>
        <div class="actions">
            <a class="primary" href="{{ url('/admin/login') }}">Sign in to the panel</a>
            <a class="secondary" href="https://github.com/EnriqueMuhammad/laravel-filament-starter">View the source</a>
        </div>
        <section class="grid">
            <article>
                <strong>Catalog</strong>
                <p>Products carry a SKU, a price, and a reorder level. Low stock shows up on the dashboard and in the navigation.</p>
            </article>
            <article>
                <strong>Ledger</strong>
                <p>Inbound, outbound, and adjustment movements recalculate on-hand stock. Outbound stops at zero.</p>
            </article>
            <article>
                <strong>Access</strong>
                <p>Filament Shield and Spatie permissions split a super admin, an inventory manager, and a read-only auditor.</p>
            </article>
        </section>
        <footer>Local demo only. Credentials live in the README and are not for a shared environment.</footer>
    </main>
</body>
</html>
