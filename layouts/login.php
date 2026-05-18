<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>REVV — Sign In</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/login.css">
</head>
<body>

<div class="page">

  <!-- ════ LEFT — Visual Panel ════ -->
  <div class="visual">
    <div class="visual-bg"></div>
    <div class="visual-stripe"></div>
    <div class="visual-grid"></div>

    <!-- Logo -->
    <a class="visual-logo" href="#">
      <div class="logo-mark">
        <svg width="16" height="12" viewBox="0 0 16 12" fill="none">
          <path d="M1 9L4 3h8l3 6" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
          <circle cx="4.5" cy="9.5" r="1.8" fill="white"/>
          <circle cx="11.5" cy="9.5" r="1.8" fill="white"/>
        </svg>
      </div>
      <span class="logo-text">REVV</span>
    </a>

    <!-- Decorative gauge -->
    <svg class="gauge-wrap" viewBox="0 0 80 80" fill="none">
      <circle cx="40" cy="40" r="36" stroke="rgba(232,52,26,0.2)" stroke-width="1" stroke-dasharray="4 4"/>
      <circle cx="40" cy="40" r="28" stroke="rgba(255,255,255,0.06)" stroke-width="1"/>
      <circle cx="40" cy="40" r="20" stroke="rgba(232,52,26,0.15)" stroke-width="1.5" stroke-dasharray="2 3"/>
      <line x1="40" y1="40" x2="40" y2="10" stroke="rgba(232,52,26,0.6)" stroke-width="1.5" stroke-linecap="round"/>
      <circle cx="40" cy="40" r="3" fill="rgba(232,52,26,0.8)"/>
    </svg>

    <!-- Car illustration -->
    <div class="car-wrap">
      <svg viewBox="0 0 520 200" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Car body shadow/base -->
        <ellipse cx="260" cy="185" rx="200" ry="10" fill="rgba(0,0,0,0.4)"/>
        <!-- Main body -->
        <path d="M60 140 L80 100 Q120 60 180 55 L340 55 Q400 60 440 100 L460 140 L460 165 Q460 170 455 170 L65 170 Q60 170 60 165 Z" fill="#1A1F28"/>
        <!-- Body highlight -->
        <path d="M80 100 Q120 60 180 55 L340 55 Q400 60 440 100 L460 140 L440 135 Q400 95 340 90 L180 90 Q130 95 80 140 Z" fill="rgba(255,255,255,0.04)"/>
        <!-- Roof / cabin -->
        <path d="M170 55 Q190 22 220 18 L310 18 Q340 22 350 55 Z" fill="#222831"/>
        <!-- Windshield -->
        <path d="M178 55 Q196 28 222 22 L308 22 Q334 28 344 55 Z" fill="rgba(100,140,200,0.15)" stroke="rgba(100,140,200,0.25)" stroke-width="1"/>
        <!-- Windshield glare -->
        <path d="M190 50 Q205 32 225 25 L260 25 Q240 35 220 52 Z" fill="rgba(255,255,255,0.06)"/>
        <!-- Rear window -->
        <path d="M178 55 L190 55 L195 35 L185 35 Z" fill="rgba(80,120,180,0.1)"/>
        <!-- Side windows -->
        <rect x="195" y="28" width="55" height="27" rx="3" fill="rgba(80,120,180,0.12)" stroke="rgba(100,140,200,0.15)" stroke-width="0.8"/>
        <rect x="258" y="28" width="55" height="27" rx="3" fill="rgba(80,120,180,0.12)" stroke="rgba(100,140,200,0.15)" stroke-width="0.8"/>
        <!-- Door lines -->
        <line x1="253" y1="90" x2="253" y2="165" stroke="rgba(255,255,255,0.06)" stroke-width="1"/>
        <line x1="320" y1="90" x2="320" y2="165" stroke="rgba(255,255,255,0.06)" stroke-width="1"/>
        <!-- Door handles -->
        <rect x="270" y="128" width="20" height="4" rx="2" fill="rgba(255,255,255,0.15)"/>
        <rect x="336" y="128" width="20" height="4" rx="2" fill="rgba(255,255,255,0.15)"/>
        <!-- Headlights -->
        <path d="M440 110 L460 115 L460 135 L440 128 Z" fill="#E8341A" opacity="0.9"/>
        <path d="M440 110 L460 115 L460 120 L440 114 Z" fill="rgba(255,200,100,0.7)"/>
        <!-- Headlight glow -->
        <ellipse cx="470" cy="122" rx="18" ry="8" fill="rgba(232,52,26,0.25)" filter="blur(4px)"/>
        <!-- Taillights -->
        <path d="M60 112 L80 108 L80 130 L60 135 Z" fill="#E8341A" opacity="0.7"/>
        <!-- Front grill -->
        <path d="M440 135 L460 140 L460 155 L440 150 Z" fill="#0E1115" stroke="rgba(232,52,26,0.4)" stroke-width="1"/>
        <line x1="443" y1="137" x2="457" y2="140" stroke="rgba(232,52,26,0.4)" stroke-width="1"/>
        <line x1="443" y1="141" x2="457" y2="144" stroke="rgba(232,52,26,0.4)" stroke-width="1"/>
        <line x1="443" y1="145" x2="457" y2="148" stroke="rgba(232,52,26,0.4)" stroke-width="1"/>
        <!-- Bumpers -->
        <path d="M440 158 L465 162 L468 170 L438 170 Z" fill="#1A1F28"/>
        <path d="M82 158 L58 162 L55 170 L85 170 Z" fill="#1A1F28"/>
        <!-- Side skirt accent -->
        <rect x="100" y="158" width="330" height="5" rx="1" fill="#E8341A" opacity="0.6"/>
        <!-- Wheels -->
        <circle cx="140" cy="170" r="28" fill="#111418"/>
        <circle cx="140" cy="170" r="22" fill="#0D1014"/>
        <circle cx="140" cy="170" r="16" fill="#161B22" stroke="rgba(255,255,255,0.08)" stroke-width="1"/>
        <circle cx="140" cy="170" r="5" fill="#E8341A" opacity="0.8"/>
        <!-- Wheel spokes left -->
        <line x1="140" y1="154" x2="140" y2="162" stroke="rgba(255,255,255,0.2)" stroke-width="2" stroke-linecap="round"/>
        <line x1="151" y1="159" x2="146" y2="165" stroke="rgba(255,255,255,0.2)" stroke-width="2" stroke-linecap="round"/>
        <line x1="156" y1="170" x2="148" y2="170" stroke="rgba(255,255,255,0.2)" stroke-width="2" stroke-linecap="round"/>
        <line x1="151" y1="181" x2="146" y2="175" stroke="rgba(255,255,255,0.2)" stroke-width="2" stroke-linecap="round"/>
        <line x1="140" y1="186" x2="140" y2="178" stroke="rgba(255,255,255,0.2)" stroke-width="2" stroke-linecap="round"/>
        <line x1="129" y1="181" x2="134" y2="175" stroke="rgba(255,255,255,0.2)" stroke-width="2" stroke-linecap="round"/>
        <line x1="124" y1="170" x2="132" y2="170" stroke="rgba(255,255,255,0.2)" stroke-width="2" stroke-linecap="round"/>
        <line x1="129" y1="159" x2="134" y2="165" stroke="rgba(255,255,255,0.2)" stroke-width="2" stroke-linecap="round"/>
        <!-- Wheel right -->
        <circle cx="380" cy="170" r="28" fill="#111418"/>
        <circle cx="380" cy="170" r="22" fill="#0D1014"/>
        <circle cx="380" cy="170" r="16" fill="#161B22" stroke="rgba(255,255,255,0.08)" stroke-width="1"/>
        <circle cx="380" cy="170" r="5" fill="#E8341A" opacity="0.8"/>
        <line x1="380" y1="154" x2="380" y2="162" stroke="rgba(255,255,255,0.2)" stroke-width="2" stroke-linecap="round"/>
        <line x1="391" y1="159" x2="386" y2="165" stroke="rgba(255,255,255,0.2)" stroke-width="2" stroke-linecap="round"/>
        <line x1="396" y1="170" x2="388" y2="170" stroke="rgba(255,255,255,0.2)" stroke-width="2" stroke-linecap="round"/>
        <line x1="391" y1="181" x2="386" y2="175" stroke="rgba(255,255,255,0.2)" stroke-width="2" stroke-linecap="round"/>
        <line x1="380" y1="186" x2="380" y2="178" stroke="rgba(255,255,255,0.2)" stroke-width="2" stroke-linecap="round"/>
        <line x1="369" y1="181" x2="374" y2="175" stroke="rgba(255,255,255,0.2)" stroke-width="2" stroke-linecap="round"/>
        <line x1="364" y1="170" x2="372" y2="170" stroke="rgba(255,255,255,0.2)" stroke-width="2" stroke-linecap="round"/>
        <line x1="369" y1="159" x2="374" y2="165" stroke="rgba(255,255,255,0.2)" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </div>

    <div class="car-shadow"></div>
    <div class="road-line"></div>

    <!-- Feature pills -->
    <div class="float-pills">
      <div class="fpill">
        <div class="fpill-icon">
          <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M6 1L7.5 4.5H11L8 6.5 9 10 6 8 3 10 4 6.5 1 4.5h3.5L6 1z" fill="#E8341A"/></svg>
        </div>
        500+ Premium Vehicles
      </div>
      <div class="fpill">
        <div class="fpill-icon">
          <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><circle cx="6" cy="6" r="4.5" stroke="#E8341A" stroke-width="1.2"/><path d="M6 3.5v2.5l1.5 1.5" stroke="#E8341A" stroke-width="1.2" stroke-linecap="round"/></svg>
        </div>
        24/7 Roadside Assist
      </div>
      <div class="fpill">
        <div class="fpill-icon">
          <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="#E8341A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        Free Cancellation
      </div>
    </div>

    <!-- Bottom copy -->
    <div class="visual-copy">
      <div class="visual-tag"><div class="tag-dot"></div>Premium Car Rental</div>
      <div class="visual-headline">Drive the<br>road your<br><span class="accent">way.</span></div>
      <div class="visual-desc">Thousands of vehicles ready to go. Seamless booking, transparent pricing, and a fleet for every journey.</div>
      <div class="stats-row">
        <div class="stat">
          <div class="stat-val">500<span>+</span></div>
          <div class="stat-lbl">Vehicles</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat">
          <div class="stat-val">48<span>h</span></div>
          <div class="stat-lbl">Max Booking</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat">
          <div class="stat-val">4.9<span>★</span></div>
          <div class="stat-lbl">Rating</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ════ RIGHT — Form Panel ════ -->
  <div class="form-side">
    <div class="form-container">
      <button type="button" class="theme-toggle" id="themeToggle" aria-label="Toggle light mode">
        <span class="theme-toggle-icon">☀️</span>
        <span class="theme-toggle-label">Light mode</span>
      </button>

      <!-- Mobile-only logo -->
      <div class="mobile-logo">
        <div class="logo-mark">
          <svg width="14" height="10" viewBox="0 0 16 12" fill="none"><path d="M1 9L4 3h8l3 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="4.5" cy="9.5" r="1.8" fill="currentColor"/><circle cx="11.5" cy="9.5" r="1.8" fill="currentColor"/></svg>
        </div>
        <span class="logo-text">REVV</span>
      </div>

      <div class="form-eyebrow">Welcome back</div>
      <div class="form-title">Sign In</div>
      <div class="form-subtitle">Enter your credentials to access your account.</div>

      <form id="loginForm" novalidate>

        <!-- Email -->
        <div class="field-group" id="emailGroup">
          <div class="field-label">Email Address</div>
          <div class="input-wrap">
            <svg class="input-icon" width="18" height="18" viewBox="0 0 18 18" fill="none">
              <rect x="2" y="4" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.4"/>
              <path d="M2 6l7 5 7-5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
            </svg>
            <input class="field-input" id="emailInput" type="email" placeholder="you@example.com" autocomplete="email">
          </div>
          <div class="field-error" id="emailError">Please enter a valid email address.</div>
        </div>

        <!-- Password -->
        <div class="field-group" id="passwordGroup">
          <div class="field-label">
            Password
            <a href="#">Forgot password?</a>
          </div>
          <div class="input-wrap">
            <svg class="input-icon" width="18" height="18" viewBox="0 0 18 18" fill="none">
              <rect x="3" y="8" width="12" height="8" rx="2" stroke="currentColor" stroke-width="1.4"/>
              <path d="M6 8V6a3 3 0 1 1 6 0v2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
              <circle cx="9" cy="12" r="1.3" fill="currentColor"/>
            </svg>
            <input class="field-input" id="passwordInput" type="password" placeholder="••••••••" autocomplete="current-password">
            <button type="button" class="eye-toggle" id="eyeToggle" aria-label="Toggle password visibility">
              <svg id="eyeIcon" width="18" height="18" viewBox="0 0 18 18" fill="none">
                <path d="M1 9s3.5-5.5 8-5.5S17 9 17 9s-3.5 5.5-8 5.5S1 9 1 9z" stroke="currentColor" stroke-width="1.4"/>
                <circle cx="9" cy="9" r="2.5" stroke="currentColor" stroke-width="1.4"/>
              </svg>
            </button>
          </div>
          <div class="field-error" id="passwordError">Password must be at least 6 characters.</div>
        </div>

        <!-- Options row -->
        <div class="options-row">
          <label class="checkbox-label">
            <input class="checkbox-input" type="checkbox" id="rememberMe">
            <div class="checkbox-box">
              <svg class="checkmark" width="10" height="8" viewBox="0 0 10 8" fill="none">
                <path d="M1 4l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <span class="checkbox-text">Keep me signed in</span>
          </label>
        </div>

        <!-- Submit -->
        <button class="btn-submit" type="submit" id="submitBtn">
          <div class="btn-text" style="display:flex;align-items:center;gap:10px">
            <span>Access My Account</span>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
              <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="spinner"></div>
        </button>

      </form>

      <div class="signup-row">
        New to REVV? <a href="http://localhost/rent/layouts/signup.php">Create a free account →</a>
      </div>

    </div>
  </div>

</div>

<!-- Toast -->
<div class="toast" id="toast">
  <svg width="16" height="16" viewBox="0 0 16 16" fill="none" id="toastIcon">
    <circle cx="8" cy="8" r="6.5" stroke="#E8341A" stroke-width="1.4"/>
    <path d="M8 5v3" stroke="#E8341A" stroke-width="1.6" stroke-linecap="round"/>
    <circle cx="8" cy="11" r="0.8" fill="#E8341A"/>
  </svg>
  <span id="toastMsg">Please fix the errors above.</span>
</div>
<script src="../javascript/login.js"></script>
</body>
</html>