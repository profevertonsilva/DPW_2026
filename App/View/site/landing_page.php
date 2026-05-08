<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AmigoPet — Cuidado Animal</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

:root {
  --orange:       #F2994A;
  --orange-d:     #D97B2A;
  --orange-l:     #FEF3EB;
  --orange-m:     #FDDFC5;
  --green:        #6FCF97;
  --green-d:      #3da96e;
  --green-l:      #EAFAF2;
  --green-m:      #C2EDD5;
  --sky:          #56CCF2;
  --sky-l:        #E8F8FD;
  --dark:         #4F4F4F;
  --dark-d:       #2A2A2A;
  --cream:        #FAF9F6;
  --white:        #FFFFFF;
  --border:       rgba(79,79,79,0.10);
}

html { scroll-behavior: smooth; }

body {
  font-family: 'Inter', sans-serif;
  background: var(--cream);
  color: var(--dark);
  overflow-x: hidden;
  -webkit-font-smoothing: antialiased;
}

/* ─── NAVBAR ─── */
nav {
  position: fixed;
  top: 0; left: 0; right: 0;
  z-index: 200;
  padding: 0 5rem;
  height: 72px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: rgba(250,249,246,0.90);
  backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(242,153,74,0.12);
  transition: box-shadow 0.3s;
}

.logo {
  font-family: 'Poppins', sans-serif;
  font-weight: 800;
  font-size: 1.45rem;
  color: var(--dark-d);
  letter-spacing: -0.04em;
  display: flex;
  align-items: center;
  gap: 0.45rem;
  text-decoration: none;
}

.logo-paw {
  width: 34px; height: 34px;
  border-radius: 10px;
  background: var(--orange);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  box-shadow: 0 3px 10px rgba(242,153,74,0.35);
}

.nav-links {
  list-style: none;
  display: flex;
  align-items: center;
  gap: 2.25rem;
}

.nav-links a {
  text-decoration: none;
  font-family: 'Inter', sans-serif;
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--dark);
  transition: color 0.2s;
}

.nav-links a:hover { color: var(--orange); }

.nav-btn {
  background: var(--orange) !important;
  color: var(--white) !important;
  padding: 0.55rem 1.35rem;
  border-radius: 100px;
  font-weight: 600 !important;
  font-family: 'Poppins', sans-serif !important;
  font-size: 0.82rem !important;
  letter-spacing: 0.01em;
  box-shadow: 0 4px 14px rgba(242,153,74,0.35);
  transition: transform 0.2s, box-shadow 0.2s, background 0.2s !important;
}

.nav-btn:hover {
  background: var(--orange-d) !important;
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(242,153,74,0.45) !important;
}

/* ─── HERO ─── */
.hero {
  min-height: 100vh;
  padding: 8rem 5rem 5rem;
  display: grid;
  grid-template-columns: 55% 45%;
  align-items: center;
  gap: 3rem;
  position: relative;
  overflow: hidden;
}

/* Background shapes */
.hero-shape {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
}

.shape-1 {
  width: 520px; height: 520px;
  background: var(--orange-l);
  top: -60px; right: -120px;
  border-radius: 40% 60% 55% 45% / 45% 55% 60% 40%;
  animation: morph 8s ease-in-out infinite;
}

.shape-2 {
  width: 300px; height: 300px;
  background: var(--green-l);
  bottom: 40px; right: 180px;
  border-radius: 60% 40% 45% 55% / 55% 45% 60% 40%;
  animation: morph 10s ease-in-out 2s infinite reverse;
  opacity: 0.8;
}

.shape-3 {
  width: 160px; height: 160px;
  background: var(--sky-l);
  top: 200px; right: 460px;
  border-radius: 50%;
  opacity: 0.5;
}

@keyframes morph {
  0%, 100% { border-radius: 40% 60% 55% 45% / 45% 55% 60% 40%; }
  33%  { border-radius: 60% 40% 35% 65% / 65% 35% 55% 45%; }
  66%  { border-radius: 35% 65% 60% 40% / 40% 60% 45% 55%; }
}

/* Hero text */
.hero-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: var(--orange-l);
  border: 1.5px solid var(--orange-m);
  color: var(--orange-d);
  font-family: 'Poppins', sans-serif;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  padding: 0.4rem 1rem;
  border-radius: 100px;
  margin-bottom: 1.6rem;
  animation: fadeUp 0.5s ease both;
}

.eyebrow-dot {
  width: 7px; height: 7px;
  border-radius: 50%;
  background: var(--orange);
  animation: blink 1.8s ease-in-out infinite;
}

@keyframes blink {
  0%,100% { opacity: 1; } 50% { opacity: 0.3; }
}

.hero-title {
  font-family: 'Poppins', sans-serif;
  font-size: clamp(2.8rem, 4.5vw, 4.4rem);
  font-weight: 800;
  line-height: 1.08;
  color: var(--dark-d);
  letter-spacing: -0.03em;
  margin-bottom: 1.4rem;
  animation: fadeUp 0.6s ease 0.08s both;
}

.hero-title .line-green {
  color: var(--green-d);
  position: relative;
  display: inline-block;
}

.hero-title .line-green::after {
  content: '';
  position: absolute;
  bottom: 2px; left: 0; right: 0;
  height: 5px;
  background: var(--green);
  border-radius: 3px;
  opacity: 0.5;
}

.hero-title .line-orange { color: var(--orange); }

