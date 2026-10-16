@php
    $primary = '#e50914';
    $secondary = '#ff4d55';
    $accent = '#b91c1c';
@endphp
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --ft-brand: {{ $primary }};
        --ft-brand-2: {{ $secondary }};
        --ft-brand-3: {{ $accent }};
        --brand: {{ $primary }};
        --brand-2: {{ $secondary }};
        --brand-3: {{ $accent }};
        --brand-soft: #3a1518;
        --ink: #f4f4f4;
        --ink-2: #e8e8e8;
        --ink-3: #c4c2c2;
        --soft: #c4c2c2;
        --muted: #8d8b8b;
        --ok-bg: #ecfdf5;
        --ok-text: #047857;
        --warn-bg: #fff7ed;
        --warn-text: #c2410c;
        --info-bg: #f0f9ff;
        --info-text: #0369a1;
        --danger-bg: #fff1f2;
        --danger-text: #be123c;
        --shadow: 0 10px 30px rgba(2, 44, 34, .05);
        --display: "Plus Jakarta Sans", Inter, "Segoe UI", sans-serif;
    }
    html, body, .fi-body {
        background: #000e17 !important;
        color: #f4f4f4 !important;
        font-family: Inter, "Plus Jakarta Sans", "Segoe UI", sans-serif !important;
    }
    body.fi-body { padding-left: 272px !important; }
    .fi-layout { display: flex !important; min-height: 100vh; background: #000e17 !important; }
    .fi-sidebar.fi-main-sidebar {
        display: flex !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        height: 100vh !important;
        width: 272px !important;
        min-width: 272px !important;
        flex: 0 0 272px !important;
        transform: none !important;
        translate: none !important;
        visibility: visible !important;
        opacity: 1 !important;
        background: #07141c !important;
        border-right: 1px solid #163042 !important;
        box-shadow: none !important;
        padding: 18px 12px 12px !important;
        z-index: 40 !important;
    }
    .fi-sidebar-close-overlay, .fi-topbar-open-sidebar-btn, .fi-topbar-close-sidebar-btn,
    .fi-layout-sidebar-toggle-btn-ctn, .fi-topbar-collapse-sidebar-btn-ctn,
    .fi-sidebar-open-collapse-sidebar-btn, .fi-sidebar-close-collapse-sidebar-btn { display: none !important; }
    .fi-main-ctn { flex: 1 1 auto !important; min-width: 0 !important; margin: 0 !important; width: auto !important; background: #000e17 !important; }
    .fi-main { padding: 22px !important; max-width: none !important; }
    .fi-topbar-ctn { position: sticky; top: 0; z-index: 20; }
    .fi-topbar {
        height: 72px !important;
        background: rgba(0, 14, 23, .94) !important;
        backdrop-filter: blur(16px);
        border-bottom: 1px solid #163042 !important;
        box-shadow: none !important;
        padding: 0 22px !important;
        ring: none !important;
    }
    .fi-header-heading, .fi-simple-header-heading {
        font-family: "Plus Jakarta Sans", Inter, sans-serif !important;
        letter-spacing: -.03em !important;
        color: #f4f4f4 !important;
        font-size: 22px !important;
        font-weight: 800 !important;
        line-height: 1.2 !important;
    }
    .fi-header { margin-bottom: 8px !important; }
    .ft-brand { display: flex; align-items: center; gap: 12px; padding: 4px 8px 8px; }
    .ft-mark {
        width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center;
        background: linear-gradient(135deg, var(--ft-brand-3), var(--ft-brand));
        color: #fff; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-size: 15px; font-weight: 800;
        box-shadow: 0 8px 18px rgba(229, 9, 20, .35);
    }
    .ft-brand strong { display: block; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-size: 16px; font-weight: 800; color: #e50914; line-height: 1.1; }
    .ft-brand small { display: block; color: #94a3b8; font-size: 11.5px; font-weight: 600; margin-top: 2px; }
    .fi-sidebar-header, .fi-sidebar-header-ctn, .fi-sidebar-header-logo-ctn {
        display: block !important;
        height: auto !important;
        width: auto !important;
        padding: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }
    .fi-sidebar .fi-logo { display: flex !important; height: auto !important; width: auto !important; }
    .fi-sidebar-nav { padding: 8px 0 !important; }
    .fi-sidebar-group-label {
        color: #94a3b8 !important; font-size: 10.5px !important; letter-spacing: .14em !important;
        font-weight: 600 !important; text-transform: uppercase !important;
    }
    .fi-sidebar-item-btn {
        border-radius: 12px !important;
        padding: 10px 12px !important;
        color: #c4c2c2 !important;
        font-size: 13.5px !important;
        font-weight: 600 !important;
    }
    .fi-sidebar-item-btn:hover { background: #10202a !important; color: #fff !important; }
    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn,
    .fi-sidebar-item.fi-sidebar-item-active > .fi-sidebar-item-btn {
        position: relative;
        background: #3a1518 !important;
        color: var(--ft-brand) !important;
        box-shadow: none !important;
    }
    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn::before {
        content: "";
        position: absolute;
        left: 0;
        top: 8px;
        bottom: 8px;
        width: 3px;
        border-radius: 0 4px 4px 0;
        background: var(--ft-brand);
    }
    .fi-sidebar-item-icon {
        width: 28px !important; height: 28px !important; border-radius: 8px !important;
        background: #10202a !important; color: #c4c2c2 !important; padding: 6px !important;
    }
    .fi-sidebar-item.fi-active .fi-sidebar-item-icon { background: #5a1820 !important; color: #fff !important; }
    .fi-topbar { display: flex !important; align-items: center !important; gap: 16px !important; }
    .fi-topbar-start, .fi-topbar .fi-logo { display: none !important; }
    .fi-topbar-end { flex: 1 1 auto !important; display: flex !important; align-items: center !important; gap: 10px !important; margin-left: 0 !important; min-width: 0 !important; }
    .fi-global-search-ctn { flex: 1 1 auto; display: flex; justify-content: center; min-width: 0; }
    .fi-global-search, .fi-global-search-ctn { width: min(640px, 100%) !important; max-width: 640px !important; margin-inline: auto !important; }
    .fi-global-search-field .fi-input-wrp {
        background: #0d1b24 !important;
        border: 1px solid #163042 !important;
        border-radius: 999px !important;
        box-shadow: none !important;
        min-height: 42px !important;
        padding-inline: 6px !important;
    }
    .fi-global-search-field input { border: 0 !important; box-shadow: none !important; background: transparent !important; color: #f4f4f4 !important; }
    .fi-global-search-field .fi-input-wrp-suffix { color: #94a3b8 !important; font-size: 11px !important; font-weight: 600 !important; }
    .fi-user-menu-trigger {
        background: #0d1b24 !important;
        border: 1px solid #163042 !important;
        border-right: 0 !important;
        border-radius: 14px 0 0 14px !important;
        padding: 6px 8px 6px 6px !important;
        height: 46px !important;
        box-shadow: none !important;
    }
    .ft-user-label {
        display: inline-flex;
        align-items: center;
        height: 46px;
        margin-left: -10px;
        padding: 0 14px 0 4px;
        background: #0d1b24;
        border: 1px solid #163042;
        border-left: 0;
        border-radius: 0 14px 14px 0;
        color: #f4f4f4;
        font-size: 12.5px;
        font-weight: 600;
        white-space: nowrap;
    }
    .fi-avatar, .fi-user-avatar {
        width: 32px !important;
        height: 32px !important;
        border-radius: 10px !important;
        background: #e50914 !important;
    }
    .fi-section, .fi-ta-ctn, .fi-wi-stats-overview-stat, .fi-fo-component-ctn {
        border-radius: 18px !important;
        border: 1px solid #163042 !important;
        box-shadow: none !important;
        background: #0d1b24;
    }
    .fi-wi-stats-overview .fi-section { background: transparent !important; border: 0 !important; box-shadow: none !important; }
    .fi-wi-stats-overview-stat {
        background: #0d1b24;
        min-height: 118px !important;
        position: relative;
        overflow: hidden;
        padding: 18px !important;
    }
    .fi-wi-stats-overview-stat::after {
        content: "";
        position: absolute;
        right: -24px;
        top: -24px;
        width: 104px;
        height: 104px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(229, 9, 20, .18), rgba(229, 9, 20, .04));
        pointer-events: none;
    }
    .fi-wi-stats-overview .fi-section-content > div .fi-wi-stats-overview-stat { background: #0d1b24 !important; border-color: #163042 !important; }
    .fi-wi-stats-overview-stat-value { font-family: "Plus Jakarta Sans", Inter, sans-serif !important; font-weight: 700 !important; letter-spacing: -.04em !important; font-size: 26px !important; color: #ffffff !important; }
    .fi-wi-stats-overview-stat-label { color: #c4c2c2 !important; font-size: 12.5px !important; font-weight: 500 !important; }
    .fi-ta-header-cell { font-size: 11px !important; letter-spacing: .06em !important; color: #94a3b8 !important; text-transform: uppercase !important; font-weight: 600 !important; }
    .fi-ta-cell { font-size: 13.5px !important; color: #e8e8e8 !important; }
    .fi-ta-row:hover { background: #10202a !important; }
    .fi-badge { border-radius: 999px !important; font-weight: 600 !important; font-size: 11px !important; }
    .mybaap-rev { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
    .mybaap-rev a {
        display: flex; align-items: center; min-height: 92px; border-radius: 16px; padding: 16px;
        text-decoration: none; color: inherit; border: 1px solid #eef1f8;
        box-shadow: 0 10px 30px rgba(2, 44, 34, .05);
    }
    .tone-brand, .tone-ok, .tone-info, .tone-warn { background: #0d1b24; border-color: #163042; }
    .fi-btn { border-radius: 12px !important; font-weight: 700 !important; }
    .fi-color-primary.fi-btn, .fi-btn.fi-color-primary {
        background: #e50914 !important;
        border: 0 !important;
        box-shadow: none !important;
    }
    .fi-input, .fi-select-input, .fi-fo-text-input input, .fi-fo-select select {
        border-radius: 12px !important;
        border-color: #163042 !important;
        background: #07141c !important;
        color: #f4f4f4 !important;
    }
    .fi-tabs-item.fi-active { background: linear-gradient(135deg, var(--ft-brand-3), var(--ft-brand-2)) !important; color: #fff !important; border-radius: 12px !important; }
    .fi-simple-layout { background: #000e17 !important; }
    .fi-simple-main { background: #0d1b24 !important; border: 1px solid #163042 !important; border-radius: 20px !important; box-shadow: none !important; color: #f4f4f4 !important; }
    @media (max-width: 1100px) { .mybaap-rev { grid-template-columns: 1fr 1fr; } }

    .fi-page:has(.ft-page) .fi-header { display: none !important; }
    .ft-page { color: var(--ink-2); font-family: Inter, "Plus Jakarta Sans", sans-serif; }
    .ft-page h1, .ft-page h2, .ft-page h3 { font-family: var(--display); color: var(--ink); letter-spacing: -.03em; }
    .ft-page .rpt-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 18px; }
    .ft-page .rpt-head h1 { margin: 0; font-size: 28px; font-weight: 800; letter-spacing: -.04em; }
    .ft-page .rpt-export {
        display: inline-flex; align-items: center; gap: 8px; width: auto;
        padding: 9px 14px; border-radius: 12px; border: 0;
        background: #e50914; color: #fff; font-weight: 700; font-size: 13.5px; cursor: pointer;
    }
    .ft-page .rpt-export svg { width: 16px; height: 16px; }
    .ft-page .rpt-filters { display: flex; justify-content: space-between; align-items: flex-end; gap: 16px; flex-wrap: wrap; margin-bottom: 16px; }
    .ft-page .rpt-filter-label { font-size: 12px; font-weight: 700; color: var(--ink-3); margin-bottom: 8px; }
    .ft-page .rpt-cats { display: flex; gap: 8px; }
    .ft-page .rpt-cat {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 72px; padding: 8px 16px; border-radius: 10px;
        border: 1px solid #163042; background: #0d1b24; color: var(--ink-2);
        font-weight: 700; font-size: 13.5px; cursor: pointer;
    }
    .ft-page .rpt-cat.active { background: var(--brand); border-color: var(--brand); color: #fff; }
    .ft-page .rpt-period {
        display: inline-flex; align-items: center; gap: 8px;
        border: 1px solid #163042; background: #07141c; border-radius: 10px; color: #f4f4f4;
        padding: 8px 12px; min-width: 240px;
    }
    .ft-page .rpt-period svg { width: 16px; height: 16px; color: var(--soft); flex: 0 0 16px; }
    .ft-page .rpt-period select { border: 0; background: transparent; font-weight: 700; color: var(--ink-2); width: 100%; padding: 0; outline: none; }
    .ft-page .rpt-charts { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
    .ft-page .rpt-charts .card { min-height: 320px; }
    .ft-page .card {
        background: #0d1b24; border: 1px solid #163042; border-radius: 18px;
        box-shadow: var(--shadow); padding: 20px;
    }
    .ft-page .card h3 { margin: 0 0 14px; font-size: 15px; font-weight: 600; color: var(--ink-3); }
    .ft-page .mt { margin-top: 16px; }
    .ft-page .grid { display: grid; gap: 16px; }
    .ft-page .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .ft-page .stat { position: relative; overflow: hidden; min-height: 118px; }
    .ft-page .stat::after {
        content: ""; position: absolute; right: -24px; top: -24px;
        width: 104px; height: 104px; border-radius: 50%;
        background: linear-gradient(135deg, rgba(229,9,20,.2), rgba(229,9,20,.05));
    }
    .ft-page .stat .label { color: var(--soft); font-size: 12.5px; font-weight: 500; }
    .ft-page .stat .value { font-family: var(--display); font-size: 26px; font-weight: 700; letter-spacing: -.04em; margin: 8px 0 4px; color: var(--ink); }
    .ft-page .table-wrap { overflow: auto; }
    .ft-page table { width: 100%; border-collapse: collapse; }
    .ft-page th, .ft-page td { text-align: left; padding: 13px 10px; border-bottom: 1px solid #163042; font-size: 13.5px; color: var(--ink-3); font-weight: 500; }
    .ft-page th { color: var(--muted); font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: .06em; }
    .ft-page td strong { font-weight: 600; color: var(--ink-2); }
    .ft-page tr:hover td { background: #10202a; }
    .ft-page .muted { color: var(--muted); }
    .ft-page .rpt-table-foot { margin: 12px 0 0; font-size: 12px; }
    .ft-page .kpi, .ft-page .rev-card {
        background: #0d1b24; border: 1px solid #163042; border-radius: 16px;
        padding: 16px; box-shadow: var(--shadow);
    }
    .ft-page .kpi-tone-brand, .ft-page .kpi-tone-ok, .ft-page .kpi-tone-info, .ft-page .kpi-tone-warn { background: #0d1b24; border-color: #163042; }
    .ft-page .kpi-ico {
        width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center;
        font-size: 18px; margin-bottom: 10px; background: var(--brand-soft); color: var(--brand);
    }
    .ft-page .kpi-label { font-size: 11.5px; font-weight: 600; color: var(--soft); }
    .ft-page .kpi-value { font-family: var(--display); font-size: 22px; font-weight: 800; letter-spacing: -.04em; color: var(--ink); margin-top: 4px; }
    .ft-page .dash-h { margin: 0 0 12px; font-size: 16px; font-weight: 700; }
    .ft-page .dash-rev { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
    .ft-page .rev-card { display: flex; align-items: center; gap: 14px; min-height: 92px; text-decoration: none; color: inherit; }
    .ft-page .rev-card .kpi-ico { margin: 0; flex: 0 0 44px; width: 44px; height: 44px; }
    .ft-page .rev-card strong { display: block; margin-top: 4px; font-size: 13.5px; color: var(--brand); }
    .ft-page .rev-card:hover { transform: translateY(-2px); border-color: #86efac; box-shadow: 0 10px 22px rgba(15, 23, 42, .10); }
    @media (max-width: 1100px) {
        .ft-page .rpt-charts, .ft-page .grid-4, .ft-page .dash-rev { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 720px) {
        .ft-page .rpt-charts, .ft-page .grid-4, .ft-page .dash-rev { grid-template-columns: 1fr; }
    }
    .ft-page .client-kicker { margin: 0; font-size: 11px; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; color: var(--brand); }
    .ft-page .badge { display: inline-flex; border-radius: 999px; padding: 5px 10px; font-size: 11px; font-weight: 700; background: var(--brand-soft); color: var(--brand); text-transform: capitalize; }
    .ft-page .badge.ok { background: var(--ok-bg); color: var(--ok-text); }
    .ft-page .badge.warn { background: var(--warn-bg); color: var(--warn-text); }
    .ft-page .badge.danger { background: var(--danger-bg); color: var(--danger-text); }
    .ft-page .badge.muted { background: #f1f5f9; color: var(--soft); }
    .ft-page .btn { border: 0; border-radius: 12px; padding: 10px 14px; cursor: pointer; font-weight: 700; font-size: 13.5px; background: #eef0f8; color: var(--ink-3); }
    .ft-page .btn-danger { background: var(--danger-bg); color: var(--danger-text); }
    .ft-page .btn-sm { padding: 7px 10px; font-size: 11.5px; border-radius: 10px; }
    .ft-page .input, .ft-page textarea, .ft-page select.input {
        width: 100%; border: 1px solid #163042; border-radius: 12px; padding: 10px 12px; background: #07141c; color: #f4f4f4;
    }
    .ft-page .kbd { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; background: var(--brand-soft); color: var(--brand); padding: 2px 6px; border-radius: 6px; font-size: 11.5px; font-weight: 700; }
    .ft-page .api-box { background: #04150d; color: #a7f3d0; border-radius: 14px; padding: 14px; overflow: auto; font-size: 12.5px; line-height: 1.55; margin: 0; }
    .ft-page .adoc-hero {
        display: flex; justify-content: space-between; gap: 18px; align-items: flex-start; flex-wrap: wrap;
        background: #0d1b24; border: 1px solid #163042; border-radius: 20px; padding: 22px 24px; box-shadow: var(--shadow); margin-bottom: 16px;
    }
    .ft-page .adoc-hero h1 { margin: 4px 0 8px; font-size: 24px; font-weight: 800; letter-spacing: -.03em; }
    .ft-page .adoc-hero p { max-width: 640px; color: var(--soft); margin: 0; }
    .ft-page .adoc-kpis { display: flex; gap: 10px; flex-wrap: wrap; }
    .ft-page .adoc-kpis div { min-width: 92px; background: var(--brand-soft); color: var(--brand); border-radius: 14px; padding: 12px 14px; text-align: center; }
    .ft-page .adoc-kpis strong { display: block; font-size: 22px; font-family: var(--display); }
    .ft-page .adoc-kpis span { font-size: 11px; font-weight: 700; color: var(--ink-3); }
    .ft-page .adoc-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
    .ft-page .adoc-meta h3 { margin: 0 0 8px; font-size: 13px; }
    .ft-page .adoc-toolbar { margin: 8px 0 18px; }
    .ft-page .adoc-toc { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
    .ft-page .adoc-toc a { font-size: 12px; font-weight: 700; color: var(--brand); text-decoration: none; background: var(--brand-soft); padding: 6px 10px; border-radius: 999px; }
    .ft-page .adoc-group { margin-bottom: 22px; scroll-margin-top: 80px; }
    .ft-page .adoc-group h2 { font-size: 18px; margin: 0 0 10px; }
    .ft-page .adoc-group h2 small { color: var(--muted); font-size: 12px; font-weight: 700; margin-left: 8px; }
    .ft-page .adoc-ep { background: #0d1b24; border: 1px solid #163042; border-radius: 14px; margin-bottom: 8px; box-shadow: var(--shadow); }
    .ft-page .adoc-ep summary { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; cursor: pointer; list-style: none; padding: 12px 14px; }
    .ft-page .adoc-ep summary::-webkit-details-marker { display: none; }
    .ft-page .adoc-ep summary code { font-size: 13px; font-weight: 700; color: var(--ink); }
    .ft-page .adoc-sum { flex: 1 1 220px; font-size: 12.5px; }
    .ft-page .adoc-m { font-size: 11px; font-weight: 800; letter-spacing: .04em; padding: 3px 8px; border-radius: 6px; min-width: 58px; text-align: center; }
    .ft-page .adoc-m-get { background: #dbeafe; color: #1d4ed8; }
    .ft-page .adoc-m-post { background: #dcfce7; color: #166534; }
    .ft-page .adoc-m-put, .ft-page .adoc-m-patch { background: #ffedd5; color: #c2410c; }
    .ft-page .adoc-m-delete { background: #fee2e2; color: #b91c1c; }
    .ft-page .adoc-auth { font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 999px; }
    .ft-page .adoc-auth.need { background: #f1f5f9; color: #475569; }
    .ft-page .adoc-auth.open { background: #ecfdf5; color: #047857; }
    .ft-page .adoc-body { padding: 0 14px 14px; border-top: 1px solid #f1f5f9; }
    .ft-page .adoc-body p { margin: 10px 0; }
    .ft-page .adoc-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .ft-page .adoc-grid h4 { margin: 4px 0 6px; font-size: 12px; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; }
    .ft-page .fi-section, .ft-page .fi-sc { background: transparent !important; border: 0 !important; box-shadow: none !important; padding: 0 !important; }
    @media (max-width: 960px) {
        .ft-page .adoc-meta, .ft-page .adoc-grid { grid-template-columns: 1fr; }
    }
    :root {
        --ft-brand: #e50914 !important;
        --brand: #e50914 !important;
        --brand-2: #f44336 !important;
        --brand-3: #ff4d55 !important;
        --brand-soft: #3a1518 !important;
        --ink: #f4f4f4 !important;
        --ink-2: #e8e8e8 !important;
        --soft: #c4c2c2 !important;
        --muted: #8d8b8b !important;
    }
    html, body, .fi-body, .fi-layout, .fi-main-ctn, .fi-main, .fi-simple-layout, .fi-simple-main {
        background: #010a0f !important;
        color: #f4f4f4 !important;
    }
    .fi-sidebar.fi-main-sidebar, .fi-sidebar {
        background: #071014 !important;
        border-right: 1px solid #1c262c !important;
        box-shadow: none !important;
    }
    .fi-sidebar-item-label, .fi-sidebar-group-label, .fi-sidebar-item-icon, .fi-topbar, .fi-header-heading, .fi-ta-header-cell {
        color: #f4f4f4 !important;
    }
    .fi-sidebar-item.fi-active, .fi-sidebar-item.fi-sidebar-item-active {
        background: #3a1518 !important;
        color: #e50914 !important;
    }
    .fi-topbar {
        background: rgba(1, 10, 15, .94) !important;
        border-bottom: 1px solid #1c262c !important;
    }
    .fi-section, .fi-wi-stats-overview-stat, .fi-ta-ctn, .fi-fo-component-ctn, .fi-modal-window, .fi-dropdown-panel {
        background: #10181d !important;
        border-color: #243038 !important;
        color: #f4f4f4 !important;
    }
    .fi-input, .fi-select-input, .fi-fo-field-wrp input, .fi-fo-field-wrp textarea, .fi-fo-field-wrp select {
        background: #071014 !important;
        color: #f4f4f4 !important;
        border-color: #243038 !important;
    }
    .fi-btn.fi-color-primary, .fi-btn-color-primary {
        background: #e50914 !important;
        border-color: #e50914 !important;
    }
    .fi-simple-page {
        background: #10181d !important;
        border: 1px solid #243038 !important;
        color: #f4f4f4 !important;
    }
    .fi-logo, .fi-simple-header-heading { color: #e50914 !important; }

    .fi-theme-switcher { display: none !important; }
    .fi-simple-layout {
        background: #010a0f !important;
        min-height: 100vh !important;
    }
    .fi-simple-main {
        width: min(420px, calc(100vw - 32px)) !important;
        margin: 32px auto !important;
        background: #141c22 !important;
        border: 0 !important;
        border-radius: 18px !important;
        box-shadow: 0 24px 60px rgba(0, 0, 0, .45) !important;
        color: #ffffff !important;
        padding: 28px 24px 22px !important;
    }
    .fi-simple-page {
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        padding: 0 !important;
        color: #ffffff !important;
    }
    .fi-simple-header { align-items: center !important; }
    .fi-simple-header .ft-brand { justify-content: center; }
    .fi-simple-header .ft-brand small { display: none; }
    .fi-simple-header-heading { font-size: 0 !important; }
    .fi-simple-header-heading::after {
        content: "Sign in";
        font-size: 28px;
        font-weight: 800;
        color: #e50914;
    }
    .fi-simple-page label,
    .fi-simple-page .fi-fo-field-wrp-label,
    .fi-simple-page .fi-fo-field-wrp-label span {
        color: #ffffff !important;
        font-weight: 700 !important;
    }
    .fi-simple-page .fi-input-wrp,
    .fi-simple-page .fi-fo-text-input,
    .fi-simple-page input,
    .fi-simple-page .fi-input {
        background: #0b1218 !important;
        color: #ffffff !important;
        border-color: #1c262c !important;
        border-radius: 8px !important;
    }
    .fi-simple-page input::placeholder { color: #8d8b8b !important; }
    .fi-simple-page .fi-btn.fi-color-primary {
        width: 100% !important;
        border-radius: 8px !important;
        min-height: 46px !important;
    }
    .mb-login-demo {
        margin-top: 18px;
        padding-top: 14px;
        border-top: 1px solid #243038;
        text-align: left;
    }
    .mb-login-demo h6 {
        margin: 0 0 10px;
        text-align: center;
        color: #ffffff;
        font-size: 14px;
        font-weight: 700;
    }
    .mb-login-demo p { margin: 0; color: #d7d5d5; font-size: 14px; line-height: 1.5; }

    .fi-input-wrp,
    .fi-fo-text-input,
    .fi-fo-textarea,
    .fi-fo-select,
    .fi-select-input,
    .fi-select-input-search-ctn,
    .filepond--root,
    .filepond--panel-root,
    .fi-fo-file-upload .filepond--panel-root {
        background: #071014 !important;
        background-color: #071014 !important;
        color: #f4f4f4 !important;
        border-color: #243038 !important;
    }
    .fi-input-wrp input,
    .fi-input-wrp textarea,
    .fi-input-wrp select,
    textarea {
        color: #f4f4f4 !important;
        -webkit-text-fill-color: #f4f4f4 !important;
        background: transparent !important;
        caret-color: #f4f4f4 !important;
    }
    .fi-input-wrp input::placeholder,
    .fi-input-wrp textarea::placeholder,
    .filepond--drop-label,
    .filepond--label-action {
        color: #8d8b8b !important;
    }
    .fi-input-wrp-prefix,
    .fi-input-wrp-suffix,
    .fi-input-wrp .fi-icon-btn {
        background: transparent !important;
        color: #f4f4f4 !important;
    }
    .fi-btn.fi-color-gray,
    .fi-btn.fi-color-secondary,
    .fi-ac-btn-action:not(.fi-color-primary) {
        background: #10181d !important;
        color: #f4f4f4 !important;
        border-color: #243038 !important;
    }
    .fi-checkbox-input {
        background: #071014 !important;
        border-color: #8d8b8b !important;
    }
</style>
