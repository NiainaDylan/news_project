<?php
$searchValue = isset($searchQuery) ? (string) $searchQuery : '';
$selectedCategoryValue = isset($selectedCategoryId) ? (string) $selectedCategoryId : '';
$selectedSourceValue = isset($selectedSourceId) ? (string) $selectedSourceId : '';
$selectedPeriodValue = isset($selectedPeriod) ? (string) $selectedPeriod : '';
$categoriesForFilter = isset($availableCategories) && is_array($availableCategories) ? $availableCategories : [];
$sourcesForFilter = isset($availableSources) && is_array($availableSources) ? $availableSources : [];
$categoryNavLinks = array_slice($categoriesForFilter, 0, 4, true);
$pageTitleMeta = isset($pageTitle) && trim((string)$pageTitle) !== '' ? (string)$pageTitle : 'ActuFlash - Actualites';
$metaDescriptionValue = isset($metaDescription) && trim((string)$metaDescription) !== ''
    ? (string)$metaDescription
    : 'Suivez les actualites en direct sur ActuFlash.';
$metaCanonicalValue = isset($metaCanonical) ? trim((string)$metaCanonical) : '';
$metaOgTypeValue = isset($metaOgType) && trim((string)$metaOgType) !== '' ? (string)$metaOgType : 'website';
$metaOgImageValue = isset($metaOgImage) ? trim((string)$metaOgImage) : '';
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitleMeta, ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescriptionValue, ENT_QUOTES, 'UTF-8'); ?>">
    <?php if ($metaCanonicalValue !== ''): ?>
        <link rel="canonical" href="<?php echo htmlspecialchars($metaCanonicalValue, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>
    <meta property="og:site_name" content="ActuFlash">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:type" content="<?php echo htmlspecialchars($metaOgTypeValue, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitleMeta, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($metaDescriptionValue, ENT_QUOTES, 'UTF-8'); ?>">
    <?php if ($metaCanonicalValue !== ''): ?>
        <meta property="og:url" content="<?php echo htmlspecialchars($metaCanonicalValue, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>
    <?php if ($metaOgImageValue !== ''): ?>
        <meta property="og:image" content="<?php echo htmlspecialchars($metaOgImageValue, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>
    <style>:root{--bg:#f4f6f8;--surface:#ffffff;--ink:#1c1f26;--muted:#5e6472;--brand:#d62828;--brand-dark:#a71d2a;--line:#dde2e8;--accent:#003049;--page-bg:linear-gradient(180deg, #f7f9fb 0%, #eef2f6 100%)}*{box-sizing:border-box;margin:0;padding:0}body{font-family:"Segoe UI",Tahoma,Geneva,Verdana,sans-serif;background:var(--page-bg);color:var(--ink);line-height:1.55}body.dark-mode{--bg:#0f1720;--surface:#111a24;--ink:#e6edf5;--muted:#a7b2c0;--brand:#ff5a5f;--brand-dark:#ff878a;--line:#2b3644;--accent:#7ec8ff;--page-bg:linear-gradient(180deg, #0b1118 0%, #121c28 100%)}a{color:inherit;text-decoration:none}.site-header{background:var(--surface);border-bottom:1px solid var(--line);position:sticky;top:0;z-index:10}.container{width:min(1120px, 92%);margin:0 auto}.header-top{display:flex;justify-content:space-between;align-items:center;padding:14px 0;gap:16px}.header-right{display:flex;align-items:center;gap:12px;margin-left:auto}.brand{font-size:1.5rem;font-weight:800;letter-spacing:.4px;color:var(--accent)}.brand span{color:var(--brand)}.header-meta{color:var(--muted);font-size:.95rem}.login-panel{display:flex;align-items:center;gap:8px;padding:6px 10px;border:1px solid var(--line);border-radius:999px;background:#f8fafc}.theme-toggle{border:1px solid var(--line);background:var(--surface);color:var(--ink);border-radius:999px;width:42px;height:42px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;position:relative;transition:transform 0.15s ease,background-color 0.2s ease}.theme-toggle:hover{transform:translateY(-1px)}.theme-icon{font-size:1.15rem;line-height:1;position:absolute;transition:opacity 0.2s ease,transform 0.2s ease}.theme-icon--sun{opacity:0;transform:scale(.6)}.theme-icon--moon{opacity:1;transform:scale(1)}body.dark-mode .theme-icon--sun{opacity:1;transform:scale(1)}body.dark-mode .theme-icon--moon{opacity:0;transform:scale(.6)}.avatar{width:30px;height:30px;border-radius:50%;display:grid;place-items:center;background:var(--accent);color:#fff;font-size:.85rem;font-weight:700}.login-btn{font-weight:700;font-size:.9rem;color:var(--brand-dark)}.main-nav{display:flex;gap:16px;padding:10px 0 14px;overflow-x:auto;align-items:center}.main-nav>a,.main-nav .dropdown-toggle{font-weight:600;color:#2d3340;white-space:nowrap;padding:6px 10px;border-radius:8px;transition:background-color 0.2s ease,color 0.2s ease}.main-nav>a:hover,.main-nav>a.active,.main-nav .dropdown:hover .dropdown-toggle{background:#ffe5e5;color:var(--brand-dark)}.dropdown{position:relative}.dropdown-toggle{cursor:pointer}.dropdown-menu{position:absolute;top:calc(100% + 8px);left:0;min-width:190px;background:#fff;border:1px solid var(--line);border-radius:10px;box-shadow:0 8px 24px rgb(0 0 0 / .08);display:none;padding:8px}.dropdown-menu a{display:block;padding:8px 10px;border-radius:8px;font-size:.95rem}.dropdown-menu a:hover{background:#f3f6f9;color:var(--brand-dark)}.dropdown:hover .dropdown-menu{display:block}.tools-bar{display:grid;grid-template-columns:1.7fr repeat(3,minmax(0,1fr)) auto;gap:10px;padding:0 0 14px}.tools-bar input,.tools-bar input[type="number"],.tools-bar select{width:100%;border:1px solid var(--line);border-radius:10px;padding:10px 12px;font-size:.92rem;background:#fff}.tools-bar button{border:0;border-radius:10px;background:var(--brand);color:#fff;font-weight:700;padding:10px 14px;cursor:pointer}main{padding:24px 0 42px}.hero{display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:24px}.card{background:var(--surface);border:1px solid var(--line);border-radius:14px;padding:18px}.hero-main{background:linear-gradient(125deg,#ffffff 0%,#fff1f1 100%)}.hero-main-link{display:block;color:inherit}.hero-media{width:100%;height:220px;border-radius:12px;object-fit:cover;margin-bottom:12px;display:block;background:#e9edf2}.tag{display:inline-block;padding:4px 10px;border-radius:999px;font-size:.78rem;font-weight:700;color:#fff;background:var(--brand);margin-bottom:10px}.hero-main h2{font-size:clamp(1.5rem, 3vw, 2rem);line-height:1.25;margin-bottom:10px}.hero-main p{color:var(--muted);margin-bottom:14px}.meta{color:#6b7383;font-size:.9rem}.side-list{display:grid;gap:12px}.side-item h3{font-size:1rem;margin-bottom:6px}.section-title{font-size:1.2rem;margin:14px 0}.search-page-title{text-align:center;font-size:clamp(1.4rem, 3.3vw, 2rem);margin:0 0 18px;color:var(--accent);letter-spacing:.3px}.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.article h3{font-size:1.05rem;margin-bottom:8px}.article-media{width:100%;height:150px;border-radius:10px;object-fit:cover;margin-bottom:10px;border:1px solid #e8edf2;display:block;background:#e9edf2}.article p{color:var(--muted);font-size:.96rem;margin-bottom:8px}.article a{font-weight:700;color:var(--brand-dark)}.image-expired-badge{display:inline-block;margin:8px 0 2px;padding:4px 10px;border-radius:999px;background:#fff1cc;color:#7a5300;border:1px solid #f2d28a;font-size:.78rem;font-weight:700;letter-spacing:.2px}.newsletter{margin-top:24px;display:grid;gap:10px;background:linear-gradient(140deg,#003049 0%,#1d4f6d 100%);color:#fff}body.dark-mode .login-panel,body.dark-mode .dropdown-menu,body.dark-mode .tools-bar input,body.dark-mode .tools-bar input[type="number"],body.dark-mode .tools-bar select,body.dark-mode .card{background:var(--surface);color:var(--ink);border-color:var(--line)}body.dark-mode .hero-main{background:linear-gradient(125deg,#131f2b 0%,#1d2b3a 100%)}body.dark-mode .main-nav>a,body.dark-mode .main-nav .dropdown-toggle,body.dark-mode .header-meta,body.dark-mode .meta,body.dark-mode .article p{color:var(--muted)}body.dark-mode .main-nav>a:hover,body.dark-mode .main-nav>a.active,body.dark-mode .main-nav .dropdown:hover .dropdown-toggle{background:#243446;color:#d9ecff}.newsletter input{border:0;border-radius:10px;padding:12px;width:100%;font-size:.95rem}.newsletter button{border:0;border-radius:10px;background:#f77f00;color:#1a1a1a;font-weight:700;padding:12px;cursor:pointer}.site-footer{background:#0f1720;color:#c2c8d2;margin-top:28px}.footer-content{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;padding:22px 0}.footer-content h4{color:#fff;margin-bottom:8px}.footer-bottom{border-top:1px solid #2b3644;padding:12px 0;font-size:.9rem;color:#9aa5b5}@media (max-width:900px){.hero,.grid,.footer-content{grid-template-columns:1fr}.header-top{flex-wrap:wrap}.header-right{width:100%;justify-content:space-between}.tools-bar{grid-template-columns:1fr}}</style>
    <title>ActuFlash - Actualites</title>
    <style>
        :root {
            --bg: #f4f6f8;
            --surface: #ffffff;
            --ink: #1c1f26;
            --muted: #5e6472;
            --brand: #d62828;
            --brand-dark: #a71d2a;
            --line: #dde2e8;
            --accent: #003049;
            --page-bg: linear-gradient(180deg, #f7f9fb 0%, #eef2f6 100%);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: var(--page-bg);
            color: var(--ink);
            line-height: 1.55;
        }

        body.dark-mode {
            --bg: #0f1720;
            --surface: #111a24;
            --ink: #e6edf5;
            --muted: #a7b2c0;
            --brand: #ff5a5f;
            --brand-dark: #ff878a;
            --line: #2b3644;
            --accent: #7ec8ff;
            --page-bg: linear-gradient(180deg, #0b1118 0%, #121c28 100%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .site-header {
            background: var(--surface);
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .container {
            width: min(1120px, 92%);
            margin: 0 auto;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 0;
            gap: 16px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: auto;
        }

        .brand {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: 0.4px;
            color: var(--accent);
        }

        .brand span {
            color: var(--brand);
        }

        .header-meta {
            color: var(--muted);
            font-size: 0.95rem;
        }

        .login-panel {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: #f8fafc;
        }

        .theme-toggle {
            border: 1px solid var(--line);
            background: var(--surface);
            color: var(--ink);
            border-radius: 999px;
            width: 42px;
            height: 42px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: transform 0.15s ease, background-color 0.2s ease;
        }

        .theme-toggle:hover {
            transform: translateY(-1px);
        }

        .theme-icon {
            font-size: 1.15rem;
            line-height: 1;
            position: absolute;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .theme-icon--sun {
            opacity: 0;
            transform: scale(0.6);
        }

        .theme-icon--moon {
            opacity: 1;
            transform: scale(1);
        }

        body.dark-mode .theme-icon--sun {
            opacity: 1;
            transform: scale(1);
        }

        body.dark-mode .theme-icon--moon {
            opacity: 0;
            transform: scale(0.6);
        }

        .avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: var(--accent);
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .login-btn {
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--brand-dark);
        }

        .main-nav {
            display: flex;
            gap: 16px;
            padding: 10px 0 14px;
            overflow-x: auto;
            align-items: center;
        }

        .main-nav > a,
        .main-nav .dropdown-toggle {
            font-weight: 600;
            color: #2d3340;
            white-space: nowrap;
            padding: 6px 10px;
            border-radius: 8px;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .main-nav > a:hover,
        .main-nav > a.active,
        .main-nav .dropdown:hover .dropdown-toggle {
            background: #ffe5e5;
            color: var(--brand-dark);
        }

        .dropdown {
            position: relative;
        }

        .dropdown-toggle {
            cursor: pointer;
        }

        .dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            min-width: 190px;
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            display: none;
            padding: 8px;
        }

        .dropdown-menu a {
            display: block;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 0.95rem;
        }

        .dropdown-menu a:hover {
            background: #f3f6f9;
            color: var(--brand-dark);
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        .tools-bar {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1fr auto;
            gap: 10px;
            padding: 0 0 14px;
        }

        .tools-bar input,
        .tools-bar select {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 0.92rem;
            background: #ffffff;
        }

        .tools-bar button {
            border: 0;
            border-radius: 10px;
            background: var(--brand);
            color: #ffffff;
            font-weight: 700;
            padding: 10px 14px;
            cursor: pointer;
        }

        main {
            padding: 24px 0 42px;
        }

        .hero {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 18px;
        }

        .hero-main {
            background: linear-gradient(125deg, #ffffff 0%, #fff1f1 100%);
        }

        .hero-media {
            /* width: 100%; */
            height: auto;
            border-radius: 12px;
            /* object-fit: contain; */
            margin-bottom: 12px;
            display: block;
            background: #e9edf2;
        }

        .tag {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #ffffff;
            background: var(--brand);
            margin-bottom: 10px;
        }

        .hero-main h2 {
            font-size: clamp(1.5rem, 3vw, 2rem);
            line-height: 1.25;
            margin-bottom: 10px;
        }

        .hero-main p {
            color: var(--muted);
            margin-bottom: 14px;
        }

        .meta {
            color: #6b7383;
            font-size: 0.9rem;
        }

        .side-list {
            display: grid;
            gap: 12px;
        }

        .side-item h3 {
            font-size: 1rem;
            margin-bottom: 6px;
        }

        .section-title {
            font-size: 1.2rem;
            margin: 14px 0;
        }

        .search-page-title {
            text-align: center;
            font-size: clamp(1.4rem, 3.3vw, 2rem);
            margin: 0 0 18px;
            color: var(--accent);
            letter-spacing: 0.3px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .article h3 {
            font-size: 1.05rem;
            margin-bottom: 8px;
        }

        .article-media {
            /* width: 100%; */
            height: auto;
            border-radius: 10px;
            /* object-fit: contain; */
            margin-bottom: 10px;
            border: 1px solid #e8edf2;
            display: block;
            background: #e9edf2;
        }

        .article p {
            color: var(--muted);
            font-size: 0.96rem;
            margin-bottom: 8px;
        }

        .article a {
            font-weight: 700;
            color: var(--brand-dark);
        }

        .image-expired-badge {
            display: inline-block;
            margin: 8px 0 2px;
            padding: 4px 10px;
            border-radius: 999px;
            background: #fff1cc;
            color: #7a5300;
            border: 1px solid #f2d28a;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .newsletter {
            margin-top: 24px;
            display: grid;
            gap: 10px;
            background: linear-gradient(140deg, #003049 0%, #1d4f6d 100%);
            color: #ffffff;
        }

        body.dark-mode .login-panel,
        body.dark-mode .dropdown-menu,
        body.dark-mode .tools-bar input,
        body.dark-mode .tools-bar select,
        body.dark-mode .card {
            background: var(--surface);
            color: var(--ink);
            border-color: var(--line);
        }

        body.dark-mode .hero-main {
            background: linear-gradient(125deg, #131f2b 0%, #1d2b3a 100%);
        }

        body.dark-mode .main-nav > a,
        body.dark-mode .main-nav .dropdown-toggle,
        body.dark-mode .header-meta,
        body.dark-mode .meta,
        body.dark-mode .article p {
            color: var(--muted);
        }

        body.dark-mode .main-nav > a:hover,
        body.dark-mode .main-nav > a.active,
        body.dark-mode .main-nav .dropdown:hover .dropdown-toggle {
            background: #243446;
            color: #d9ecff;
        }

        .newsletter input {
            border: 0;
            border-radius: 10px;
            padding: 12px;
            width: 100%;
            font-size: 0.95rem;
        }

        .newsletter button {
            border: 0;
            border-radius: 10px;
            background: #f77f00;
            color: #1a1a1a;
            font-weight: 700;
            padding: 12px;
            cursor: pointer;
        }

        .site-footer {
            background: #0f1720;
            color: #c2c8d2;
            margin-top: 28px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            padding: 22px 0;
        }

        .footer-content h4 {
            color: #ffffff;
            margin-bottom: 8px;
        }

        .footer-bottom {
            border-top: 1px solid #2b3644;
            padding: 12px 0;
            font-size: 0.9rem;
            color: #9aa5b5;
        }

        @media (max-width: 900px) {
            .hero,
            .grid,
            .footer-content {
                grid-template-columns: 1fr;
            }

            .header-top {
                flex-wrap: wrap;
            }

            .header-right {
                width: 100%;
                justify-content: space-between;
            }

            .tools-bar {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="container">
            <div class="header-top">
                <a href="/accueil" class="brand">Actu<span>Flash</span></a>
                <div class="header-right">
                    <div class="header-meta"><?php echo date('d/m/Y'); ?> | Edition France</div>
                    <button id="theme-toggle" class="theme-toggle" type="button" aria-pressed="false" aria-label="Activer le mode sombre" title="Activer le mode sombre">
                        <span class="theme-icon theme-icon--moon" aria-hidden="true">🌙</span>
                        <span class="theme-icon theme-icon--sun" aria-hidden="true">☀</span>
                    </button>
                    <div class="login-panel">
                        <span class="avatar">BO</span>
                        <a class="login-btn" href="/backoffice/?action=login">BackOffice</a>
                    </div>
                </div>
            </div>
            <nav class="main-nav">
                <a class="active" href="/accueil">Accueil</a>
                <div class="dropdown">
                    <span class="dropdown-toggle">Rubriques</span>
                    <div class="dropdown-menu">
                        <?php foreach ($categoryNavLinks as $categoryId => $categoryLabel): ?>
                            <a href="/accueil?id_categorie=<?php echo (int)$categoryId; ?>"><?php echo htmlspecialchars((string) $categoryLabel, ENT_QUOTES, 'UTF-8'); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- <a href="/backoffice/?action=article_list">Backoffice</a> -->
            </nav>
            <form class="tools-bar" action="/accueil" method="get">
                <input type="search" name="q" value="<?php echo htmlspecialchars($searchValue, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Rechercher: Iran, Téhéran, diplomatie, cessez-le-feu...">
                <select name="id_categorie">
                    <option value="">Categorie: Toutes</option>
                    <?php foreach ($categoriesForFilter as $categoryId => $categoryLabel): ?>
                        <option value="<?php echo (int)$categoryId; ?>" <?php echo $selectedCategoryValue === (string)$categoryId ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars((string)$categoryLabel, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="id_source">
                    <option value="">Source: Toutes</option>
                    <?php foreach ($sourcesForFilter as $sourceId => $sourceLabel): ?>
                        <option value="<?php echo (int)$sourceId; ?>" <?php echo $selectedSourceValue === (string)$sourceId ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars((string)$sourceLabel, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="periode">
                    <option value="mois_courant_auto" <?php echo $selectedPeriodValue === 'mois_courant_auto' ? 'selected' : ''; ?>>Periode: Mois actuel</option>
                    <option value="today" <?php echo $selectedPeriodValue === 'today' ? 'selected' : ''; ?>>Aujourd hui</option>
                    <option value="week" <?php echo $selectedPeriodValue === 'week' ? 'selected' : ''; ?>>Cette semaine</option>
                    <option value="month" <?php echo $selectedPeriodValue === 'month' ? 'selected' : ''; ?>>Ce mois</option>
                </select>
                <button type="submit">Filtrer</button>
            </form>
        </div>
    </header>
    <main>
        <div class="container">