.hero-desc {
  font-family: 'Inter', sans-serif;
  font-size: 1rem;
  line-height: 1.75;
  color: #737373;
  font-weight: 300;
  max-width: 490px;
  margin-bottom: 2.4rem;
  animation: fadeUp 0.6s ease 0.16s both;
}

.hero-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
  animation: fadeUp 0.6s ease 0.24s both;
}

.btn-main {
  background: var(--orange);
  color: var(--white);
  padding: 0.9rem 2rem;
  border-radius: 100px;
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  font-size: 0.88rem;
  letter-spacing: 0.01em;
  text-decoration: none;
  box-shadow: 0 6px 20px rgba(242,153,74,0.4);
  transition: all 0.25s;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-main:hover {
  background: var(--orange-d);
  transform: translateY(-2px);
  box-shadow: 0 10px 28px rgba(242,153,74,0.45);
}

.btn-ghost {
  color: var(--dark);
  font-family: 'Inter', sans-serif;
  font-weight: 500;
  font-size: 0.88rem;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  transition: gap 0.2s, color 0.2s;
}

.btn-ghost:hover { color: var(--green-d); gap: 0.65rem; }

.hero-trust {
  margin-top: 2.5rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  animation: fadeUp 0.6s ease 0.32s both;
}

.trust-avatars {
  display: flex;
}

.trust-avatar {
  width: 30px; height: 30px;
  border-radius: 50%;
  border: 2px solid var(--cream);
  margin-left: -8px;
  font-size: 0.7rem;
  font-weight: 700;
  font-family: 'Poppins', sans-serif;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--white);
}

