<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AESCALA — Arquitectura y Construcción | Cali, Colombia</title>
<meta name="description" content="Empresa líder en remodelación, construcción y carpintería en Cali. Más de 1.000 proyectos realizados. Arquitectos e ingenieros al servicio de tu hogar.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:ital,wght@0,300;0,400;0,600;0,700;1,300&family=Barlow+Condensed:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --orange: #E8450A;
    --orange-light: #FF5A1F;
    --orange-dim: rgba(232, 69, 10, 0.15);
    --black: #080808;
    --dark: #111111;
    --dark2: #181818;
    --gray: #888888;
    --gray-light: #AAAAAA;
    --white: #F5F3EF;
    --white-dim: rgba(245,243,239,0.08);
    --white-dim2: rgba(245,243,239,0.04);
    --border: rgba(245,243,239,0.10);
    --font-display: 'Bebas Neue', sans-serif;
    --font-cond: 'Barlow Condensed', sans-serif;
    --font-body: 'Barlow', sans-serif;
  }

  html { scroll-behavior: smooth; }

  body {
    background: var(--black);
    color: var(--white);
    font-family: var(--font-body);
    font-weight: 300;
    line-height: 1.6;
    overflow-x: hidden;
  }

  /* ─── SCROLLBAR ─── */
  ::-webkit-scrollbar { width: 4px; }
  ::-webkit-scrollbar-track { background: var(--dark); }
  ::-webkit-scrollbar-thumb { background: var(--orange); }

  /* ─── NAVBAR ─── */
  nav {
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 100;
    padding: 1.2rem 5vw;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: background 0.4s, backdrop-filter 0.4s;
  }
  nav.scrolled {
    background: rgba(8,8,8,0.92);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--border);
  }

  .nav-logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
  }
  .nav-logo svg {
    width: 36px;
    height: 36px;
  }
  .nav-logo-text {
    font-family: var(--font-display);
    font-size: 1.6rem;
    letter-spacing: 0.1em;
    color: var(--white);
  }
  .nav-logo-text span { color: var(--orange); }

  .nav-links {
    display: flex;
    gap: 2.5rem;
    list-style: none;
  }
  .nav-links a {
    font-family: var(--font-cond);
    font-size: 0.85rem;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--gray-light);
    text-decoration: none;
    transition: color 0.2s;
    position: relative;
  }
  .nav-links a::after {
    content: '';
    position: absolute;
    bottom: -4px; left: 0;
    width: 0; height: 1px;
    background: var(--orange);
    transition: width 0.3s;
  }
  .nav-links a:hover { color: var(--white); }
  .nav-links a:hover::after { width: 100%; }

  .nav-cta {
    font-family: var(--font-cond);
    font-size: 0.85rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--white);
    background: var(--orange);
    padding: 0.6rem 1.6rem;
    text-decoration: none;
    clip-path: polygon(8px 0%, 100% 0%, calc(100% - 8px) 100%, 0% 100%);
    transition: background 0.2s, transform 0.2s;
  }
  .nav-cta:hover { background: var(--orange-light); transform: translateY(-1px); }

  .nav-hamburger {
    display: none;
    flex-direction: column;
    gap: 5px;
    cursor: pointer;
    padding: 4px;
    background: none;
    border: none;
  }
  .nav-hamburger span {
    display: block;
    width: 24px; height: 2px;
    background: var(--white);
    transition: all 0.3s;
  }

  /* ─── HERO ─── */
  #hero {
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1fr 1fr;
    position: relative;
    overflow: hidden;
  }

  .hero-left {
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 12rem 5vw 6rem;
    position: relative;
    z-index: 2;
  }

  .hero-tag {
    font-family: var(--font-cond);
    font-size: 0.78rem;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: var(--orange);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.8rem;
    opacity: 0;
    animation: fadeUp 0.8s 0.3s forwards;
  }
  .hero-tag::before {
    content: '';
    display: block;
    width: 40px; height: 1px;
    background: var(--orange);
  }

  .hero-h1 {
    font-family: var(--font-display);
    font-size: clamp(5rem, 10vw, 9rem);
    line-height: 0.88;
    letter-spacing: 0.02em;
    color: var(--white);
    opacity: 0;
    animation: fadeUp 0.9s 0.5s forwards;
  }
  .hero-h1 em {
    color: var(--orange);
    font-style: normal;
  }

  .hero-sub {
    font-family: var(--font-body);
    font-weight: 300;
    font-size: 1.05rem;
    color: var(--gray-light);
    max-width: 420px;
    margin-top: 1.8rem;
    line-height: 1.7;
    opacity: 0;
    animation: fadeUp 0.8s 0.7s forwards;
  }

  .hero-actions {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-top: 2.8rem;
    opacity: 0;
    animation: fadeUp 0.8s 0.9s forwards;
  }

  .btn-primary {
    font-family: var(--font-cond);
    font-size: 0.9rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--white);
    background: var(--orange);
    padding: 1rem 2.5rem;
    text-decoration: none;
    clip-path: polygon(12px 0%, 100% 0%, calc(100% - 12px) 100%, 0% 100%);
    transition: all 0.25s;
    white-space: nowrap;
  }
  .btn-primary:hover { background: var(--orange-light); transform: translateY(-2px); }

  .btn-ghost {
    font-family: var(--font-cond);
    font-size: 0.9rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--gray-light);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: color 0.2s;
  }
  .btn-ghost:hover { color: var(--white); }
  .btn-ghost svg { transition: transform 0.2s; }
  .btn-ghost:hover svg { transform: translateX(4px); }

  .hero-stats {
    display: flex;
    gap: 3rem;
    margin-top: 4rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border);
    opacity: 0;
    animation: fadeUp 0.8s 1.1s forwards;
  }
  .stat-item {}
  .stat-num {
    font-family: var(--font-display);
    font-size: 2.4rem;
    color: var(--orange);
    line-height: 1;
  }
  .stat-label {
    font-family: var(--font-cond);
    font-size: 0.75rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--gray);
    margin-top: 0.3rem;
  }

  .hero-right {
    position: relative;
    overflow: hidden;
  }
  .hero-right::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(to right, var(--black) 0%, transparent 30%),
                linear-gradient(to top, var(--black) 0%, transparent 40%);
    z-index: 1;
  }
  .hero-img {
    width: 100%; height: 100%;
    object-fit: cover;
    opacity: 0.6;
    animation: zoomIn 1.5s 0.2s forwards;
    transform: scale(1.08);
  }

  .hero-accent {
    position: absolute;
    bottom: 5rem; right: 4vw;
    z-index: 2;
    text-align: right;
    opacity: 0;
    animation: fadeUp 0.8s 1.3s forwards;
  }
  .hero-accent-text {
    font-family: var(--font-display);
    font-size: 1rem;
    letter-spacing: 0.25em;
    color: var(--orange);
    text-transform: uppercase;
  }
  .hero-accent-sub {
    font-family: var(--font-cond);
    font-size: 0.75rem;
    color: var(--gray);
    letter-spacing: 0.15em;
    margin-top: 0.3rem;
  }

  /* Giant background letter */
  .hero-bg-letter {
    position: absolute;
    bottom: -2rem; left: 50%;
    transform: translateX(-50%);
    font-family: var(--font-display);
    font-size: 40vw;
    color: rgba(232,69,10,0.04);
    line-height: 1;
    z-index: 0;
    pointer-events: none;
    user-select: none;
  }

  /* ─── MARQUEE STRIP ─── */
  .marquee-strip {
    background: var(--orange);
    padding: 0.9rem 0;
    overflow: hidden;
    position: relative;
  }
  .marquee-track {
    display: flex;
    gap: 3rem;
    white-space: nowrap;
    animation: marquee 25s linear infinite;
    width: max-content;
  }
  .marquee-item {
    font-family: var(--font-cond);
    font-size: 0.8rem;
    letter-spacing: 0.25em;
    text-transform: uppercase;
    color: var(--black);
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  .marquee-item::before {
    content: '▲';
    font-size: 0.5rem;
    opacity: 0.5;
  }

  /* ─── SECTION GENERAL ─── */
  section { padding: 7rem 5vw; }

  .section-label {
    font-family: var(--font-cond);
    font-size: 0.75rem;
    letter-spacing: 0.35em;
    text-transform: uppercase;
    color: var(--orange);
    display: flex;
    align-items: center;
    gap: 0.8rem;
    margin-bottom: 1rem;
  }
  .section-label::before {
    content: '';
    display: block;
    width: 30px; height: 1px;
    background: var(--orange);
  }

  .section-title {
    font-family: var(--font-display);
    font-size: clamp(3rem, 6vw, 5.5rem);
    line-height: 0.92;
    letter-spacing: 0.02em;
    color: var(--white);
  }
  .section-title em { color: var(--orange); font-style: normal; }

  /* ─── QUIÉNES SOMOS ─── */
  #quienes {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6rem;
    align-items: center;
    background: var(--dark);
  }

  .quienes-left {}
  .quienes-body {
    font-size: 1.05rem;
    color: var(--gray-light);
    margin-top: 2rem;
    line-height: 1.8;
  }
  .quienes-body strong { color: var(--white); font-weight: 600; }

  .valores {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.6rem;
    margin-top: 2.5rem;
  }
  .valor-pill {
    font-family: var(--font-cond);
    font-size: 0.78rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--gray-light);
    padding: 0.5rem 1rem;
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
  }
  .valor-pill:hover {
    border-color: var(--orange);
    color: var(--orange);
    background: var(--orange-dim);
  }
  .valor-pill::before { content: '▸'; font-size: 0.6rem; color: var(--orange); }

  .quienes-right {}
  .mission-card {
    background: var(--dark2);
    border: 1px solid var(--border);
    border-left: 3px solid var(--orange);
    padding: 2rem 2rem 2rem 2.2rem;
    margin-bottom: 1.2rem;
  }
  .mission-card-label {
    font-family: var(--font-cond);
    font-size: 0.7rem;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: var(--orange);
    margin-bottom: 0.7rem;
  }
  .mission-card-text {
    font-size: 0.95rem;
    color: var(--gray-light);
    line-height: 1.7;
  }

  .team-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 0.8rem;
    margin-top: 2.5rem;
  }
  .team-stat {
    background: var(--orange-dim);
    border: 1px solid rgba(232,69,10,0.25);
    padding: 1.2rem;
    text-align: center;
  }
  .team-stat-num {
    font-family: var(--font-display);
    font-size: 2.2rem;
    color: var(--orange);
    line-height: 1;
  }
  .team-stat-label {
    font-family: var(--font-cond);
    font-size: 0.72rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--gray-light);
    margin-top: 0.4rem;
  }

  /* ─── SERVICIOS ─── */
  #servicios {
    background: var(--black);
  }

  .servicios-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 4rem;
    gap: 2rem;
  }

  .servicios-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1px;
    background: var(--border);
  }

  .servicio-card {
    background: var(--black);
    padding: 2.5rem 2rem;
    position: relative;
    overflow: hidden;
    transition: background 0.35s;
    cursor: default;
  }
  .servicio-card::before {
    content: '';
    position: absolute;
    bottom: 0; left: 0;
    width: 0; height: 2px;
    background: var(--orange);
    transition: width 0.4s;
  }
  .servicio-card:hover { background: var(--dark2); }
  .servicio-card:hover::before { width: 100%; }

  .servicio-icon {
    font-size: 2.2rem;
    margin-bottom: 1.2rem;
    display: block;
    line-height: 1;
  }
  .servicio-num {
    font-family: var(--font-display);
    font-size: 5rem;
    color: rgba(232,69,10,0.08);
    position: absolute;
    top: 1rem; right: 1.5rem;
    line-height: 1;
    transition: color 0.3s;
  }
  .servicio-card:hover .servicio-num { color: rgba(232,69,10,0.15); }

  .servicio-title {
    font-family: var(--font-cond);
    font-size: 1.4rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--white);
    margin-bottom: 0.8rem;
  }
  .servicio-desc {
    font-size: 0.88rem;
    color: var(--gray);
    line-height: 1.6;
  }

  .servicio-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    margin-top: 1.2rem;
  }
  .servicio-tag {
    font-family: var(--font-cond);
    font-size: 0.68rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--gray);
    border: 1px solid var(--border);
    padding: 0.2rem 0.6rem;
  }

  /* ─── PROCESO ─── */
  #proceso {
    background: var(--dark);
    position: relative;
  }

  .proceso-steps {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1px;
    background: var(--border);
    margin-top: 4rem;
  }

  .proceso-step {
    background: var(--dark);
    padding: 2.5rem 1.8rem;
    position: relative;
  }

  .paso-num {
    font-family: var(--font-display);
    font-size: 5rem;
    color: rgba(232,69,10,0.12);
    line-height: 1;
    margin-bottom: 0.5rem;
  }
  .paso-title {
    font-family: var(--font-cond);
    font-size: 1.1rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--white);
    margin-bottom: 0.8rem;
  }
  .paso-desc {
    font-size: 0.88rem;
    color: var(--gray);
    line-height: 1.6;
  }

  /* ─── PROYECTOS ─── */
  #proyectos {
    background: var(--black);
  }

  .proyectos-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 3.5rem;
    gap: 2rem;
  }

  .proyectos-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    grid-template-rows: auto auto;
    gap: 1px;
    background: var(--border);
  }

  .proyecto-card {
    position: relative;
    overflow: hidden;
    background: var(--dark2);
    min-height: 280px;
  }
  .proyecto-card:first-child {
    grid-row: 1 / 3;
    min-height: 560px;
  }

  .proyecto-bg {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    opacity: 0.5;
    transition: opacity 0.4s, transform 0.5s;
  }
  .proyecto-card:hover .proyecto-bg {
    opacity: 0.7;
    transform: scale(1.04);
  }

  .proyecto-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(8,8,8,0.95) 0%, transparent 60%);
  }

  .proyecto-content {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    padding: 2rem 1.8rem;
    z-index: 1;
  }

  .proyecto-cat {
    font-family: var(--font-cond);
    font-size: 0.68rem;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: var(--orange);
    margin-bottom: 0.5rem;
  }
  .proyecto-title {
    font-family: var(--font-display);
    font-size: 1.6rem;
    color: var(--white);
    line-height: 1.1;
  }
  .proyecto-card:first-child .proyecto-title { font-size: 2.2rem; }
  .proyecto-info {
    font-size: 0.82rem;
    color: var(--gray-light);
    margin-top: 0.5rem;
  }

  /* Placeholders visuales para proyectos */
  .proy-1 { background: linear-gradient(135deg, #1a1a1a 0%, #2a1a10 100%); }
  .proy-2 { background: linear-gradient(135deg, #0a0a0a 0%, #1a1205 100%); }
  .proy-3 { background: linear-gradient(135deg, #141414 0%, #0a1520 100%); }
  .proy-4 { background: linear-gradient(135deg, #1a0a0a 0%, #201015 100%); }

  /* Líneas decorativas internas */
  .proy-deco {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.15;
  }
  .proy-deco svg { width: 60%; height: 60%; }

  /* ─── ALIADOS ─── */
  #aliados {
    background: var(--dark);
    text-align: center;
  }
  .aliados-header { margin-bottom: 3.5rem; }
  .aliados-grid {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4rem;
    flex-wrap: wrap;
    margin-top: 3rem;
  }
  .aliado-item {
    font-family: var(--font-display);
    font-size: 1.4rem;
    color: var(--gray);
    letter-spacing: 0.1em;
    transition: color 0.2s;
    text-transform: uppercase;
  }
  .aliado-item:hover { color: var(--white); }

  /* ─── CONTACTO ─── */
  #contacto {
    background: var(--black);
    position: relative;
    overflow: hidden;
  }
  #contacto::before {
    content: 'AESCALA';
    position: absolute;
    bottom: -4rem;
    right: -2rem;
    font-family: var(--font-display);
    font-size: 22vw;
    color: rgba(232,69,10,0.04);
    pointer-events: none;
    line-height: 1;
  }

  .contacto-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6rem;
    align-items: start;
  }

  .contacto-left {}

  .contacto-info {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    margin-top: 3rem;
  }

  .info-row {
    display: flex;
    align-items: flex-start;
    gap: 1.2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--border);
  }
  .info-row:last-child { border-bottom: none; }
  .info-icon {
    width: 40px; height: 40px;
    background: var(--orange-dim);
    border: 1px solid rgba(232,69,10,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
  }
  .info-label {
    font-family: var(--font-cond);
    font-size: 0.7rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--orange);
    margin-bottom: 0.3rem;
  }
  .info-value {
    font-size: 0.95rem;
    color: var(--white);
  }
  .info-value a {
    color: var(--white);
    text-decoration: none;
    transition: color 0.2s;
  }
  .info-value a:hover { color: var(--orange); }

  .contacto-right {}

  .contact-form {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
    position: relative;
    z-index: 1;
  }

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }

  .form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  .form-label {
    font-family: var(--font-cond);
    font-size: 0.72rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--gray);
  }
  .form-input, .form-select, .form-textarea {
    background: var(--dark2);
    border: 1px solid var(--border);
    color: var(--white);
    padding: 0.85rem 1rem;
    font-family: var(--font-body);
    font-size: 0.9rem;
    font-weight: 300;
    outline: none;
    transition: border-color 0.2s;
    width: 100%;
    appearance: none;
  }
  .form-input::placeholder, .form-textarea::placeholder { color: var(--gray); }
  .form-input:focus, .form-select:focus, .form-textarea:focus {
    border-color: var(--orange);
  }
  .form-select {
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23888' stroke-width='1.5' fill='none'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    padding-right: 2.5rem;
  }
  .form-select option { background: var(--dark2); }
  .form-textarea { resize: vertical; min-height: 120px; }

  .btn-submit {
    font-family: var(--font-cond);
    font-size: 0.9rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--white);
    background: var(--orange);
    border: none;
    padding: 1.1rem 2.5rem;
    cursor: pointer;
    clip-path: polygon(12px 0%, 100% 0%, calc(100% - 12px) 100%, 0% 100%);
    transition: all 0.25s;
    align-self: flex-start;
  }
  .btn-submit:hover { background: var(--orange-light); transform: translateY(-2px); }

  /* ─── ZONAS ─── */
  .zonas-strip {
    background: var(--orange);
    padding: 1.5rem 5vw;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
    flex-wrap: wrap;
  }
  .zonas-label {
    font-family: var(--font-cond);
    font-size: 0.75rem;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: rgba(0,0,0,0.6);
    font-weight: 700;
  }
  .zonas-list {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
  }
  .zona-item {
    font-family: var(--font-display);
    font-size: 1.1rem;
    color: var(--black);
    letter-spacing: 0.08em;
  }
  .zona-item.highlight { color: rgba(0,0,0,0.45); }

  /* ─── FOOTER ─── */
  footer {
    background: var(--dark);
    padding: 3rem 5vw 2rem;
    border-top: 1px solid var(--border);
  }
  .footer-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 4rem;
    margin-bottom: 3rem;
  }
  .footer-brand {}
  .footer-logo {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    text-decoration: none;
    margin-bottom: 1rem;
  }
  .footer-logo-text {
    font-family: var(--font-display);
    font-size: 1.5rem;
    color: var(--white);
    letter-spacing: 0.1em;
  }
  .footer-logo-text span { color: var(--orange); }
  .footer-tagline {
    font-family: var(--font-cond);
    font-size: 0.75rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--gray);
    margin-bottom: 1.5rem;
  }
  .footer-social {
    display: flex;
    gap: 0.8rem;
  }
  .social-btn {
    width: 36px; height: 36px;
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: var(--gray-light);
    text-decoration: none;
    transition: all 0.2s;
  }
  .social-btn:hover {
    border-color: var(--orange);
    color: var(--orange);
    background: var(--orange-dim);
  }

  .footer-col-title {
    font-family: var(--font-cond);
    font-size: 0.72rem;
    letter-spacing: 0.25em;
    text-transform: uppercase;
    color: var(--orange);
    margin-bottom: 1.2rem;
  }
  .footer-links {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
  }
  .footer-links a {
    font-size: 0.88rem;
    color: var(--gray);
    text-decoration: none;
    transition: color 0.2s;
  }
  .footer-links a:hover { color: var(--white); }

  .footer-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 2rem;
    border-top: 1px solid var(--border);
    gap: 1rem;
    flex-wrap: wrap;
  }
  .footer-copy {
    font-size: 0.8rem;
    color: var(--gray);
  }
  .footer-copy span { color: var(--orange); }

  /* ─── MOBILE NAV OPEN ─── */
  .mobile-nav {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(8,8,8,0.98);
    z-index: 99;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2.5rem;
  }
  .mobile-nav.open { display: flex; }
  .mobile-nav a {
    font-family: var(--font-display);
    font-size: 3rem;
    color: var(--white);
    text-decoration: none;
    letter-spacing: 0.05em;
    transition: color 0.2s;
  }
  .mobile-nav a:hover { color: var(--orange); }
  .mobile-nav-close {
    position: absolute;
    top: 2rem; right: 5vw;
    font-size: 2rem;
    color: var(--white);
    background: none;
    border: none;
    cursor: pointer;
    transition: color 0.2s;
  }
  .mobile-nav-close:hover { color: var(--orange); }

  /* ─── ANIMATIONS ─── */
  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(30px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  @keyframes zoomIn {
    to { transform: scale(1); }
  }
  @keyframes marquee {
    from { transform: translateX(0); }
    to   { transform: translateX(-50%); }
  }

  /* Scroll reveal */
  .reveal {
    opacity: 0;
    transform: translateY(40px);
    transition: opacity 0.7s ease, transform 0.7s ease;
  }
  .reveal.visible {
    opacity: 1;
    transform: translateY(0);
  }
  .reveal-d1 { transition-delay: 0.1s; }
  .reveal-d2 { transition-delay: 0.2s; }
  .reveal-d3 { transition-delay: 0.3s; }
  .reveal-d4 { transition-delay: 0.4s; }

  /* ─── WHATSAPP BUTTON ─── */
  .wa-btn {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    z-index: 50;
    width: 54px; height: 54px;
    background: #25D366;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    box-shadow: 0 4px 20px rgba(37, 211, 102, 0.35);
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .wa-btn:hover {
    transform: scale(1.08) translateY(-2px);
    box-shadow: 0 8px 28px rgba(37, 211, 102, 0.45);
  }
  .wa-btn svg { width: 28px; height: 28px; }

  /* ─── RESPONSIVE ─── */
  @media (max-width: 1024px) {
    #hero { grid-template-columns: 1fr; }
    .hero-right { height: 50vh; }
    .hero-right::before {
      background: linear-gradient(to bottom, var(--black) 0%, transparent 30%),
                  linear-gradient(to top, var(--black) 0%, transparent 50%);
    }
    .hero-left { padding-top: 8rem; padding-bottom: 3rem; }
    #quienes { grid-template-columns: 1fr; gap: 3rem; }
    .servicios-grid { grid-template-columns: repeat(2, 1fr); }
    .proceso-steps { grid-template-columns: 1fr 1fr; }
    .contacto-grid { grid-template-columns: 1fr; gap: 3rem; }
    .footer-grid { grid-template-columns: 1fr 1fr; gap: 2rem; }
    .proyectos-grid { grid-template-columns: 1fr 1fr; }
    .proyecto-card:first-child { grid-column: 1 / 3; grid-row: auto; }
  }

  @media (max-width: 768px) {
    .nav-links, .nav-cta { display: none; }
    .nav-hamburger { display: flex; }
    .hero-h1 { font-size: clamp(3.5rem, 14vw, 5rem); }
    .hero-stats { gap: 1.5rem; }
    .servicios-grid { grid-template-columns: 1fr; }
    .proceso-steps { grid-template-columns: 1fr; }
    .servicios-header { flex-direction: column; align-items: flex-start; }
    .proyectos-header { flex-direction: column; align-items: flex-start; }
    .proyectos-grid { grid-template-columns: 1fr; }
    .proyecto-card:first-child { grid-column: auto; }
    .form-row { grid-template-columns: 1fr; }
    .footer-grid { grid-template-columns: 1fr; gap: 2rem; }
    .valores { grid-template-columns: 1fr; }
    .team-grid { grid-template-columns: 1fr; }
    section { padding: 5rem 5vw; }
    .hero-left { padding: 7rem 5vw 3rem; }
    .hero-accent { display: none; }
  }

  @media (max-width: 480px) {
    .hero-actions { flex-direction: column; align-items: flex-start; }
    .hero-stats { flex-wrap: wrap; gap: 1rem; }
    .zonas-strip { justify-content: center; }
    .footer-bottom { flex-direction: column; text-align: center; }
  }
</style>
</head>
<body>

<!-- ─── MOBILE NAV ─── -->
<div class="mobile-nav" id="mobileNav">
  <button class="mobile-nav-close" onclick="toggleMobileNav()">✕</button>
  <a href="#quienes" onclick="toggleMobileNav()">Nosotros</a>
  <a href="#servicios" onclick="toggleMobileNav()">Servicios</a>
  <a href="#proceso" onclick="toggleMobileNav()">Proceso</a>
  <a href="#proyectos" onclick="toggleMobileNav()">Proyectos</a>
  <a href="#contacto" onclick="toggleMobileNav()">Contacto</a>
</div>

<!-- ─── NAVBAR ─── -->
<nav id="navbar">
  <a href="#" class="nav-logo">
    <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M20 4L36 34H4L20 4Z" stroke="#E8450A" stroke-width="3" fill="none"/>
      <path d="M20 4L36 34" stroke="#E8450A" stroke-width="3"/>
      <line x1="10" y1="34" x2="30" y2="34" stroke="#E8450A" stroke-width="3"/>
    </svg>
    <span class="nav-logo-text">A<span>.</span>ESCALA</span>
  </a>

  <ul class="nav-links">
    <li><a href="#quienes">Nosotros</a></li>
    <li><a href="#servicios">Servicios</a></li>
    <li><a href="#proceso">Proceso</a></li>
    <li><a href="#proyectos">Proyectos</a></li>
  </ul>

  <a href="#contacto" class="nav-cta">Contáctanos</a>

  <button class="nav-hamburger" onclick="toggleMobileNav()" aria-label="Menú">
    <span></span><span></span><span></span>
  </button>
</nav>

<!-- ─── HERO ─── -->
<section id="hero">
  <div class="hero-bg-letter">A</div>

  <div class="hero-left">
    <p class="hero-tag">Cali — Colombia</p>
    <h1 class="hero-h1">
      CONS<em>TRUI</em><br>MOS<br>SUEÑOS
    </h1>
    <p class="hero-sub">
      Empresa líder en remodelación, construcción y carpintería en Cali.
      Más de <strong>1.000 proyectos</strong> entregados con excelencia y cumplimiento.
    </p>
    <div class="hero-actions">
      <a href="#contacto" class="btn-primary">Cotizar ahora</a>
      <a href="#proyectos" class="btn-ghost">
        Ver proyectos
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
          <path d="M3 8h10M9 4l4 4-4 4" stroke="#E8450A" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
      </a>
    </div>
    <div class="hero-stats">
      <div class="stat-item">
        <div class="stat-num">+1K</div>
        <div class="stat-label">Proyectos</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">+15</div>
        <div class="stat-label">Arquitectos</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">200+</div>
        <div class="stat-label">Obras simultáneas</div>
      </div>
    </div>
  </div>

  <div class="hero-right">
    <!-- Visual abstracto arquitectónico -->
    <svg style="width:100%;height:100%;position:absolute;inset:0;" viewBox="0 0 600 700" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
      <!-- Fondo -->
      <rect width="600" height="700" fill="#0d0d0d"/>
      <!-- Líneas arquitectónicas perspectiva -->
      <line x1="300" y1="80" x2="50" y2="600" stroke="rgba(232,69,10,0.2)" stroke-width="1"/>
      <line x1="300" y1="80" x2="550" y2="600" stroke="rgba(232,69,10,0.2)" stroke-width="1"/>
      <line x1="300" y1="80" x2="300" y2="650" stroke="rgba(232,69,10,0.1)" stroke-width="0.5"/>
      <!-- Grid de perspectiva -->
      <line x1="150" y1="340" x2="50" y2="600" stroke="rgba(245,243,239,0.04)" stroke-width="0.5"/>
      <line x1="200" y1="210" x2="50" y2="600" stroke="rgba(245,243,239,0.04)" stroke-width="0.5"/>
      <line x1="450" y1="340" x2="550" y2="600" stroke="rgba(245,243,239,0.04)" stroke-width="0.5"/>
      <line x1="400" y1="210" x2="550" y2="600" stroke="rgba(245,243,239,0.04)" stroke-width="0.5"/>
      <!-- Horizontales perspectiva -->
      <line x1="50" y1="600" x2="550" y2="600" stroke="rgba(245,243,239,0.06)" stroke-width="0.5"/>
      <line x1="150" y1="340" x2="450" y2="340" stroke="rgba(245,243,239,0.04)" stroke-width="0.5"/>
      <line x1="200" y1="210" x2="400" y2="210" stroke="rgba(245,243,239,0.04)" stroke-width="0.5"/>
      <!-- Triángulo grande naranja -->
      <polygon points="300,60 520,580 80,580" fill="none" stroke="#E8450A" stroke-width="2.5" opacity="0.7"/>
      <!-- Triángulo interior -->
      <polygon points="300,120 460,540 140,540" fill="none" stroke="rgba(232,69,10,0.3)" stroke-width="1"/>
      <!-- Punto de fuga -->
      <circle cx="300" cy="80" r="4" fill="#E8450A" opacity="0.8"/>
      <circle cx="300" cy="80" r="12" fill="none" stroke="#E8450A" stroke-width="0.5" opacity="0.4"/>
      <circle cx="300" cy="80" r="25" fill="none" stroke="#E8450A" stroke-width="0.3" opacity="0.2"/>
      <!-- Planta arquitectónica esquemática -->
      <g transform="translate(170, 380)" opacity="0.25">
        <rect x="0" y="0" width="260" height="180" fill="none" stroke="#E8450A" stroke-width="1.5"/>
        <rect x="0" y="0" width="120" height="80" fill="rgba(232,69,10,0.1)" stroke="#E8450A" stroke-width="1"/>
        <rect x="140" y="0" width="120" height="80" fill="none" stroke="rgba(232,69,10,0.5)" stroke-width="0.5"/>
        <rect x="0" y="100" width="260" height="80" fill="none" stroke="rgba(232,69,10,0.5)" stroke-width="0.5"/>
        <line x1="60" y1="80" x2="60" y2="100" stroke="#E8450A" stroke-width="1"/>
        <line x1="200" y1="80" x2="200" y2="100" stroke="rgba(232,69,10,0.5)" stroke-width="0.5"/>
      </g>
      <!-- Cotas / medidas estilo plano -->
      <g opacity="0.2" stroke="rgba(245,243,239,0.5)" stroke-width="0.5">
        <line x1="80" y1="570" x2="520" y2="570"/>
        <line x1="80" y1="565" x2="80" y2="575"/>
        <line x1="520" y1="565" x2="520" y2="575"/>
      </g>
      <!-- Texto de plano -->
      <text x="300" y="568" text-anchor="middle" font-family="'Barlow Condensed',sans-serif" font-size="9" fill="rgba(245,243,239,0.2)" letter-spacing="3">FRENTE</text>
      <!-- Overlay gradiente -->
      <defs>
        <linearGradient id="heroGrad" x1="0" y1="0" x2="1" y2="0">
          <stop offset="0%" stop-color="#080808" stop-opacity="1"/>
          <stop offset="30%" stop-color="#080808" stop-opacity="0"/>
        </linearGradient>
        <linearGradient id="heroGrad2" x1="0" y1="1" x2="0" y2="0">
          <stop offset="0%" stop-color="#080808" stop-opacity="0.9"/>
          <stop offset="50%" stop-color="#080808" stop-opacity="0"/>
        </linearGradient>
      </defs>
      <rect width="600" height="700" fill="url(#heroGrad)"/>
      <rect width="600" height="700" fill="url(#heroGrad2)"/>
    </svg>

    <div class="hero-accent">
      <div class="hero-accent-text">AESCALA.CO</div>
      <div class="hero-accent-sub">Arquitectura y Construcción</div>
    </div>
  </div>
</section>

<!-- ─── MARQUEE ─── -->
<div class="marquee-strip">
  <div class="marquee-track" id="marqueeTrack">
    <span class="marquee-item">Remodelación</span>
    <span class="marquee-item">Construcción</span>
    <span class="marquee-item">Carpintería</span>
    <span class="marquee-item">Diseño de Interiores</span>
    <span class="marquee-item">Obra Civil</span>
    <span class="marquee-item">Electricidad</span>
    <span class="marquee-item">Drywall</span>
    <span class="marquee-item">Enchapes</span>
    <span class="marquee-item">Cocinas</span>
    <span class="marquee-item">Closets</span>
    <span class="marquee-item">Remodelación</span>
    <span class="marquee-item">Construcción</span>
    <span class="marquee-item">Carpintería</span>
    <span class="marquee-item">Diseño de Interiores</span>
    <span class="marquee-item">Obra Civil</span>
    <span class="marquee-item">Electricidad</span>
    <span class="marquee-item">Drywall</span>
    <span class="marquee-item">Enchapes</span>
    <span class="marquee-item">Cocinas</span>
    <span class="marquee-item">Closets</span>
  </div>
</div>

<!-- ─── QUIÉNES SOMOS ─── -->
<section id="quienes">
  <div class="quienes-left reveal">
    <p class="section-label">01 — Quiénes somos</p>
    <h2 class="section-title">CON AES<em>CALA</em><br>VAS A LA<br>FIJA</h2>
    <p class="quienes-body">
      AESCALA es una empresa de remodelaciones <strong>legalmente constituida</strong> en Cali,
      con alcance a municipios como Palmira, Yumbo, Jamundí y Candelaria.
      Contamos con <strong>más de 1.000 remodelaciones</strong> realizadas y capacidad
      para desarrollar más de 200 obras simultáneas.
    </p>
    <div class="valores">
      <div class="valor-pill">Liderazgo</div>
      <div class="valor-pill">Responsabilidad</div>
      <div class="valor-pill">Servicio</div>
      <div class="valor-pill">Innovación</div>
      <div class="valor-pill">Trabajo en equipo</div>
      <div class="valor-pill">Eficiencia</div>
    </div>
  </div>

  <div class="quienes-right reveal reveal-d2">
    <div class="mission-card">
      <div class="mission-card-label">Misión</div>
      <p class="mission-card-text">
        Construir los sueños de nuestros clientes plasmando sus gustos y anhelos en las obras,
        trabajando con un equipo altamente calificado y bajo los más estrictos estándares de calidad.
        Nos encargamos de personalizar los espacios con el mejor estilo y bajo el presupuesto acordado.
      </p>
    </div>
    <div class="mission-card">
      <div class="mission-card-label">Visión</div>
      <p class="mission-card-text">
        En 5 años ser la empresa líder de construcción y remodelación por preferencia de los caleños,
        resaltando nuestro profesionalismo y dedicación en cada proyecto.
        Capacitándonos e innovando constantemente en diseño y técnica.
      </p>
    </div>
    <div class="team-grid">
      <div class="team-stat">
        <div class="team-stat-num">+15</div>
        <div class="team-stat-label">Arquitectos</div>
      </div>
      <div class="team-stat">
        <div class="team-stat-num">+6</div>
        <div class="team-stat-label">Ingenieros</div>
      </div>
      <div class="team-stat">
        <div class="team-stat-num">+300</div>
        <div class="team-stat-label">Operativos</div>
      </div>
    </div>
  </div>
</section>

<!-- ─── SERVICIOS ─── -->
<section id="servicios">
  <div class="servicios-header">
    <div>
      <p class="section-label reveal">02 — Servicios</p>
      <h2 class="section-title reveal reveal-d1">NUES<em>TRAS</em><br>ESPECIALI<em>DADES</em></h2>
    </div>
    <p class="reveal reveal-d2" style="max-width:320px; color:var(--gray); font-size:0.9rem; line-height:1.7;">
      Cubrimos todo el ciclo de tu proyecto: desde el diseño hasta la entrega final,
      con los más altos estándares de calidad.
    </p>
  </div>

  <div class="servicios-grid">
    <div class="servicio-card reveal">
      <span class="servicio-num">01</span>
      <span class="servicio-icon">🏗️</span>
      <div class="servicio-title">Obra Civil</div>
      <p class="servicio-desc">Construcción completa, obra blanca, remodelación y ambientación de espacios residenciales y comerciales.</p>
      <div class="servicio-tags">
        <span class="servicio-tag">Construcción</span>
        <span class="servicio-tag">Obra Blanca</span>
        <span class="servicio-tag">Remodelación</span>
      </div>
    </div>
    <div class="servicio-card reveal reveal-d1">
      <span class="servicio-num">02</span>
      <span class="servicio-icon">🪵</span>
      <div class="servicio-title">Carpintería</div>
      <p class="servicio-desc">Instalación de cocinas integrales, closets, puertas, muebles modulares y carpintería arquitectónica a medida.</p>
      <div class="servicio-tags">
        <span class="servicio-tag">Cocinas</span>
        <span class="servicio-tag">Closets</span>
        <span class="servicio-tag">Modulares</span>
      </div>
    </div>
    <div class="servicio-card reveal reveal-d2">
      <span class="servicio-num">03</span>
      <span class="servicio-icon">✏️</span>
      <div class="servicio-title">Diseño</div>
      <p class="servicio-desc">Diseño arquitectónico, de interiores y exteriores, renders 3D, planos sanitarios, eléctricos y estructurales.</p>
      <div class="servicio-tags">
        <span class="servicio-tag">Interiores</span>
        <span class="servicio-tag">3D</span>
        <span class="servicio-tag">Planos</span>
      </div>
    </div>
    <div class="servicio-card reveal">
      <span class="servicio-num">04</span>
      <span class="servicio-icon">⚡</span>
      <div class="servicio-title">Electricidad</div>
      <p class="servicio-desc">Instalaciones eléctricas, perfilería en aluminio, iluminación LED, manejo de circuitos bajo norma RETIE.</p>
      <div class="servicio-tags">
        <span class="servicio-tag">LED</span>
        <span class="servicio-tag">Circuitos</span>
        <span class="servicio-tag">RETIE</span>
      </div>
    </div>
    <div class="servicio-card reveal reveal-d1">
      <span class="servicio-num">05</span>
      <span class="servicio-icon">🚿</span>
      <div class="servicio-title">Instalaciones</div>
      <p class="servicio-desc">Instalación de sanitarios, grifería, tubería, desagües, soportes y refuerzos hidráulicos y sanitarios.</p>
      <div class="servicio-tags">
        <span class="servicio-tag">Hidráulica</span>
        <span class="servicio-tag">Sanitarios</span>
        <span class="servicio-tag">Grifería</span>
      </div>
    </div>
    <div class="servicio-card reveal reveal-d2">
      <span class="servicio-num">06</span>
      <span class="servicio-icon">📋</span>
      <div class="servicio-title">Administrativo</div>
      <p class="servicio-desc">Trámite de licencias de reconocimiento, formulación de proyectos y asesoría en construcción y POT.</p>
      <div class="servicio-tags">
        <span class="servicio-tag">Licencias</span>
        <span class="servicio-tag">POT</span>
        <span class="servicio-tag">Asesoría</span>
      </div>
    </div>
  </div>
</section>

<!-- ─── PROCESO ─── -->
<section id="proceso">
  <p class="section-label reveal">03 — Metodología</p>
  <h2 class="section-title reveal reveal-d1">¿CÓMO <em>TRABA</em><br>JAMOS?</h2>

  <div class="proceso-steps">
    <div class="proceso-step reveal">
      <div class="paso-num">01</div>
      <div class="paso-title">Diseño</div>
      <p class="paso-desc">Nuestro equipo de arquitectos materializa tu visión en planos y renders 3D, adaptados a tu presupuesto y gustos.</p>
    </div>
    <div class="proceso-step reveal reveal-d1">
      <div class="paso-num">02</div>
      <div class="paso-title">Materializamos la obra</div>
      <p class="paso-desc">Ejecutamos el proyecto con profesionales altamente calificados, garantizando calidad en cada detalle de la construcción.</p>
    </div>
    <div class="proceso-step reveal reveal-d2">
      <div class="paso-num">03</div>
      <div class="paso-title">Control de materiales</div>
      <p class="paso-desc">Manejamos nuestra propia bodega y planta de corte. Control de calidad estandarizado y precios de distribución.</p>
    </div>
    <div class="proceso-step reveal reveal-d3">
      <div class="paso-num">04</div>
      <div class="paso-title">Cumplimiento de plazos</div>
      <p class="paso-desc">Ingenieros de control reaccionan a cada requerimiento en tiempo real, garantizando entrega puntual con garantía de 1 año.</p>
    </div>
  </div>
</section>

<!-- ─── PROYECTOS ─── -->
<section id="proyectos">
  <div class="proyectos-header">
    <div>
      <p class="section-label reveal">04 — Portafolio</p>
      <h2 class="section-title reveal reveal-d1">PROYEC<em>TOS</em><br>DESTA<em>CADOS</em></h2>
    </div>
    <a href="mailto:a.escalacomercial@gmail.com" class="btn-ghost reveal reveal-d2">
      Ver portafolio completo
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
        <path d="M3 8h10M9 4l4 4-4 4" stroke="#E8450A" stroke-width="1.5" stroke-linecap="round"/>
      </svg>
    </a>
  </div>

  <div class="proyectos-grid">
    <!-- Proyecto grande -->
    <div class="proyecto-card reveal">
      <div class="proy-deco">
        <svg viewBox="0 0 300 300" xmlns="http://www.w3.org/2000/svg">
          <rect x="20" y="20" width="260" height="260" fill="none" stroke="#E8450A" stroke-width="1" opacity="0.4"/>
          <rect x="20" y="20" width="120" height="120" fill="rgba(232,69,10,0.15)" stroke="#E8450A" stroke-width="1" opacity="0.6"/>
          <rect x="160" y="20" width="120" height="120" fill="none" stroke="rgba(232,69,10,0.3)" stroke-width="0.5" opacity="0.4"/>
          <rect x="20" y="160" width="260" height="120" fill="none" stroke="rgba(232,69,10,0.3)" stroke-width="0.5" opacity="0.4"/>
          <line x1="20" y1="140" x2="280" y2="140" stroke="#E8450A" stroke-width="0.5" opacity="0.3"/>
          <line x1="140" y1="20" x2="140" y2="280" stroke="rgba(232,69,10,0.3)" stroke-width="0.5" opacity="0.3"/>
          <circle cx="80" cy="80" r="30" fill="none" stroke="#E8450A" stroke-width="0.5" opacity="0.3"/>
          <circle cx="220" cy="220" r="40" fill="none" stroke="rgba(232,69,10,0.4)" stroke-width="0.5" opacity="0.3"/>
        </svg>
      </div>
      <div class="proyecto-overlay"></div>
      <div class="proyecto-content">
        <div class="proyecto-cat">RMD009 — Carpintería & Remodelación</div>
        <div class="proyecto-title">Sala familiar<br>Lili · Safiro</div>
        <div class="proyecto-info">Panel yeso · Iluminación LED · Muebles modulares</div>
      </div>
    </div>

    <!-- Proyecto 2 -->
    <div class="proyecto-card reveal reveal-d1">
      <div class="proy-deco">
        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
          <rect x="10" y="10" width="180" height="180" fill="none" stroke="#E8450A" stroke-width="1" opacity="0.3"/>
          <line x1="10" y1="10" x2="190" y2="190" stroke="#E8450A" stroke-width="0.5" opacity="0.2"/>
          <rect x="40" y="40" width="120" height="120" fill="rgba(232,69,10,0.1)" stroke="#E8450A" stroke-width="0.5" opacity="0.4"/>
        </svg>
      </div>
      <div class="proyecto-overlay"></div>
      <div class="proyecto-content">
        <div class="proyecto-cat">RMD005 — Carpintería</div>
        <div class="proyecto-title">Cocina Integral<br>Sta. Barbara</div>
        <div class="proyecto-info">Cuarzo · LED · Enchape · Grifería</div>
      </div>
    </div>

    <!-- Proyecto 3 -->
    <div class="proyecto-card reveal reveal-d2">
      <div class="proy-deco">
        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
          <polygon points="100,10 190,190 10,190" fill="none" stroke="#E8450A" stroke-width="1" opacity="0.4"/>
          <polygon points="100,50 160,170 40,170" fill="rgba(232,69,10,0.1)" stroke="#E8450A" stroke-width="0.5" opacity="0.3"/>
        </svg>
      </div>
      <div class="proyecto-overlay"></div>
      <div class="proyecto-content">
        <div class="proyecto-cat">RMD012 — Remodelación</div>
        <div class="proyecto-title">Techo LED<br>Barrio Ingenio</div>
        <div class="proyecto-info">Drywall · Estuco · Balas LED</div>
      </div>
    </div>

    <!-- Proyecto 4 -->
    <div class="proyecto-card reveal reveal-d1">
      <div class="proy-deco">
        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
          <rect x="10" y="10" width="80" height="80" fill="rgba(232,69,10,0.12)" stroke="#E8450A" stroke-width="0.8" opacity="0.5"/>
          <rect x="110" y="10" width="80" height="80" fill="none" stroke="rgba(232,69,10,0.4)" stroke-width="0.5" opacity="0.3"/>
          <rect x="10" y="110" width="180" height="80" fill="none" stroke="rgba(232,69,10,0.3)" stroke-width="0.5" opacity="0.3"/>
        </svg>
      </div>
      <div class="proyecto-overlay"></div>
      <div class="proyecto-content">
        <div class="proyecto-cat">RMD010 — Remodelación</div>
        <div class="proyecto-title">Cielo falso<br>Barrio Canney</div>
        <div class="proyecto-info">Drywall · Filos · Difuminado LED</div>
      </div>
    </div>
  </div>
</section>

<!-- ─── ALIADOS ─── -->
<section id="aliados">
  <div class="aliados-header">
    <p class="section-label reveal" style="justify-content:center;">05 — Aliados estratégicos</p>
    <h2 class="section-title reveal reveal-d1" style="text-align:center;">NUES<em>TROS</em><br>ALIADOS</h2>
  </div>
  <p class="reveal" style="text-align:center; color:var(--gray); max-width:480px; margin:0 auto 1rem; font-size:0.9rem;">
    Trabajamos con las marcas líderes del mercado para garantizar calidad en cada material utilizado.
  </p>
  <div class="aliados-grid reveal reveal-d1">
    <div class="aliado-item">IMPADOC</div>
    <div class="aliado-item">MAPEI</div>
    <div class="aliado-item">BRONCO</div>
    <div class="aliado-item">BOCCHERINI</div>
    <div class="aliado-item">MADECENTRO</div>
    <div class="aliado-item">CORONA</div>
  </div>
</section>

<!-- ─── ZONA DE COBERTURA ─── -->
<div class="zonas-strip">
  <div class="zonas-label">📍 Cobertura</div>
  <div class="zonas-list">
    <div class="zona-item">Cali</div>
    <div class="zona-item highlight">·</div>
    <div class="zona-item">Palmira</div>
    <div class="zona-item highlight">·</div>
    <div class="zona-item">Jamundí</div>
    <div class="zona-item highlight">·</div>
    <div class="zona-item">Yumbo</div>
    <div class="zona-item highlight">·</div>
    <div class="zona-item">Candelaria</div>
  </div>
</div>

<!-- ─── CONTACTO ─── -->
<section id="contacto">
  <div class="contacto-grid">
    <div class="contacto-left reveal">
      <p class="section-label">06 — Contacto</p>
      <h2 class="section-title">HABLE<em>MOS</em><br>DE TU<br>PROYEC<em>TO</em></h2>

      <div class="contacto-info">
        <div class="info-row">
          <div class="info-icon">📞</div>
          <div>
            <div class="info-label">WhatsApp / Teléfono</div>
            <div class="info-value">
              <a href="https://wa.me/573165334760">316-533-4760</a>
            </div>
          </div>
        </div>
        <div class="info-row">
          <div class="info-icon">✉️</div>
          <div>
            <div class="info-label">Correo electrónico</div>
            <div class="info-value">
              <a href="mailto:a.escalacomercial@gmail.com">a.escalacomercial@gmail.com</a>
            </div>
          </div>
        </div>
        <div class="info-row">
          <div class="info-icon">🌐</div>
          <div>
            <div class="info-label">Sitio web</div>
            <div class="info-value">
              <a href="https://aescala.co" target="_blank">aescala.co</a>
            </div>
          </div>
        </div>
        <div class="info-row">
          <div class="info-icon">📍</div>
          <div>
            <div class="info-label">Ubicación</div>
            <div class="info-value">Cali, Valle del Cauca — Colombia</div>
          </div>
        </div>
        <div class="info-row">
          <div class="info-icon">📱</div>
          <div>
            <div class="info-label">Redes sociales</div>
            <div class="info-value">
              <a href="https://instagram.com/aescala.co" target="_blank">@aescala.co</a> en Instagram y Facebook
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="contacto-right reveal reveal-d2">
      <p class="section-label" style="margin-bottom:1.5rem;">Cotización gratuita</p>
      <form class="contact-form" onsubmit="handleSubmit(event)">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-input" placeholder="Tu nombre" required>
          </div>
          <div class="form-group">
            <label class="form-label">Teléfono</label>
            <input type="tel" class="form-input" placeholder="300 000 0000" required>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Correo electrónico</label>
          <input type="email" class="form-input" placeholder="tu@correo.com" required>
        </div>
        <div class="form-group">
          <label class="form-label">Servicio de interés</label>
          <select class="form-select form-input">
            <option value="">Selecciona un servicio</option>
            <option>Remodelación</option>
            <option>Construcción</option>
            <option>Carpintería</option>
            <option>Diseño de Interiores</option>
            <option>Electricidad</option>
            <option>Instalaciones Hidráulicas</option>
            <option>Diseño Arquitectónico</option>
            <option>Otro</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Cuéntanos tu proyecto</label>
          <textarea class="form-textarea" placeholder="Describe brevemente qué necesitas hacer, el espacio y la ciudad..." required></textarea>
        </div>
        <button type="submit" class="btn-submit">Enviar solicitud ▶</button>
      </form>
    </div>
  </div>
</section>

<!-- ─── FOOTER ─── -->
<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <a href="#" class="footer-logo">
        <svg viewBox="0 0 36 36" width="32" height="32" fill="none">
          <path d="M18 3L33 31H3L18 3Z" stroke="#E8450A" stroke-width="2.5" fill="none"/>
          <line x1="8" y1="31" x2="28" y2="31" stroke="#E8450A" stroke-width="2.5"/>
        </svg>
        <span class="footer-logo-text">A<span>.</span>ESCALA</span>
      </a>
      <p class="footer-tagline">Arquitectura y Construcción · Cali, Colombia</p>
      <div class="footer-social">
        <a href="https://instagram.com/aescala.co" class="social-btn" target="_blank" title="Instagram">📷</a>
        <a href="https://facebook.com/aescala.co" class="social-btn" target="_blank" title="Facebook">👍</a>
        <a href="https://wa.me/573165334760" class="social-btn" target="_blank" title="WhatsApp">💬</a>
      </div>
    </div>

    <div>
      <p class="footer-col-title">Servicios</p>
      <ul class="footer-links">
        <li><a href="#servicios">Remodelación</a></li>
        <li><a href="#servicios">Construcción</a></li>
        <li><a href="#servicios">Carpintería</a></li>
        <li><a href="#servicios">Diseño de Interiores</a></li>
        <li><a href="#servicios">Electricidad</a></li>
        <li><a href="#servicios">Tramites Administrativos</a></li>
      </ul>
    </div>

    <div>
      <p class="footer-col-title">Empresa</p>
      <ul class="footer-links">
        <li><a href="#quienes">Quiénes somos</a></li>
        <li><a href="#proceso">Cómo trabajamos</a></li>
        <li><a href="#proyectos">Proyectos</a></li>
        <li><a href="#aliados">Aliados</a></li>
        <li><a href="#contacto">Contacto</a></li>
        <li><a href="mailto:a.escalacomercial@gmail.com">a.escalacomercial@gmail.com</a></li>
      </ul>
    </div>
  </div>

  <div class="footer-bottom">
    <p class="footer-copy">© 2025 <span>A.ESCALA</span> Arquitectura y Construcción · Todos los derechos reservados</p>
    <p class="footer-copy">Cali, Valle del Cauca — Colombia · <span>316-533-4760</span></p>
  </div>
</footer>

<!-- ─── WHATSAPP BTN ─── -->
<a href="https://wa.me/573165334760?text=Hola,%20me%20interesa%20una%20cotización%20para%20mi%20proyecto" class="wa-btn" target="_blank" title="Escríbenos por WhatsApp">
  <svg viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
  </svg>
</a>

<!-- ─── SCRIPTS ─── -->
<script>
  // Navbar scroll
  const navbar = document.getElementById('navbar');
  window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 60);
  });

  // Mobile nav
  function toggleMobileNav() {
    document.getElementById('mobileNav').classList.toggle('open');
  }

  // Scroll reveal
  const reveals = document.querySelectorAll('.reveal');
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        revealObserver.unobserve(e.target);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

  reveals.forEach(el => revealObserver.observe(el));

  // Form submit
  function handleSubmit(e) {
    e.preventDefault();
    const btn = e.target.querySelector('.btn-submit');
    btn.textContent = '✓ Enviado — te contactamos pronto';
    btn.style.background = '#1a7a3a';
    btn.style.clipPath = 'none';
    setTimeout(() => {
      btn.textContent = 'Enviar solicitud ▶';
      btn.style.background = '';
      btn.style.clipPath = '';
      e.target.reset();
    }, 4000);
  }

  // Smooth hover on proyecto cards
  document.querySelectorAll('.proyecto-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
      card.querySelector('.proyecto-bg') && (card.querySelector('.proyecto-bg').style.opacity = '0.7');
    });
  });
</script>
</body>
</html>
