<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PetCycle — Cuidado Animal Completo</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Syne:wght@400;500;600;700;800&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">

<style>
  *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

  :root {
    --sky: #56CCF2;
    --orange: #F2994A;
    --green: #6FCF97;
    --dark: #4F4F4F;
    --cream: #FAF9F6;
    --sky-light: #E8F8FD;
    --orange-light: #FEF3EB;
    --green-light: #EBF8F1;
    --dark-deep: #2C2C2C;
    --white: #FFFFFF;
  }

  html { scroll-behavior: smooth; }

  body {
    font-family: 'Inter', sans-serif;
    background: var(--cream);
    color: var(--dark);
    overflow-x: hidden;
  }

  /* ─── NAV ─── */
  nav {
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 100;
    padding: 1.25rem 4rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    backdrop-filter: blur(12px);
    background: rgba(250,249,246,0.85);
    border-bottom: 1px solid rgba(86,204,242,0.15);
    transition: box-shadow 0.3s;
  }

  .logo {
    font-family: 'Syne', sans-serif;
    font-weight: 800;
    font-size: 1.5rem;
    color: var(--dark-deep);
    letter-spacing: -0.03em;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .logo-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    background: var(--orange);
    display: inline-block;
  }

  .nav-links {
    display: flex;
    gap: 2.5rem;
    list-style: none;
  }

  .nav-links a {
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--dark);
    letter-spacing: 0.02em;
    transition: color 0.2s;
  }

  .nav-links a:hover { color: var(--sky); }

  .nav-cta {
    background: var(--dark-deep);
    color: var(--cream) !important;
    padding: 0.6rem 1.4rem;
    border-radius: 100px;
    font-size: 0.85rem !important;
    font-weight: 600 !important;
    transition: background 0.2s, transform 0.15s !important;
  }

  .nav-cta:hover { background: var(--sky) !important; color: var(--dark-deep) !important; transform: translateY(-1px); }

  /* ─── HERO ─── */
  .hero {
    min-height: 100vh;
    padding: 8rem 4rem 5rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    align-items: center;
    gap: 4rem;
    position: relative;
    overflow: hidden;
  }

  .hero-bg-blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.25;
    pointer-events: none;
  }

  .blob-1 {
    width: 600px; height: 600px;
    background: var(--sky);
    top: -150px; right: -100px;
  }

  .blob-2 {
    width: 400px; height: 400px;
    background: var(--green);
    bottom: -100px; left: -80px;
  }

  .blob-3 {
    width: 300px; height: 300px;
    background: var(--orange);
    top: 200px; right: 200px;
    opacity: 0.18;
  }

  .hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--sky-light);
    border: 1px solid rgba(86,204,242,0.4);
    color: #1a8fb5;
    font-size: 0.78rem;
    font-weight: 600;
    font-family: 'Syne', sans-serif;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 0.45rem 1rem;
    border-radius: 100px;
    margin-bottom: 1.75rem;
    animation: fadeUp 0.6s ease both;
  }

  .badge-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: var(--sky);
    animation: pulse 2s infinite;
  }

  @keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(0.8); }
  }

  .hero-title {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(3rem, 5vw, 5rem);
    line-height: 1.05;
    color: var(--dark-deep);
    letter-spacing: -0.02em;
    margin-bottom: 1.5rem;
    animation: fadeUp 0.7s ease 0.1s both;
  }

  .hero-title em {
    font-style: italic;
    color: var(--sky);
  }

  .hero-title .accent {
    position: relative;
    display: inline-block;
  }

  .hero-title .accent::after {
    content: '';
    position: absolute;
    bottom: 4px;
    left: 0; right: 0;
    height: 4px;
    background: var(--orange);
    border-radius: 2px;
  }

  .hero-desc {
    font-size: 1.05rem;
    line-height: 1.75;
    color: #6b6b6b;
    max-width: 480px;
    margin-bottom: 2.5rem;
    font-weight: 300;
    animation: fadeUp 0.7s ease 0.2s both;
  }

  .hero-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
    animation: fadeUp 0.7s ease 0.3s both;
  }

  .btn-primary {
    background: var(--dark-deep);
    color: var(--cream);
    padding: 0.9rem 2rem;
    border-radius: 100px;
    font-size: 0.9rem;
    font-weight: 600;
    font-family: 'Syne', sans-serif;
    text-decoration: none;
    transition: all 0.25s;
    border: 2px solid var(--dark-deep);
  }

  .btn-primary:hover {
    background: var(--sky);
    border-color: var(--sky);
    color: var(--dark-deep);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(86,204,242,0.3);
  }

  .btn-secondary {
    color: var(--dark);
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    transition: gap 0.2s, color 0.2s;
  }

  .btn-secondary:hover { gap: 0.7rem; color: var(--orange); }

  /* Hero Visual */
  .hero-visual {
    position: relative;
    animation: fadeUp 0.8s ease 0.2s both;
  }

  .hero-card-main {
    background: var(--white);
    border-radius: 24px;
    padding: 2rem;
    box-shadow: 0 20px 60px rgba(79,79,79,0.12);
    border: 1px solid rgba(86,204,242,0.15);
    position: relative;
    z-index: 2;
  }

  .pet-profile-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.25rem;
    border-bottom: 1px solid #f0f0f0;
  }

  .pet-avatar {
    width: 56px; height: 56px;
    border-radius: 16px;
    background: linear-gradient(135deg, var(--orange-light), var(--orange));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
  }

  .pet-name {
    font-family: 'Syne', sans-serif;
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--dark-deep);
  }

  .pet-breed {
    font-size: 0.8rem;
    color: #9b9b9b;
    margin-top: 2px;
  }

  .status-chip {
    margin-left: auto;
    padding: 0.3rem 0.75rem;
    border-radius: 100px;
    font-size: 0.72rem;
    font-weight: 600;
    font-family: 'Syne', sans-serif;
    letter-spacing: 0.04em;
  }

  .status-ok { background: var(--green-light); color: #2d7a4f; }
  .status-lost { background: #FEF3EB; color: #b5580a; }

  .health-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
  }

  .health-item {
    background: var(--cream);
    border-radius: 12px;
    padding: 0.75rem 1rem;
  }

  .health-label {
    font-size: 0.7rem;
    font-weight: 600;
    color: #9b9b9b;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin-bottom: 0.3rem;
  }

  .health-value {
    font-family: 'Syne', sans-serif;
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--dark-deep);
  }

  .health-icon { font-size: 0.85rem; margin-right: 0.3rem; }

  .vaccine-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.6rem 0;
    border-top: 1px solid #f5f5f5;
    font-size: 0.82rem;
  }

  .vaccine-name { color: var(--dark); font-weight: 500; }
  .vaccine-date { color: #9b9b9b; font-size: 0.75rem; }
  .vaccine-ok { width: 8px; height: 8px; border-radius: 50%; background: var(--green); }

  /* Floating cards */
  .float-card {
    position: absolute;
    background: var(--white);
    border-radius: 16px;
    padding: 0.9rem 1.1rem;
    box-shadow: 0 8px 30px rgba(79,79,79,0.12);
    border: 1px solid rgba(255,255,255,0.8);
    z-index: 3;
    animation: float 4s ease-in-out infinite;
  }

  .float-card-1 {
    top: -30px; right: -30px;
    animation-delay: 0s;
  }

  .float-card-2 {
    bottom: 60px; left: -50px;
    animation-delay: 1.5s;
  }

  @keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
  }

  .float-icon { font-size: 1.1rem; margin-bottom: 0.3rem; }
  .float-title { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 0.8rem; color: var(--dark-deep); }
  .float-sub { font-size: 0.7rem; color: #9b9b9b; margin-top: 2px; }

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(24px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* ─── STATS ─── */
  .stats-strip {
    background: var(--dark-deep);
    padding: 2rem 4rem;
    display: flex;
    justify-content: space-around;
    align-items: center;
    gap: 2rem;
  }

  .stat-item {
    text-align: center;
  }

  .stat-number {
    font-family: 'DM Serif Display', serif;
    font-size: 2.5rem;
    color: var(--sky);
    line-height: 1;
  }

  .stat-label {
    font-size: 0.8rem;
    color: rgba(250,249,246,0.6);
    margin-top: 0.3rem;
    font-weight: 300;
    letter-spacing: 0.04em;
  }

  .stat-divider {
    width: 1px;
    height: 40px;
    background: rgba(255,255,255,0.1);
  }

  /* ─── FEATURES ─── */
  .section {
    padding: 6rem 4rem;
  }

  .section-tag {
    font-family: 'Syne', sans-serif;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--sky);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .section-tag::before {
    content: '';
    display: block;
    width: 24px;
    height: 2px;
    background: var(--sky);
    border-radius: 1px;
  }

  .section-title {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(2rem, 3.5vw, 3.2rem);
    color: var(--dark-deep);
    line-height: 1.1;
    letter-spacing: -0.02em;
    margin-bottom: 1rem;
  }

  .section-subtitle {
    font-size: 1rem;
    color: #7a7a7a;
    line-height: 1.7;
    max-width: 520px;
    font-weight: 300;
  }

  .features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    margin-top: 3.5rem;
  }

  .feature-card {
    background: var(--white);
    border-radius: 20px;
    padding: 2rem;
    border: 1px solid rgba(79,79,79,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
    position: relative;
    overflow: hidden;
  }

  .feature-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 20px 20px 0 0;
    opacity: 0;
    transition: opacity 0.3s;
  }

  .feature-card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(79,79,79,0.1); }
  .feature-card:hover::before { opacity: 1; }

  .card-sky::before { background: var(--sky); }
  .card-orange::before { background: var(--orange); }
  .card-green::before { background: var(--green); }

  .feature-icon-wrap {
    width: 52px; height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    margin-bottom: 1.25rem;
  }

  .icon-sky { background: var(--sky-light); }
  .icon-orange { background: var(--orange-light); }
  .icon-green { background: var(--green-light); }

  .feature-title {
    font-family: 'Syne', sans-serif;
    font-weight: 700;
    font-size: 1rem;
    color: var(--dark-deep);
    margin-bottom: 0.6rem;
  }

  .feature-desc {
    font-size: 0.875rem;
    line-height: 1.65;
    color: #7a7a7a;
    font-weight: 300;
  }

  /* ─── HOW IT WORKS ─── */
  .how-section {
    background: var(--dark-deep);
    padding: 6rem 4rem;
  }

  .how-section .section-tag { color: var(--orange); }
  .how-section .section-tag::before { background: var(--orange); }
  .how-section .section-title { color: var(--cream); }
  .how-section .section-subtitle { color: rgba(250,249,246,0.6); }

  .steps-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
    margin-top: 4rem;
    position: relative;
  }

  .steps-container::before {
    content: '';
    position: absolute;
    top: 28px;
    left: 10%;
    right: 10%;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(86,204,242,0.3), transparent);
  }

  .step-item {
    text-align: center;
    position: relative;
  }

  .step-number {
    width: 56px; height: 56px;
    border-radius: 50%;
    border: 2px solid rgba(86,204,242,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
    font-family: 'Syne', sans-serif;
    font-weight: 800;
    font-size: 1.1rem;
    color: var(--sky);
    background: rgba(86,204,242,0.05);
    position: relative;
    z-index: 1;
  }

  .step-title {
    font-family: 'Syne', sans-serif;
    font-weight: 700;
    color: var(--cream);
    font-size: 0.95rem;
    margin-bottom: 0.6rem;
  }

  .step-desc {
    font-size: 0.82rem;
    color: rgba(250,249,246,0.55);
    line-height: 1.6;
    font-weight: 300;
  }

  /* ─── ADOPTION / LOST ─── */
  .dual-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: 600px;
  }

  .dual-panel {
    padding: 5rem 4rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .panel-sky { background: var(--sky-light); }
  .panel-orange { background: var(--orange-light); }

  .panel-tag-sky { color: #1a8fb5; }
  .panel-tag-orange { color: #b5580a; }

  .panel-sky .section-tag::before { background: var(--sky); }
  .panel-orange .section-tag::before { background: var(--orange); }

  .panel-sky .section-tag { color: #1a8fb5; }
  .panel-orange .section-tag { color: #b5580a; }

  .panel-title {
    font-family: 'DM Serif Display', serif;
    font-size: 2.2rem;
    color: var(--dark-deep);
    line-height: 1.1;
    margin-bottom: 1rem;
    letter-spacing: -0.02em;
  }

  .panel-desc {
    font-size: 0.9rem;
    color: #6b6b6b;
    line-height: 1.7;
    font-weight: 300;
    max-width: 380px;
    margin-bottom: 2rem;
  }

  .feature-list {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 0.7rem;
  }

  .feature-list li {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 0.875rem;
    color: var(--dark);
  }

  .check {
    width: 20px; height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.65rem;
    flex-shrink: 0;
  }

  .check-sky { background: var(--sky-light); color: #1a8fb5; border: 1px solid rgba(86,204,242,0.4); }
  .check-orange { background: var(--orange-light); color: #b5580a; border: 1px solid rgba(242,153,74,0.4); }

  /* ─── VETERINARY ─── */
  .vet-section {
    background: var(--cream);
    padding: 6rem 4rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 5rem;
    align-items: center;
  }

  .vet-dashboard {
    background: var(--white);
    border-radius: 24px;
    padding: 1.75rem;
    box-shadow: 0 20px 60px rgba(79,79,79,0.1);
    border: 1px solid rgba(79,79,79,0.06);
  }

  .vet-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
  }

  .vet-title-widget {
    font-family: 'Syne', sans-serif;
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--dark-deep);
  }

  .vet-subtitle-widget { font-size: 0.75rem; color: #9b9b9b; margin-top: 2px; }

  .vet-badge {
    background: var(--green-light);
    color: #2d7a4f;
    font-size: 0.7rem;
    font-weight: 700;
    font-family: 'Syne', sans-serif;
    padding: 0.3rem 0.75rem;
    border-radius: 100px;
    border: 1px solid rgba(111,207,151,0.3);
  }

  .vet-timeline {
    position: relative;
    padding-left: 1.5rem;
  }

  .vet-timeline::before {
    content: '';
    position: absolute;
    left: 6px;
    top: 0; bottom: 0;
    width: 1px;
    background: linear-gradient(to bottom, var(--sky), var(--green), var(--orange));
    opacity: 0.3;
  }

  .timeline-item {
    position: relative;
    padding: 0.85rem 0;
    border-bottom: 1px solid #f5f5f5;
  }

  .timeline-item:last-child { border-bottom: none; }

  .timeline-dot {
    position: absolute;
    left: -1.25rem;
    top: 1.1rem;
    width: 10px; height: 10px;
    border-radius: 50%;
    border: 2px solid var(--white);
    box-shadow: 0 0 0 1px;
  }

  .dot-sky { background: var(--sky); box-shadow: 0 0 0 1px var(--sky); }
  .dot-green { background: var(--green); box-shadow: 0 0 0 1px var(--green); }
  .dot-orange { background: var(--orange); box-shadow: 0 0 0 1px var(--orange); }

  .timeline-event {
    font-weight: 500;
    font-size: 0.85rem;
    color: var(--dark-deep);
    margin-bottom: 0.2rem;
  }

  .timeline-meta {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.73rem;
    color: #9b9b9b;
  }

  .timeline-chip {
    padding: 0.15rem 0.5rem;
    border-radius: 100px;
    font-size: 0.67rem;
    font-weight: 600;
    font-family: 'Syne', sans-serif;
  }

  .chip-sky { background: var(--sky-light); color: #1a8fb5; }
  .chip-green { background: var(--green-light); color: #2d7a4f; }
  .chip-orange { background: var(--orange-light); color: #b5580a; }

  /* ─── TESTIMONIALS ─── */
  .testimonials-section {
    background: var(--dark-deep);
    padding: 6rem 4rem;
    overflow: hidden;
  }

  .testimonials-section .section-title { color: var(--cream); text-align: center; }
  .testimonials-section .section-tag {
    color: var(--green);
    justify-content: center;
  }
  .testimonials-section .section-tag::before { background: var(--green); }

  .testimonials-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    margin-top: 3.5rem;
  }

  .testimonial-card {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 20px;
    padding: 1.75rem;
    transition: background 0.3s, border 0.3s;
  }

  .testimonial-card:hover {
    background: rgba(86,204,242,0.05);
    border-color: rgba(86,204,242,0.2);
  }

  .testimonial-stars {
    display: flex;
    gap: 3px;
    margin-bottom: 1rem;
  }

  .star {
    width: 12px; height: 12px;
    background: var(--orange);
    clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
  }

  .testimonial-text {
    font-family: 'DM Serif Display', serif;
    font-style: italic;
    font-size: 1rem;
    line-height: 1.65;
    color: rgba(250,249,246,0.8);
    margin-bottom: 1.5rem;
  }

  .testimonial-author {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .author-avatar {
    width: 38px; height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Syne', sans-serif;
    font-weight: 700;
    font-size: 0.8rem;
    flex-shrink: 0;
  }

  .av-sky { background: rgba(86,204,242,0.15); color: var(--sky); }
  .av-green { background: rgba(111,207,151,0.15); color: var(--green); }
  .av-orange { background: rgba(242,153,74,0.15); color: var(--orange); }

  .author-name {
    font-family: 'Syne', sans-serif;
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--cream);
  }

  .author-role { font-size: 0.73rem; color: rgba(250,249,246,0.4); margin-top: 1px; }

  /* ─── CTA ─── */
  .cta-section {
    background: var(--sky);
    padding: 6rem 4rem;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .cta-section::before {
    content: '';
    position: absolute;
    top: -80px; right: -80px;
    width: 400px; height: 400px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
  }

  .cta-section::after {
    content: '';
    position: absolute;
    bottom: -100px; left: -60px;
    width: 300px; height: 300px;
    border-radius: 50%;
    background: rgba(79,79,79,0.08);
  }

  .cta-title {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(2.2rem, 4vw, 3.8rem);
    color: var(--dark-deep);
    line-height: 1.1;
    letter-spacing: -0.02em;
    margin-bottom: 1.25rem;
    position: relative;
    z-index: 1;
  }

  .cta-subtitle {
    font-size: 1rem;
    color: rgba(44,44,44,0.7);
    max-width: 460px;
    margin: 0 auto 2.5rem;
    line-height: 1.65;
    font-weight: 300;
    position: relative;
    z-index: 1;
  }

  .cta-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    align-items: center;
    position: relative;
    z-index: 1;
  }

  .btn-cta-primary {
    background: var(--dark-deep);
    color: var(--cream);
    padding: 1rem 2.2rem;
    border-radius: 100px;
    font-family: 'Syne', sans-serif;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.25s;
    letter-spacing: 0.01em;
  }

  .btn-cta-primary:hover {
    background: var(--white);
    color: var(--dark-deep);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(44,44,44,0.2);
  }

  .btn-cta-secondary {
    color: var(--dark-deep);
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    opacity: 0.7;
    transition: opacity 0.2s;
  }

  .btn-cta-secondary:hover { opacity: 1; }

  /* ─── FOOTER ─── */
  footer {
    background: var(--dark-deep);
    padding: 3rem 4rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 1px solid rgba(255,255,255,0.05);
  }

  .footer-logo {
    font-family: 'Syne', sans-serif;
    font-weight: 800;
    font-size: 1.2rem;
    color: var(--cream);
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .footer-copy {
    font-size: 0.78rem;
    color: rgba(250,249,246,0.35);
  }

  .footer-links {
    display: flex;
    gap: 1.75rem;
    list-style: none;
  }

  .footer-links a {
    font-size: 0.8rem;
    color: rgba(250,249,246,0.5);
    text-decoration: none;
    transition: color 0.2s;
  }

  .footer-links a:hover { color: var(--sky); }

  /* ─── SCROLL ANIMATIONS ─── */
  .reveal {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.6s ease, transform 0.6s ease;
  }

  .reveal.visible {
    opacity: 1;
    transform: translateY(0);
  }
</style>
</head>
<body>

<!-- NAV -->
<nav>
  <div class="logo">
    <span class="logo-dot"></span>
    PetCycle
  </div>
  <ul class="nav-links">
    <li><a href="#features">Funcionalidades</a></li>
    <li><a href="#como-funciona">Como Funciona</a></li>
    <li><a href="#veterinario">Saúde</a></li>
    <li><a href="#adocao">Adoção</a></li>
    <li><a href="#cta" class="nav-cta">Começar grátis</a></li>
  </ul>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg-blob blob-1"></div>
  <div class="hero-bg-blob blob-2"></div>
  <div class="hero-bg-blob blob-3"></div>

  <div class="hero-content">
    <div class="hero-badge">
      <span class="badge-dot"></span>
      Ciclo completo do cuidado animal
    </div>
    <h1 class="hero-title">
      O bem-estar animal,<br>
      <em>do início</em> ao<br>
      <span class="accent">lar certo</span>
    </h1>
    <p class="hero-desc">
      Comunicação de animais perdidos, facilitação de adoções e prontuário veterinário completo — tudo em uma plataforma pensada para quem ama animais.
    </p>
    <div class="hero-actions">
      <a href="#cta" class="btn-primary">Criar conta gratuita</a>
      <a href="#como-funciona" class="btn-secondary">Ver como funciona →</a>
    </div>
  </div>

  <div class="hero-visual">
    <div class="float-card float-card-1">
      <div class="float-icon">🔔</div>
      <div class="float-title">Animal encontrado!</div>
      <div class="float-sub">Próximo a você · agora mesmo</div>
    </div>

    <div class="hero-card-main">
      <div class="pet-profile-header">
        <div class="pet-avatar">🐕</div>
        <div>
          <div class="pet-name">Bolinha</div>
          <div class="pet-breed">Golden Retriever · 3 anos</div>
        </div>
        <span class="status-chip status-ok">✓ Saudável</span>
      </div>

      <div class="health-grid">
        <div class="health-item">
          <div class="health-label">Vacinação</div>
          <div class="health-value"><span class="health-icon">💉</span>Em dia</div>
        </div>
        <div class="health-item">
          <div class="health-label">Castração</div>
          <div class="health-value"><span class="health-icon">✂️</span>Realizada</div>
        </div>
        <div class="health-item">
          <div class="health-label">Último exame</div>
          <div class="health-value"><span class="health-icon">🔬</span>Mar 2025</div>
        </div>
        <div class="health-item">
          <div class="health-label">Próx. consulta</div>
          <div class="health-value"><span class="health-icon">📅</span>Jul 2025</div>
        </div>
      </div>

      <div class="vaccine-row">
        <span class="vaccine-name">Antirrábica</span>
        <span class="vaccine-date">Jan 2025</span>
        <div class="vaccine-ok"></div>
      </div>
      <div class="vaccine-row">
        <span class="vaccine-name">V10 Polivalente</span>
        <span class="vaccine-date">Mar 2025</span>
        <div class="vaccine-ok"></div>
      </div>
      <div class="vaccine-row">
        <span class="vaccine-name">Gripe Canina</span>
        <span class="vaccine-date">Mar 2025</span>
        <div class="vaccine-ok"></div>
      </div>
    </div>

    <div class="float-card float-card-2">
      <div class="float-icon">💚</div>
      <div class="float-title">12 adoções hoje</div>
      <div class="float-sub">lares encontrados</div>
    </div>
  </div>
</section>

<!-- STATS -->
<div class="stats-strip">
  <div class="stat-item">
    <div class="stat-number">48k+</div>
    <div class="stat-label">Animais cadastrados</div>
  </div>
  <div class="stat-divider"></div>
  <div class="stat-item">
    <div class="stat-number">3.2k</div>
    <div class="stat-label">Adoções realizadas</div>
  </div>
  <div class="stat-divider"></div>
  <div class="stat-item">
    <div class="stat-number">91%</div>
    <div class="stat-label">Taxa de reencontro</div>
  </div>
  <div class="stat-divider"></div>
  <div class="stat-item">
    <div class="stat-number">280+</div>
    <div class="stat-label">Clínicas parceiras</div>
  </div>
</div>

<!-- FEATURES -->
<section class="section" id="features">
  <div class="section-tag">Funcionalidades</div>
  <h2 class="section-title">Tudo que seu pet precisa,<br>num só lugar</h2>
  <p class="section-subtitle">De alertas de desaparecimento a prontuários clínicos completos — uma plataforma para cuidar do ciclo completo.</p>

  <div class="features-grid">
    <div class="feature-card card-sky reveal">
      <div class="feature-icon-wrap icon-sky">🔍</div>
      <div class="feature-title">Animais Perdidos & Encontrados</div>
      <p class="feature-desc">Alertas geolocalizados em tempo real para animais desaparecidos. Comunidade ativa que ajuda a reunir tutores e pets.</p>
    </div>
    <div class="feature-card card-orange reveal">
      <div class="feature-icon-wrap icon-orange">🏠</div>
      <div class="feature-title">Facilitação de Adoções</div>
      <p class="feature-desc">Conectamos animais que precisam de um lar com tutores responsáveis, tornando o processo seguro e transparente.</p>
    </div>
    <div class="feature-card card-green reveal">
      <div class="feature-icon-wrap icon-green">🩺</div>
      <div class="feature-title">Prontuário Veterinário</div>
      <p class="feature-desc">Registro completo de vacinações, castração, exames e consultas. Histórico clínico acessível a qualquer momento.</p>
    </div>
    <div class="feature-card card-green reveal">
      <div class="feature-icon-wrap icon-green">💉</div>
      <div class="feature-title">Lembretes de Vacinas</div>
      <p class="feature-desc">Nunca mais perca uma dose. Receba notificações automáticas sobre vacinas e consultas de reforço do seu animal.</p>
    </div>
    <div class="feature-card card-sky reveal">
      <div class="feature-icon-wrap icon-sky">📊</div>
      <div class="feature-title">Relatórios Clínicos</div>
      <p class="feature-desc">Acesse resultados de exames, laudos e evoluções de saúde de forma organizada e compartilhe com qualquer veterinário.</p>
    </div>
    <div class="feature-card card-orange reveal">
      <div class="feature-icon-wrap icon-orange">🌐</div>
      <div class="feature-title">Rede de Cuidadores</div>
      <p class="feature-desc">Conecte-se com ONGs, abrigos e cuidadores temporários para garantir que nenhum animal fique desamparado.</p>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="how-section" id="como-funciona">
  <div class="section-tag">Como Funciona</div>
  <h2 class="section-title" style="color: var(--cream);">Simples. Rápido.<br><em style="color: var(--sky);">Eficaz.</em></h2>
  <p class="section-subtitle">Em poucos passos, seu animal tem um perfil completo e sua rede de apoio está ativada.</p>

  <div class="steps-container">
    <div class="step-item reveal">
      <div class="step-number">01</div>
      <div class="step-title">Cadastre seu animal</div>
      <p class="step-desc">Crie o perfil completo com fotos, raça, microchip e dados de saúde em minutos.</p>
    </div>
    <div class="step-item reveal">
      <div class="step-number">02</div>
      <div class="step-title">Gerencie a saúde</div>
      <p class="step-desc">Adicione vacinas, consultas e exames para um prontuário sempre atualizado.</p>
    </div>
    <div class="step-item reveal">
      <div class="step-number">03</div>
      <div class="step-title">Ative a comunidade</div>
      <p class="step-desc">Em caso de desaparecimento, alertas chegam a quem está por perto automaticamente.</p>
    </div>
    <div class="step-item reveal">
      <div class="step-number">04</div>
      <div class="step-title">Conecte & Adote</div>
      <p class="step-desc">Animais disponíveis para adoção encontram o tutor ideal com transparência e segurança.</p>
    </div>
  </div>
</section>

<!-- DUAL: ADOÇÃO + PERDIDOS -->
<div class="dual-section" id="adocao">
  <div class="dual-panel panel-sky">
    <div class="section-tag">Adoção Responsável</div>
    <div class="panel-title">Cada animal merece um lar de verdade</div>
    <p class="panel-desc">Nosso processo de adoção conecta animais com tutores compatíveis, garantindo bem-estar para ambos os lados.</p>
    <ul class="feature-list">
      <li>
        <span class="check check-sky">✓</span>
        Filtros por porte, raça, idade e localização
      </li>
      <li>
        <span class="check check-sky">✓</span>
        Histórico de saúde disponível para adotantes
      </li>
      <li>
        <span class="check check-sky">✓</span>
        Acompanhamento pós-adoção integrado
      </li>
      <li>
        <span class="check check-sky">✓</span>
        Parceria com abrigos e ONGs verificados
      </li>
    </ul>
  </div>

  <div class="dual-panel panel-orange">
    <div class="section-tag">Perdidos & Encontrados</div>
    <div class="panel-title">Reencontros que mudam vidas</div>
    <p class="panel-desc">Sistema de alerta inteligente que mobiliza a comunidade em segundos para encontrar animais desaparecidos.</p>
    <ul class="feature-list">
      <li>
        <span class="check check-orange">✓</span>
        Alertas geolocalizados em tempo real
      </li>
      <li>
        <span class="check check-orange">✓</span>
        Notificações push para usuários na região
      </li>
      <li>
        <span class="check check-orange">✓</span>
        Integração com redes sociais e grupos locais
      </li>
      <li>
        <span class="check check-orange">✓</span>
        Reconhecimento visual por foto do animal
      </li>
    </ul>
  </div>
</div>

<!-- VETERINARY -->
<section class="vet-section" id="veterinario">
  <div class="vet-dashboard reveal">
    <div class="vet-header">
      <div>
        <div class="vet-title-widget">Prontuário Clínico</div>
        <div class="vet-subtitle-widget">Histórico completo · sincronizado</div>
      </div>
      <span class="vet-badge">✓ Atualizado</span>
    </div>

    <div class="vet-timeline">
      <div class="timeline-item">
        <div class="timeline-dot dot-green"></div>
        <div class="timeline-event">Vacinação V10 + Antirrábica</div>
        <div class="timeline-meta">
          <span>Mar 2025</span>
          <span class="timeline-chip chip-green">Vacina</span>
          <span>Dr. Fernando Alves</span>
        </div>
      </div>
      <div class="timeline-item">
        <div class="timeline-dot dot-sky"></div>
        <div class="timeline-event">Hemograma completo + Bioquímica</div>
        <div class="timeline-meta">
          <span>Fev 2025</span>
          <span class="timeline-chip chip-sky">Exame</span>
          <span>Resultados normais</span>
        </div>
      </div>
      <div class="timeline-item">
        <div class="timeline-dot dot-orange"></div>
        <div class="timeline-event">Cirurgia de castração</div>
        <div class="timeline-meta">
          <span>Jan 2025</span>
          <span class="timeline-chip chip-orange">Procedimento</span>
          <span>Recuperação: ótima</span>
        </div>
      </div>
      <div class="timeline-item">
        <div class="timeline-dot dot-sky"></div>
        <div class="timeline-event">Consulta de rotina + Vermifugação</div>
        <div class="timeline-meta">
          <span>Dez 2024</span>
          <span class="timeline-chip chip-sky">Consulta</span>
          <span>Dra. Ana Souza</span>
        </div>
      </div>
      <div class="timeline-item">
        <div class="timeline-dot dot-green"></div>
        <div class="timeline-event">Vacinação Gripe Canina</div>
        <div class="timeline-meta">
          <span>Nov 2024</span>
          <span class="timeline-chip chip-green">Vacina</span>
          <span>Dr. Marcos Lima</span>
        </div>
      </div>
    </div>
  </div>

  <div>
    <div class="section-tag">Saúde Veterinária</div>
    <h2 class="section-title">Prontuário digital<br>completo e seguro</h2>
    <p class="section-subtitle">Todas as informações clínicas do seu animal armazenadas de forma segura e acessível a qualquer momento, de qualquer lugar.</p>

    <div style="margin-top: 2rem; display: flex; flex-direction: column; gap: 1.25rem;">
      <div style="display: flex; align-items: flex-start; gap: 1rem;">
        <div style="width: 40px; height: 40px; border-radius: 12px; background: var(--sky-light); display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0;">📋</div>
        <div>
          <div style="font-family: 'Syne', sans-serif; font-weight: 600; font-size: 0.9rem; color: var(--dark-deep); margin-bottom: 0.3rem;">Registros centralizados</div>
          <div style="font-size: 0.82rem; color: #7a7a7a; line-height: 1.6; font-weight: 300;">Vacinas, exames, laudos e evoluções em um único perfil digital, organizado por linha do tempo.</div>
        </div>
      </div>
      <div style="display: flex; align-items: flex-start; gap: 1rem;">
        <div style="width: 40px; height: 40px; border-radius: 12px; background: var(--orange-light); display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0;">🔔</div>
        <div>
          <div style="font-family: 'Syne', sans-serif; font-weight: 600; font-size: 0.9rem; color: var(--dark-deep); margin-bottom: 0.3rem;">Alertas automáticos</div>
          <div style="font-size: 0.82rem; color: #7a7a7a; line-height: 1.6; font-weight: 300;">Lembretes inteligentes para doses de reforço, consultas preventivas e medicamentos contínuos.</div>
        </div>
      </div>
      <div style="display: flex; align-items: flex-start; gap: 1rem;">
        <div style="width: 40px; height: 40px; border-radius: 12px; background: var(--green-light); display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0;">🔒</div>
        <div>
          <div style="font-family: 'Syne', sans-serif; font-weight: 600; font-size: 0.9rem; color: var(--dark-deep); margin-bottom: 0.3rem;">Compartilhamento seguro</div>
          <div style="font-size: 0.82rem; color: #7a7a7a; line-height: 1.6; font-weight: 300;">Compartilhe o histórico com qualquer veterinário ou clínica parceira com um simples link seguro.</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testimonials-section">
  <div class="section-tag" style="justify-content: center;">Depoimentos</div>
  <h2 class="section-title" style="text-align: center; max-width: 500px; margin: 0 auto;">O que dizem quem já usa</h2>

  <div class="testimonials-grid">
    <div class="testimonial-card reveal">
      <div class="testimonial-stars">
        <div class="star"></div><div class="star"></div><div class="star"></div><div class="star"></div><div class="star"></div>
      </div>
      <p class="testimonial-text">"Minha cachorra sumiu e em menos de 2 horas, graças aos alertas do PetCycle, uma vizinha me ligou dizendo que ela estava lá. Não tenho palavras."</p>
      <div class="testimonial-author">
        <div class="author-avatar av-sky">MC</div>
        <div>
          <div class="author-name">Maria Clara</div>
          <div class="author-role">Tutora · São Paulo, SP</div>
        </div>
      </div>
    </div>

    <div class="testimonial-card reveal">
      <div class="testimonial-stars">
        <div class="star"></div><div class="star"></div><div class="star"></div><div class="star"></div><div class="star"></div>
      </div>
      <p class="testimonial-text">"Uso o prontuário para todos os animais da minha clínica. A organização e facilidade de acesso ao histórico dos pacientes mudou nossa rotina completamente."</p>
      <div class="testimonial-author">
        <div class="author-avatar av-green">DR</div>
        <div>
          <div class="author-name">Dr. Rafael Matos</div>
          <div class="author-role">Médico Veterinário · Belo Horizonte</div>
        </div>
      </div>
    </div>

    <div class="testimonial-card reveal">
      <div class="testimonial-stars">
        <div class="star"></div><div class="star"></div><div class="star"></div><div class="star"></div><div class="star"></div>
      </div>
      <p class="testimonial-text">"Adotei meu gato pelo PetCycle e todo o processo foi incrível. Ver o histórico de vacinação dele antes de adotar me deu muita segurança e confiança."</p>
      <div class="testimonial-author">
        <div class="author-avatar av-orange">LF</div>
        <div>
          <div class="author-name">Lucas Ferreira</div>
          <div class="author-role">Tutor adotante · Rio de Janeiro</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section" id="cta">
  <h2 class="cta-title">Pronto para cuidar melhor<br>do seu animal?</h2>
  <p class="cta-subtitle">Junte-se a milhares de tutores, veterinários e protetores que já confiam no PetCycle para garantir o bem-estar animal.</p>
  <div class="cta-actions">
    <a href="#" class="btn-cta-primary">Criar conta gratuita</a>
    <a href="#" class="btn-cta-secondary">Falar com a equipe</a>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-logo">
    <span class="logo-dot"></span>
    PetCycle
  </div>
  <span class="footer-copy">© 2025 PetCycle. Todos os direitos reservados.</span>
  <ul class="footer-links">
    <li><a href="#">Privacidade</a></li>
    <li><a href="#">Termos</a></li>
    <li><a href="#">Contato</a></li>
  </ul>
</footer>

<script>
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        setTimeout(() => {
          entry.target.classList.add('visible');
        }, (entry.target.dataset.delay || 0) * 100);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.reveal').forEach((el, i) => {
    el.dataset.delay = i % 3;
    observer.observe(el);
  });

  window.addEventListener('scroll', () => {
    const nav = document.querySelector('nav');
    nav.style.boxShadow = window.scrollY > 20 ? '0 4px 20px rgba(79,79,79,0.08)' : 'none';
  });
</script>
</body>
</html>