.trust-avatars .trust-avatar:first-child { margin-left: 0; }
.av-1 { background: var(--orange); }
.av-2 { background: var(--green-d); }
.av-3 { background: var(--sky); }
.av-4 { background: #9b59b6; }

.trust-text {
  font-family: 'Inter', sans-serif;
  font-size: 0.78rem;
  color: #9a9a9a;
}

.trust-text strong { color: var(--dark); font-weight: 600; }

/* ─── HERO VISUAL ─── */
.hero-visual {
  position: relative;
  z-index: 2;
  animation: fadeUp 0.7s ease 0.2s both;
}

.main-card {
  background: var(--white);
  border-radius: 28px;
  padding: 1.75rem;
  box-shadow: 0 24px 64px rgba(79,79,79,0.13);
  border: 1px solid rgba(242,153,74,0.1);
  position: relative;
  z-index: 2;
}

.card-top-bar {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1.4rem;
}

.bar-dot { width: 10px; height: 10px; border-radius: 50%; }
.bd-red   { background: #ff5f56; }
.bd-yel   { background: #ffbd2e; }
.bd-grn   { background: #27c93f; }

.card-label {
  margin-left: auto;
  font-family: 'Poppins', sans-serif;
  font-size: 0.68rem;
  font-weight: 600;
  color: #b0b0b0;
  letter-spacing: 0.06em;
  text-transform: uppercase;
}

.pet-header {
  display: flex;
  align-items: center;
  gap: 0.9rem;
  padding-bottom: 1.2rem;
  border-bottom: 1px solid #f3f3f3;
  margin-bottom: 1.2rem;
}

.pet-emo {
  width: 54px; height: 54px;
  background: var(--orange-l);
  border: 2px solid var(--orange-m);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  flex-shrink: 0;
}

.pet-name-lbl {
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  font-size: 1rem;
  color: var(--dark-d);
}

.pet-sub {
  font-size: 0.75rem;
  color: #a0a0a0;
  margin-top: 2px;
  font-family: 'Inter', sans-serif;
}

.chip {
  margin-left: auto;
  padding: 0.28rem 0.75rem;
  border-radius: 100px;
  font-family: 'Poppins', sans-serif;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  flex-shrink: 0;
}

.chip-green { background: var(--green-l); color: var(--green-d); border: 1px solid var(--green-m); }
.chip-orange { background: var(--orange-l); color: var(--orange-d); border: 1px solid var(--orange-m); }
.chip-sky { background: var(--sky-l); color: #1a98be; border: 1px solid rgba(86,204,242,0.25); }

.stats-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.65rem;
  margin-bottom: 1.2rem;
}

.stat-box {
  background: var(--cream);
  border-radius: 12px;
  padding: 0.75rem;
  text-align: center;
}

.stat-box-icon { font-size: 1rem; margin-bottom: 0.2rem; }

.stat-box-val {
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  font-size: 0.78rem;
  color: var(--dark-d);
  line-height: 1.2;
}

.stat-box-lbl {
  font-size: 0.63rem;
  color: #b0b0b0;
  margin-top: 1px;
  font-family: 'Inter', sans-serif;
}

.vax-list { display: flex; flex-direction: column; gap: 0; }

.vax-row {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.55rem 0;
  border-top: 1px solid #f3f3f3;
  font-family: 'Inter', sans-serif;
}

.vax-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.vax-dot-g { background: var(--green); }
.vax-dot-o { background: var(--orange); }

.vax-name { font-size: 0.8rem; font-weight: 500; color: var(--dark-d); }
.vax-date { font-size: 0.72rem; color: #b0b0b0; margin-left: auto; }

/* Floating cards */
.fc {
  position: absolute;
  background: var(--white);
  border-radius: 16px;
  padding: 0.85rem 1rem;
  box-shadow: 0 10px 32px rgba(79,79,79,0.13);
  z-index: 3;
  border: 1px solid rgba(255,255,255,0.9);
}

.fc-1 { top: -28px; right: -36px; min-width: 170px; animation: floatA 4s ease-in-out infinite; }
.fc-2 { bottom: 50px; left: -44px; min-width: 160px; animation: floatA 4s ease-in-out 1.5s infinite; }

@keyframes floatA {
  0%,100% { transform: translateY(0); }
  50% { transform: translateY(-9px); }
}

.fc-icon { font-size: 1rem; margin-bottom: 0.25rem; }
.fc-title { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 0.78rem; color: var(--dark-d); }
.fc-sub { font-size: 0.68rem; color: #b0b0b0; margin-top: 2px; font-family: 'Inter', sans-serif; }

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(22px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ─── STATS BAND ─── */
.stats-band {
  background: var(--orange);
  padding: 1.75rem 5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
}

.sb-item { text-align: center; }

.sb-num {
  font-family: 'Poppins', sans-serif;
  font-weight: 800;
  font-size: 2.2rem;
  color: var(--white);
  line-height: 1;
}

.sb-lbl {
  font-family: 'Inter', sans-serif;
  font-size: 0.78rem;
  color: rgba(255,255,255,0.75);
  margin-top: 0.25rem;
  font-weight: 400;
}

.sb-div { width: 1px; height: 36px; background: rgba(255,255,255,0.25); }

/* ─── SECTION BASE ─── */
section { padding: 6rem 5rem; }

.tag {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-family: 'Poppins', sans-serif;
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  margin-bottom: 1rem;
}

.tag-orange { color: var(--orange-d); }
.tag-green  { color: var(--green-d); }
.tag-sky    { color: #1a98be; }

.tag::before {
  content: '';
  display: block;
  width: 20px;
  height: 2.5px;
  border-radius: 2px;
}

.tag-orange::before { background: var(--orange); }
.tag-green::before  { background: var(--green); }
.tag-sky::before    { background: var(--sky); }

.sec-title {
  font-family: 'Poppins', sans-serif;
  font-size: clamp(1.9rem, 3vw, 2.9rem);
  font-weight: 800;
  line-height: 1.12;
  color: var(--dark-d);
  letter-spacing: -0.03em;
  margin-bottom: 0.85rem;
}

.sec-sub {
  font-family: 'Inter', sans-serif;
  font-size: 0.95rem;
  line-height: 1.72;
  color: #7a7a7a;
  font-weight: 300;
  max-width: 520px;
}

/* ─── FEATURES ─── */
.features-bg { background: var(--cream); }

.feat-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 3rem;
  gap: 2rem;
}

.feat-see-all {
  font-family: 'Inter', sans-serif;
  font-size: 0.82rem;
  font-weight: 500;
  color: var(--orange);
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 0.3rem;
  white-space: nowrap;
  transition: gap 0.2s;
}

.feat-see-all:hover { gap: 0.5rem; }

.feat-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.25rem;
}

.feat-card {
  background: var(--white);
  border-radius: 22px;
  padding: 1.75rem;
  border: 1px solid var(--border);
  transition: transform 0.28s, box-shadow 0.28s;
  position: relative;
  overflow: hidden;
}

.feat-card::after {
  content: '';
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 3px;
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.3s;
  border-radius: 0 0 22px 22px;
}

.feat-card:hover { transform: translateY(-5px); box-shadow: 0 18px 48px rgba(79,79,79,0.10); }
.feat-card:hover::after { transform: scaleX(1); }

.fc-orange::after { background: var(--orange); }
.fc-green::after  { background: var(--green); }
.fc-sky::after    { background: var(--sky); }

.feat-icon {
  width: 50px; height: 50px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.3rem;
  margin-bottom: 1.1rem;
}

.fi-orange { background: var(--orange-l); }
.fi-green  { background: var(--green-l); }
.fi-sky    { background: var(--sky-l); }

.feat-title {
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  font-size: 0.95rem;
  color: var(--dark-d);
  margin-bottom: 0.5rem;
}

.feat-desc {
  font-family: 'Inter', sans-serif;
  font-size: 0.82rem;
  line-height: 1.65;
  color: #888;
  font-weight: 300;
}

/* ─── HOW IT WORKS ─── */
.how-bg { background: var(--dark-d); }

.how-bg .sec-title { color: var(--cream); }
.how-bg .sec-sub   { color: rgba(250,249,246,0.55); max-width: 480px; }
.how-bg .tag-orange { color: var(--orange); }

.steps-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
  margin-top: 3.5rem;
  position: relative;
}

.steps-grid::before {
  content: '';
  position: absolute;
  top: 27px;
  left: 13%;
  right: 13%;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(242,153,74,0.4), rgba(111,207,151,0.4), transparent);
}

.step {
  text-align: center;
  position: relative;
}

.step-num {
  width: 54px; height: 54px;
  border-radius: 50%;
  border: 2px solid rgba(242,153,74,0.35);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.2rem;
  font-family: 'Poppins', sans-serif;
  font-weight: 800;
  font-size: 1.05rem;
  background: rgba(242,153,74,0.06);
  position: relative;
  z-index: 1;
  transition: border-color 0.3s, background 0.3s;
}

.step:hover .step-num {
  border-color: var(--orange);
  background: rgba(242,153,74,0.15);
}

.step-num-1 { color: var(--orange); }
.step-num-2 { color: var(--green); border-color: rgba(111,207,151,0.35); background: rgba(111,207,151,0.06); }
.step-num-3 { color: var(--sky); border-color: rgba(86,204,242,0.35); background: rgba(86,204,242,0.06); }
.step-num-4 { color: var(--orange); }

.step:hover .step-num-2 { border-color: var(--green); background: rgba(111,207,151,0.15); }
.step:hover .step-num-3 { border-color: var(--sky); background: rgba(86,204,242,0.15); }

.step-title {
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  font-size: 0.88rem;
  color: var(--cream);
  margin-bottom: 0.5rem;
}

.step-desc {
  font-family: 'Inter', sans-serif;
  font-size: 0.78rem;
  color: rgba(250,249,246,0.45);
  line-height: 1.6;
  font-weight: 300;
}

/* ─── DUAL PANELS ─── */
.dual { display: grid; grid-template-columns: 1fr 1fr; }

.panel {
  padding: 5.5rem 4.5rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.panel-a { background: var(--green-l); }
.panel-b { background: var(--orange-l); }

.panel-title {
  font-family: 'Poppins', sans-serif;
  font-weight: 800;
  font-size: clamp(1.6rem, 2.5vw, 2.1rem);
  color: var(--dark-d);
  line-height: 1.15;
  letter-spacing: -0.02em;
  margin-bottom: 0.85rem;
}

.panel-desc {
  font-family: 'Inter', sans-serif;
  font-size: 0.88rem;
  line-height: 1.72;
  color: #6e6e6e;
  font-weight: 300;
  max-width: 380px;
  margin-bottom: 1.8rem;
}

.check-list { list-style: none; display: flex; flex-direction: column; gap: 0.65rem; }

.check-list li {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  font-family: 'Inter', sans-serif;
  font-size: 0.85rem;
  color: var(--dark);
  font-weight: 400;
}

.chk {
  width: 22px; height: 22px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.65rem;
  flex-shrink: 0;
  font-weight: 700;
}

.chk-g { background: var(--green-m); color: var(--green-d); }
.chk-o { background: var(--orange-m); color: var(--orange-d); }

/* ─── VETERINARY ─── */
.vet-section {
  background: var(--white);
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4.5rem;
  align-items: center;
  padding: 6rem 5rem;
}

.vet-card {
  background: var(--cream);
  border-radius: 24px;
  padding: 1.75rem;
  border: 1px solid rgba(111,207,151,0.2);
}

.vet-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1.2rem;
  border-bottom: 1px solid #ececec;
}

.vc-title {
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  font-size: 0.95rem;
  color: var(--dark-d);
}

.vc-sub {
  font-family: 'Inter', sans-serif;
  font-size: 0.72rem;
  color: #b0b0b0;
  margin-top: 3px;
}

.timeline { position: relative; padding-left: 1.4rem; }

.timeline::before {
  content: '';
  position: absolute;
  left: 5px; top: 0; bottom: 0;
  width: 2px;
  background: linear-gradient(to bottom, var(--orange), var(--green), var(--sky));
  border-radius: 2px;
  opacity: 0.3;
}

.tl-item {
  position: relative;
  padding: 0.8rem 0;
  border-bottom: 1px solid #f0f0f0;
}

.tl-item:last-child { border-bottom: none; }

.tl-dot {
  position: absolute;
  left: -1.15rem;
  top: 1rem;
  width: 10px; height: 10px;
  border-radius: 50%;
  border: 2px solid var(--white);
}

.dot-o { background: var(--orange); box-shadow: 0 0 0 2px rgba(242,153,74,0.25); }
.dot-g { background: var(--green); box-shadow: 0 0 0 2px rgba(111,207,151,0.25); }
.dot-s { background: var(--sky); box-shadow: 0 0 0 2px rgba(86,204,242,0.25); }

.tl-event {
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  font-size: 0.82rem;
  color: var(--dark-d);
  margin-bottom: 0.25rem;
}

.tl-meta {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  font-family: 'Inter', sans-serif;
  font-size: 0.7rem;
  color: #b0b0b0;
}

.tl-chip {
  padding: 0.12rem 0.5rem;
  border-radius: 100px;
  font-family: 'Poppins', sans-serif;
  font-size: 0.62rem;
  font-weight: 700;
}

.tc-o { background: var(--orange-l); color: var(--orange-d); }
.tc-g { background: var(--green-l);  color: var(--green-d); }
.tc-s { background: var(--sky-l);    color: #1a98be; }

.vet-perks {
  margin-top: 2.2rem;
  display: flex;
  flex-direction: column;
  gap: 1.1rem;
}

.perk {
  display: flex;
  align-items: flex-start;
  gap: 0.9rem;
}

.perk-icon {
  width: 42px; height: 42px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  flex-shrink: 0;
}

.pi-o { background: var(--orange-l); }
.pi-g { background: var(--green-l); }
.pi-s { background: var(--sky-l); }

.perk-title {
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  font-size: 0.88rem;
  color: var(--dark-d);
  margin-bottom: 0.25rem;
}

.perk-desc {
  font-family: 'Inter', sans-serif;
  font-size: 0.8rem;
  color: #888;
  line-height: 1.6;
  font-weight: 300;
}

/* ─── TESTIMONIALS ─── */
.testi-section {
  background: var(--cream);
  padding: 6rem 5rem;
}

.testi-header {
  text-align: center;
  margin-bottom: 3.5rem;
}

.testi-header .sec-sub {
  max-width: 420px;
  margin: 0 auto;
}

.testi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.25rem;
}

.testi-card {
  background: var(--white);
  border-radius: 22px;
  padding: 1.75rem;
  border: 1px solid var(--border);
  transition: transform 0.3s, box-shadow 0.3s;
}

.testi-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 14px 40px rgba(79,79,79,0.09);
}

.stars {
  display: flex;
  gap: 3px;
  margin-bottom: 1rem;
}

.star {
  font-size: 0.85rem;
  color: var(--orange);
}

.testi-quote {
  font-family: 'Inter', sans-serif;
  font-size: 0.875rem;
  line-height: 1.7;
  color: #5a5a5a;
  font-style: italic;
  margin-bottom: 1.4rem;
  font-weight: 300;
}

.testi-author { display: flex; align-items: center; gap: 0.7rem; }

.testi-av {
  width: 38px; height: 38px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  font-size: 0.78rem;
  flex-shrink: 0;
}

.ta-o { background: var(--orange-l); color: var(--orange-d); }
.ta-g { background: var(--green-l); color: var(--green-d); }
.ta-s { background: var(--sky-l); color: #1a98be; }

.testi-name {
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  font-size: 0.82rem;
  color: var(--dark-d);
}

.testi-role {
  font-family: 'Inter', sans-serif;
  font-size: 0.7rem;
  color: #b0b0b0;
  margin-top: 1px;
}

/* ─── CTA ─── */
.cta-section {
  background: var(--green);
  padding: 6rem 5rem;
  text-align: center;
  position: relative;
  overflow: hidden;
}

.cta-section::before {
  content: '';
  position: absolute;
  top: -100px; right: -100px;
  width: 350px; height: 350px;
  background: rgba(255,255,255,0.15);
  border-radius: 50%;
}

.cta-section::after {
  content: '';
  position: absolute;
  bottom: -80px; left: -60px;
  width: 280px; height: 280px;
  background: rgba(242,153,74,0.15);
  border-radius: 50%;
}

.cta-tag {
  display: inline-block;
  background: rgba(255,255,255,0.25);
  color: var(--white);
  font-family: 'Poppins', sans-serif;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  padding: 0.38rem 1rem;
  border-radius: 100px;
  margin-bottom: 1.5rem;
  position: relative;
  z-index: 1;
}

.cta-title {
  font-family: 'Poppins', sans-serif;
  font-weight: 800;
  font-size: clamp(2rem, 4vw, 3.2rem);
  line-height: 1.1;
  color: var(--white);
  letter-spacing: -0.03em;
  margin-bottom: 1rem;
  position: relative;
  z-index: 1;
}

.cta-sub {
  font-family: 'Inter', sans-serif;
  font-size: 0.95rem;
  line-height: 1.7;
  color: rgba(255,255,255,0.8);
  max-width: 420px;
  margin: 0 auto 2.5rem;
  font-weight: 300;
  position: relative;
  z-index: 1;
}

.cta-btns {
  display: flex;
  gap: 1rem;
  justify-content: center;
  align-items: center;
  position: relative;
  z-index: 1;
}

.cta-btn-main {
  background: var(--white);
  color: var(--green-d);
  padding: 0.95rem 2.2rem;
  border-radius: 100px;
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  font-size: 0.88rem;
  text-decoration: none;
  box-shadow: 0 6px 20px rgba(44,44,44,0.15);
  transition: all 0.25s;
}

.cta-btn-main:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(44,44,44,0.2);
  background: var(--cream);
}

.cta-btn-ghost {
  color: var(--white);
  font-family: 'Inter', sans-serif;
  font-weight: 500;
  font-size: 0.88rem;
  text-decoration: none;
  opacity: 0.85;
  display: flex;
  align-items: center;
  gap: 0.4rem;
  transition: opacity 0.2s, gap 0.2s;
}

.cta-btn-ghost:hover { opacity: 1; gap: 0.6rem; }

/* ─── FOOTER ─── */
footer {
  background: var(--dark-d);
  padding: 2.5rem 5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.footer-logo {
  font-family: 'Poppins', sans-serif;
  font-weight: 800;
  font-size: 1.15rem;
  color: var(--cream);
  display: flex;
  align-items: center;
  gap: 0.4rem;
  text-decoration: none;
}

.footer-copy {
  font-family: 'Inter', sans-serif;
  font-size: 0.75rem;
  color: rgba(250,249,246,0.3);
}

.footer-links {
  list-style: none;
  display: flex;
  gap: 1.75rem;
}

.footer-links a {
  font-family: 'Inter', sans-serif;
  font-size: 0.78rem;
  color: rgba(250,249,246,0.45);
  text-decoration: none;
  transition: color 0.2s;
}

.footer-links a:hover { color: var(--orange); }

/* ─── REVEAL ─── */
.reveal {
  opacity: 0;
  transform: translateY(28px);
  transition: opacity 0.6s ease, transform 0.6s ease;
}

.reveal.in { opacity: 1; transform: translateY(0); }
</style>
</head>
<body>

<!-- ═══ NAV ═══ -->
<nav id="navbar">
  <a href="#" class="logo">
    <div class="logo-paw">🐾</div>
    AmigoPet
  </a>
  <ul class="nav-links">
    <li><a href="#como-funciona">Como Funciona</a></li>
    <li><a href="#saude">Saúde Animal</a></li>
    <li><a href="#adocao">Adoção</a></li>
    <li><a href="#cta" class="nav-btn">Começar agora</a></li>
  </ul>
</nav>

<!-- ═══ HERO ═══ -->
<section class="hero">
  <div class="hero-shape shape-1"></div>
  <div class="hero-shape shape-2"></div>
  <div class="hero-shape shape-3"></div>

  <div>
    <div class="hero-eyebrow">
      <span class="eyebrow-dot"></span>
      Plataforma de Bem-Estar Animal
    </div>
    <h1 class="hero-title">
      Cuide, proteja<br>
      e <span class="line-green">reencontre</span><br>
      com <span class="line-orange">amor</span>
    </h1>
    <p class="hero-desc">
      Do alerta de animal perdido à adoção responsável, com prontuário veterinário completo, vacinação, exames e muito mais. Tudo em um só lugar.
    </p>
    <div class="hero-actions">
      <a href="#cta" class="btn-main">Começar gratuitamente →</a>
      <a href="#como-funciona" class="btn-ghost">Ver como funciona ›</a>
    </div>
    <div class="hero-trust">
      <div class="trust-avatars">
        <div class="trust-avatar av-1">MR</div>
        <div class="trust-avatar av-2">JP</div>
        <div class="trust-avatar av-3">KL</div>
        <div class="trust-avatar av-4">TS</div>
      </div>
      <div class="trust-text">
        Mais de <strong>48 mil tutores</strong> já confiam no AmigoPet
      </div>
    </div>
  </div>

  <div class="hero-visual">
    <div class="fc fc-1">
      <div class="fc-icon">🔔</div>
      <div class="fc-title">Animal encontrado!</div>
      <div class="fc-sub">300m de você · agora</div>
    </div>

    <div class="main-card">
      <div class="card-top-bar">
        <div class="bar-dot bd-red"></div>
        <div class="bar-dot bd-yel"></div>
        <div class="bar-dot bd-grn"></div>
        <span class="card-label">Prontuário Digital</span>
      </div>

      <div class="pet-header">
        <div class="pet-emo">🐕</div>
        <div>
          <div class="pet-name-lbl">Bolinha</div>
          <div class="pet-sub">Golden Retriever · 3 anos</div>
        </div>
        <span class="chip chip-green">✓ Saudável</span>
      </div>

      <div class="stats-row">
        <div class="stat-box">
          <div class="stat-box-icon">💉</div>
          <div class="stat-box-val">Em dia</div>
          <div class="stat-box-lbl">Vacinação</div>
        </div>
        <div class="stat-box">
          <div class="stat-box-icon">✂️</div>
          <div class="stat-box-val">Realizada</div>
          <div class="stat-box-lbl">Castração</div>
        </div>
        <div class="stat-box">
          <div class="stat-box-icon">🔬</div>
          <div class="stat-box-val">Mar/25</div>
          <div class="stat-box-lbl">Último exame</div>
        </div>
      </div>

      <div class="vax-list">
        <div class="vax-row">
          <div class="vax-dot vax-dot-g"></div>
          <span class="vax-name">Antirrábica</span>
          <span class="vax-date">Jan 2025</span>
        </div>
        <div class="vax-row">
          <div class="vax-dot vax-dot-g"></div>
          <span class="vax-name">V10 Polivalente</span>
          <span class="vax-date">Mar 2025</span>
        </div>
        <div class="vax-row">
          <div class="vax-dot vax-dot-o"></div>
          <span class="vax-name">Gripe Canina</span>
          <span class="vax-date">Jun 2025 ← próxima</span>
        </div>
      </div>
    </div>

    <div class="fc fc-2">
      <div class="fc-icon">💚</div>
      <div class="fc-title">14 adoções hoje</div>
      <div class="fc-sub">novos lares encontrados</div>
    </div>
  </div>
</section>

<!-- ═══ STATS BAND ═══ -->
<div class="stats-band">
  <div class="sb-item"><div class="sb-num">48k+</div><div class="sb-lbl">Animais cadastrados</div></div>
  <div class="sb-div"></div>
  <div class="sb-item"><div class="sb-num">3.6k</div><div class="sb-lbl">Adoções realizadas</div></div>
  <div class="sb-div"></div>
  <div class="sb-item"><div class="sb-num">91%</div><div class="sb-lbl">Taxa de reencontro</div></div>
  <div class="sb-div"></div>
  <div class="sb-item"><div class="sb-num">320+</div><div class="sb-lbl">Clínicas parceiras</div></div>
  <div class="sb-div"></div>
  <div class="sb-item"><div class="sb-num">4.9★</div><div class="sb-lbl">Avaliação dos usuários</div></div>
</div>

<!-- ═══ FEATURES ═══ -->
<section class="features-bg" id="features">
  <div class="feat-header reveal">
    <div>
      <div class="tag tag-orange">Funcionalidades</div>
      <h2 class="sec-title">Tudo que seu pet precisa,<br>num só lugar</h2>
      <p class="sec-sub">Da comunicação de animais desaparecidos ao prontuário clínico completo — uma plataforma pensada para quem ama animais.</p>
    </div>
    <a href="#" class="feat-see-all">Ver todas as funções →</a>
  </div>

  <div class="feat-grid">
    <div class="feat-card fc-orange reveal">
      <div class="feat-icon fi-orange">🔍</div>
      <div class="feat-title">Animais Perdidos & Encontrados</div>
      <p class="feat-desc">Alertas geolocalizados em tempo real. A comunidade é notificada automaticamente para ajudar a reunir tutores e pets.</p>
    </div>
    <div class="feat-card fc-green reveal">
      <div class="feat-icon fi-green">🏠</div>
      <div class="feat-title">Adoção Responsável</div>
      <p class="feat-desc">Conectamos animais que precisam de um lar com tutores comprometidos, de forma segura e totalmente transparente.</p>
    </div>
    <div class="feat-card fc-sky reveal">
      <div class="feat-icon fi-sky">🩺</div>
      <div class="feat-title">Prontuário Veterinário</div>
      <p class="feat-desc">Registro completo de vacinações, castração, exames e consultas. Histórico clínico acessível a qualquer momento.</p>
    </div>
    <div class="feat-card fc-green reveal">
      <div class="feat-icon fi-green">💉</div>
      <div class="feat-title">Lembretes de Vacinas</div>
      <p class="feat-desc">Notificações automáticas sobre doses de reforço, consultas preventivas e medicamentos contínuos do seu animal.</p>
    </div>
    <div class="feat-card fc-orange reveal">
      <div class="feat-icon fi-orange">📊</div>
      <div class="feat-title">Laudos & Exames</div>
      <p class="feat-desc">Acesse resultados de exames e laudos de forma organizada. Compartilhe com qualquer veterinário com um link seguro.</p>
    </div>
    <div class="feat-card fc-sky reveal">
      <div class="feat-icon fi-sky">🌐</div>
      <div class="feat-title">Rede de Cuidadores</div>
      <p class="feat-desc">Conecte-se com ONGs, abrigos e cuidadores temporários para garantir que nenhum animal fique desamparado.</p>
    </div>
  </div>
</section>

<!-- ═══ HOW IT WORKS ═══ -->
<section class="how-bg" id="como-funciona">
  <div class="reveal">
    <div class="tag tag-orange">Como Funciona</div>
    <h2 class="sec-title">Simples, rápido e eficaz</h2>
    <p class="sec-sub">Em poucos passos, seu animal tem um perfil completo e sua rede de apoio está ativada e pronta para agir.</p>
  </div>

  <div class="steps-grid">
    <div class="step reveal">
      <div class="step-num step-num-1">01</div>
      <div class="step-title">Cadastre seu animal</div>
      <p class="step-desc">Crie o perfil completo com fotos, raça, microchip e dados de saúde em apenas alguns minutos.</p>
    </div>
    <div class="step reveal">
      <div class="step-num step-num-2">02</div>
      <div class="step-title">Gerencie a saúde</div>
      <p class="step-desc">Adicione vacinas, consultas e exames para manter um prontuário sempre atualizado e completo.</p>
    </div>
    <div class="step reveal">
      <div class="step-num step-num-3">03</div>
      <div class="step-title">Ative a comunidade</div>
      <p class="step-desc">Em caso de desaparecimento, alertas chegam a quem está por perto de forma automática e geolocalizada.</p>
    </div>
    <div class="step reveal">
      <div class="step-num step-num-1">04</div>
      <div class="step-title">Conecte & Adote</div>
      <p class="step-desc">Animais disponíveis para adoção encontram o tutor ideal com total transparência e segurança.</p>
    </div>
  </div>
</section>

<!-- ═══ DUAL PANELS ═══ -->
<div class="dual" id="adocao">
  <div class="panel panel-a">
    <div class="tag tag-green">Adoção Responsável</div>
    <div class="panel-title">Cada animal merece<br>um lar de verdade</div>
    <p class="panel-desc">Nosso processo conecta animais com tutores compatíveis, garantindo bem-estar e segurança para todos os lados.</p>
    <ul class="check-list">
      <li><span class="chk chk-g">✓</span> Filtros por porte, raça, idade e localização</li>
      <li><span class="chk chk-g">✓</span> Histórico de saúde disponível ao adotante</li>
      <li><span class="chk chk-g">✓</span> Acompanhamento pós-adoção integrado</li>
      <li><span class="chk chk-g">✓</span> Parceria com abrigos e ONGs verificados</li>
    </ul>
  </div>
  <div class="panel panel-b">
    <div class="tag tag-orange">Perdidos & Encontrados</div>
    <div class="panel-title">Reencontros que<br>mudam histórias</div>
    <p class="panel-desc">Sistema de alerta inteligente que mobiliza a comunidade em segundos para encontrar animais desaparecidos.</p>
    <ul class="check-list">
      <li><span class="chk chk-o">✓</span> Alertas geolocalizados em tempo real</li>
      <li><span class="chk chk-o">✓</span> Notificações push para usuários na região</li>
      <li><span class="chk chk-o">✓</span> Integração com redes sociais e grupos locais</li>
      <li><span class="chk chk-o">✓</span> Identificação visual por foto do animal</li>
    </ul>
  </div>
</div>

<!-- ═══ VETERINARY ═══ -->
<div class="vet-section" id="saude">
  <div class="vet-card reveal">
    <div class="vet-card-header">
      <div>
        <div class="vc-title">Prontuário Clínico Digital</div>
        <div class="vc-sub">Histórico sincronizado automaticamente</div>
      </div>
      <span class="chip chip-green">✓ Atualizado</span>
    </div>

    <div class="timeline">
      <div class="tl-item">
        <div class="tl-dot dot-g"></div>
        <div class="tl-event">Vacinação V10 + Antirrábica</div>
        <div class="tl-meta">
          <span>Mar 2025</span>
          <span class="tl-chip tc-g">Vacina</span>
          <span>Dr. Fernando Alves</span>
        </div>
      </div>
      <div class="tl-item">
        <div class="tl-dot dot-s"></div>
        <div class="tl-event">Hemograma completo + Bioquímica</div>
        <div class="tl-meta">
          <span>Fev 2025</span>
          <span class="tl-chip tc-s">Exame</span>
          <span>Resultados normais</span>
        </div>
      </div>
      <div class="tl-item">
        <div class="tl-dot dot-o"></div>
        <div class="tl-event">Cirurgia de castração</div>
        <div class="tl-meta">
          <span>Jan 2025</span>
          <span class="tl-chip tc-o">Procedimento</span>
          <span>Recuperação: ótima</span>
        </div>
      </div>
      <div class="tl-item">
        <div class="tl-dot dot-s"></div>
        <div class="tl-event">Consulta de rotina + Vermifugação</div>
        <div class="tl-meta">
          <span>Nov 2024</span>
          <span class="tl-chip tc-s">Consulta</span>
          <span>Dra. Ana Souza</span>
        </div>
      </div>
      <div class="tl-item">
        <div class="tl-dot dot-g"></div>
        <div class="tl-event">Gripe Canina + Leishmaniose</div>
        <div class="tl-meta">
          <span>Out 2024</span>
          <span class="tl-chip tc-g">Vacina</span>
          <span>Dr. Marcos Lima</span>
        </div>
      </div>
    </div>
  </div>

  <div class="reveal">
    <div class="tag tag-green">Saúde Veterinária</div>
    <h2 class="sec-title">Prontuário completo,<br>seguro e acessível</h2>
    <p class="sec-sub">Todas as informações clínicas do seu animal armazenadas com segurança — acessíveis de qualquer lugar, a qualquer hora.</p>

    <div class="vet-perks">
      <div class="perk">
        <div class="perk-icon pi-o">📋</div>
        <div>
          <div class="perk-title">Registros centralizados</div>
          <p class="perk-desc">Vacinas, exames, laudos e evoluções em um único perfil digital, organizado em linha do tempo cronológica.</p>
        </div>
      </div>
      <div class="perk">
        <div class="perk-icon pi-g">🔔</div>
        <div>
          <div class="perk-title">Alertas automáticos</div>
          <p class="perk-desc">Lembretes inteligentes para reforços de vacina, consultas preventivas e medicamentos contínuos.</p>
        </div>
      </div>
      <div class="perk">
        <div class="perk-icon pi-s">🔒</div>
        <div>
          <div class="perk-title">Compartilhamento seguro</div>
          <p class="perk-desc">Envie o histórico clínico para qualquer veterinário ou clínica parceira com um link criptografado.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ═══ TESTIMONIALS ═══ -->
<section class="testi-section">
  <div class="testi-header reveal">
    <div class="tag tag-green" style="justify-content: center;">Depoimentos</div>
    <h2 class="sec-title">O que dizem quem já usa</h2>
    <p class="sec-sub">Tutores, veterinários e protetores que transformaram o cuidado animal com o AmigoPet.</p>
  </div>

  <div class="testi-grid">
    <div class="testi-card reveal">
      <div class="stars">⭐⭐⭐⭐⭐</div>
      <p class="testi-quote">"Minha cachorra sumiu e em menos de 2 horas, graças aos alertas do AmigoPet, uma vizinha me ligou dizendo que ela estava com ela. Não tenho palavras para descrever o alívio."</p>
      <div class="testi-author">
        <div class="testi-av ta-o">MC</div>
        <div>
          <div class="testi-name">Maria Clara Oliveira</div>
          <div class="testi-role">Tutora · São Paulo, SP</div>
        </div>
      </div>
    </div>
    <div class="testi-card reveal">
      <div class="stars">⭐⭐⭐⭐⭐</div>
      <p class="testi-quote">"O prontuário digital mudou a rotina da minha clínica. Tenho o histórico de todos os pacientes na palma da mão. A facilidade de acesso é impressionante."</p>
      <div class="testi-author">
        <div class="testi-av ta-g">RM</div>
        <div>
          <div class="testi-name">Dr. Rafael Matos</div>
          <div class="testi-role">Médico Veterinário · Belo Horizonte</div>
        </div>
      </div>
    </div>
    <div class="testi-card reveal">
      <div class="stars">⭐⭐⭐⭐⭐</div>
      <p class="testi-quote">"Adotei meu gato pelo AmigoPet e o processo foi incrível. Ver todo o histórico de vacinação e saúde antes de adotar me deu uma segurança enorme."</p>
      <div class="testi-author">
        <div class="testi-av ta-s">LF</div>
        <div>
          <div class="testi-name">Lucas Ferreira</div>
          <div class="testi-role">Tutor adotante · Rio de Janeiro</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ CTA ═══ -->
<section class="cta-section" id="cta">
  <div class="cta-tag">🐾 Junte-se à comunidade</div>
  <h2 class="cta-title">Pronto para cuidar melhor<br>do seu animal?</h2>
  <p class="cta-sub">Milhares de tutores, veterinários e protetores já confiam no AmigoPet para garantir o bem-estar animal completo.</p>
  <div class="cta-btns">
    <a href="#" class="cta-btn-main">Criar conta gratuita</a>
    <a href="#" class="cta-btn-ghost">Falar com a equipe →</a>
  </div>
</section>

<?php require __DIR__ . '/../includes/dashboard/footer.php'; ?>

<script>
  const obs = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        const siblings = [...entry.target.parentElement.querySelectorAll('.reveal')];
        const idx = siblings.indexOf(entry.target);
        setTimeout(() => entry.target.classList.add('in'), idx * 90);
        obs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });

  document.querySelectorAll('.reveal').forEach(el => obs.observe(el));

  window.addEventListener('scroll', () => {
    document.getElementById('navbar').style.boxShadow =
      window.scrollY > 20 ? '0 4px 24px rgba(79,79,79,0.08)' : 'none';
  });
</script>
</body>
</html>