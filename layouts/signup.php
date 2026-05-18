<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>REVV — Create Account</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/signup.css">
</head>
<body>

<div class="page">

  <!-- ══════════════════════════════════
       SIDEBAR
  ══════════════════════════════════ -->
  <aside class="sidebar">
    <div class="sidebar-bg"></div>
    <div class="sidebar-grid"></div>
    <div class="sidebar-stripe"></div>

    <a class="sidebar-logo" href="carrental_login.html">
      <div class="logo-hex">
        <svg width="16" height="12" viewBox="0 0 16 12" fill="none">
          <path d="M1 9L4 3h8l3 6" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
          <circle cx="4.5" cy="9.5" r="1.8" fill="white"/>
          <circle cx="11.5" cy="9.5" r="1.8" fill="white"/>
        </svg>
      </div>
      <span class="logo-wordmark">REVV</span>
    </a>

    <div class="steps-wrap">
      <div class="steps-title">Registration Steps</div>
      <div class="steps" id="sideSteps">
        <div class="step-item active" data-step="1">
          <div class="step-num">1</div>
          <div class="step-content">
            <div class="step-label">Personal Info</div>
            <div class="step-desc">Name, gender, age &amp; birthdate</div>
          </div>
        </div>
        <div class="step-item" data-step="2">
          <div class="step-num">2</div>
          <div class="step-content">
            <div class="step-label">Contact Details</div>
            <div class="step-desc">Phone, email &amp; address</div>
          </div>
        </div>
        <div class="step-item" data-step="3">
          <div class="step-num">3</div>
          <div class="step-content">
            <div class="step-label">Account Security</div>
            <div class="step-desc">Password &amp; confirmation</div>
          </div>
        </div>
      </div>
    </div>

    <div class="progress-wrap">
      <div class="progress-label">
        <span>Progress</span>
        <strong id="progressPct">33%</strong>
      </div>
      <div class="progress-track">
        <div class="progress-fill" id="progressFill"></div>
      </div>
    </div>
  </aside>

  <!-- ══════════════════════════════════
       FORM MAIN
  ══════════════════════════════════ -->
  <main class="form-main">
    <div class="form-topbar">
      <div class="topbar-heading">
        <div class="topbar-eyebrow">New Account</div>
        <div class="topbar-title">Create Account</div>
      </div>
      <div class="topbar-signin">Already a member? <a href="http://localhost/rent/layouts/login.php">Sign in →</a></div>      <button type="button" class="theme-toggle" id="themeToggle" aria-label="Toggle light mode">
        <span class="theme-toggle-icon">☀️</span>
        <span class="theme-toggle-label">Light mode</span>
      </button>    </div>

    <!-- Mobile step pills -->
    <div class="mobile-steps" id="mobilePills">
      <div class="mobile-step-pill active" data-step="1">1. Personal</div>
      <div class="mobile-step-pill" data-step="2">2. Contact</div>
      <div class="mobile-step-pill" data-step="3">3. Security</div>
    </div>

    <div class="form-scroll">

      <!-- ── STEP 1: Personal Info ── -->
      <div class="step-panel active" id="panel1">
        <div class="panel-title">Personal Information</div>
        <div class="panel-desc">Tell us a bit about yourself to get started with REVV.</div>

        <!-- First Name + Last Name -->
        <div class="field-row col-2">
          <div class="field-group" id="grp-firstName">
            <div class="field-label">First Name <span class="req">*</span></div>
            <div class="input-wrap has-icon">
              <svg class="input-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" color="white"><circle cx="8" cy="5.5" r="3" stroke="currentColor" stroke-width="1.4"/><path d="M2 14c0-3.3 2.7-5.5 6-5.5s6 2.2 6 5.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
              <input class="field-input" id="firstName" type="text" placeholder="Maria" autocomplete="given-name">
            </div>
            <div class="field-error">Please enter your first name.</div>
          </div>
          <div class="field-group" id="grp-lastName">
            <div class="field-label">Last Name <span class="req">*</span></div>
            <div class="input-wrap has-icon">
              <svg class="input-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" color="white"><circle cx="8" cy="5.5" r="3" stroke="currentColor" stroke-width="1.4"/><path d="M2 14c0-3.3 2.7-5.5 6-5.5s6 2.2 6 5.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
              <input class="field-input" id="lastName" type="text" placeholder="Reyes" autocomplete="family-name">
            </div>
            <div class="field-error">Please enter your last name.</div>
          </div>
        </div>

        <!-- Gender -->
        <div class="field-row col-1">
          <div class="field-group" id="grp-gender">
            <div class="field-label">Gender <span class="req">*</span></div>
            <div class="gender-row" id="genderRow">
              <label class="gender-opt" data-val="male">
                <input type="radio" name="gender" value="male">
                <span class="gender-icon">♂</span> Male
              </label>
              <label class="gender-opt" data-val="female">
                <input type="radio" name="gender" value="female">
                <span class="gender-icon">♀</span> Female
              </label>
              <label class="gender-opt" data-val="other">
                <input type="radio" name="gender" value="other">
                <span class="gender-icon">⚧</span> Other
              </label>
              <label class="gender-opt" data-val="prefer-not">
                <input type="radio" name="gender" value="prefer-not">
                <span class="gender-icon">—</span> Prefer not
              </label>
            </div>
            <div class="field-error">Please select your gender.</div>
          </div>
        </div>

        <!-- Age + Birthdate -->
        <div class="field-row col-2">
          <div class="field-group" id="grp-age">
            <div class="field-label">Age <span class="req">*</span></div>
            <div class="input-wrap has-icon">
              <svg class="input-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" color="white"><rect x="2" y="2" width="12" height="12" rx="2" stroke="currentColor" stroke-width="1.4"/><path d="M8 6v4M6 8h4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
              <input class="field-input" id="age" type="number" placeholder="25" min="18" max="100" inputmode="numeric">
            </div>
            <div class="field-error">Must be 18 or older to rent.</div>
          </div>
          <div class="field-group" id="grp-birthdate">
            <div class="field-label">Birth Date <span class="req">*</span></div>
            <div class="input-wrap has-icon">
              <svg class="input-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" color="white"><rect x="2" y="3" width="12" height="11" rx="2" stroke="currentColor" stroke-width="1.4"/><path d="M5 2v2M11 2v2M2 7h12" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
              <input class="field-input" id="birthdate" type="date" max="">
            </div>
            <div class="field-error">Please enter a valid birth date.</div>
          </div>
        </div>

        <div class="form-nav">
          <button class="btn-next" type="button" onclick="goNext(1)">
            <span class="btn-next-text" style="display:flex;align-items:center;gap:8px">
              Continue
              <svg width="15" height="15" viewBox="0 0 15 15" fill="none" color="white"><path d="M3 7.5h9M8.5 4l4 3.5-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <div class="spinner"></div>
          </button>
        </div>
      </div>

      <!-- ── STEP 2: Contact Details ── -->
      <div class="step-panel" id="panel2">
        <div class="panel-title">Contact Details</div>
        <div class="panel-desc">How can we reach you? All fields are required.</div>

        <!-- Phone -->
        <div class="field-row col-1">
          <div class="field-group" id="grp-phone">
            <div class="field-label">Mobile Number <span class="req">*</span></div>
            <div class="phone-wrap">
              <div class="phone-prefix">
                <span>🇵🇭</span> +63
              </div>
              <div class="input-wrap has-icon" style="flex:1">
                <svg class="input-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" color="white"><rect x="4" y="1" width="8" height="14" rx="2" stroke="currentColor" stroke-width="1.4"/><circle cx="8" cy="12" r="0.8" fill="currentColor"/><path d="M6.5 3h3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                <input class="field-input" id="phone" type="tel" placeholder="912 345 6789" inputmode="tel" maxlength="11">
              </div>
            </div>
            <div class="field-error" id="phoneErr">Please enter a valid 10-digit mobile number.</div>
          </div>
        </div>

        <!-- Email -->
        <div class="field-row col-1">
          <div class="field-group" id="grp-email">
            <div class="field-label">Email Address <span class="req">*</span></div>
            <div class="input-wrap has-icon">
              <svg class="input-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" color="white"><rect x="1" y="3" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.4"/><path d="M1 5l7 5 7-5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
              <input class="field-input" id="email" type="email" placeholder="you@example.com" autocomplete="email">
            </div>
            <div class="field-error">Please enter a valid email address.</div>
          </div>
        </div>

        <!-- Address -->
        <div class="section-divider">
          <div class="div-line"></div>
          <span class="div-label">Residential Address</span>
          <div class="div-line"></div>
        </div>

        <!-- Street -->
        <div class="field-row col-1">
          <div class="field-group" id="grp-street">
            <div class="field-label">Street / Barangay <span class="req">*</span></div>
            <div class="input-wrap has-icon">
              <svg class="input-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" color="white"><path d="M8 1.5C5.5 1.5 3.5 3.5 3.5 6c0 3.5 4.5 8.5 4.5 8.5S12.5 9.5 12.5 6C12.5 3.5 10.5 1.5 8 1.5z" stroke="currentColor" stroke-width="1.4"/><circle cx="8" cy="6" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg>
              <input class="field-input" id="street" type="text" placeholder="123 Rizal St, Brgy. San Antonio" autocomplete="street-address">
            </div>
            <div class="field-error">Please enter your street address.</div>
          </div>
        </div>

        <!-- City + Province + ZIP -->
        <div class="field-row col-3">
          <div class="field-group" id="grp-city">
            <div class="field-label">City <span class="req">*</span></div>
            <input class="field-input" id="city" type="text" placeholder="Davao City" autocomplete="address-level2">
            <div class="field-error">Required.</div>
          </div>
          <div class="field-group" id="grp-province">
            <div class="field-label">Province <span class="req">*</span></div>
            <input class="field-input" id="province" type="text" placeholder="Davao del Sur" autocomplete="address-level1">
            <div class="field-error">Required.</div>
          </div>
          <div class="field-group" id="grp-zip">
            <div class="field-label">ZIP Code <span class="req">*</span></div>
            <input class="field-input" id="zip" type="text" placeholder="8000" inputmode="numeric" maxlength="4">
            <div class="field-error">Enter 4-digit ZIP.</div>
          </div>
        </div>

        <div class="form-nav">
          <button class="btn-back" type="button" onclick="goPrev(2)">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="currentColor"><path d="M9 3L5 7l4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Back
          </button>
          <button class="btn-next" type="button" onclick="goNext(2)">
            <span class="btn-next-text" style="display:flex;align-items:center;gap:8px">
              Continue
              <svg width="15" height="15" viewBox="0 0 15 15" fill="none" color="white"><path d="M3 7.5h9M8.5 4l4 3.5-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <div class="spinner"></div>
          </button>
        </div>
      </div>

      <!-- ── STEP 3: Account Security ── -->
      <div class="step-panel" id="panel3">
        <div class="panel-title">Account Security</div>
        <div class="panel-desc">Create a strong password to protect your REVV account.</div>

        <!-- Password -->
        <div class="field-row col-1">
          <div class="field-group" id="grp-password">
            <div class="field-label">Password <span class="req">*</span></div>
            <div class="input-wrap has-icon">
              <svg class="input-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" color="white"><rect x="2.5" y="7" width="11" height="7.5" rx="2" stroke="currentColor" stroke-width="1.4"/><path d="M5 7V5a3 3 0 1 1 6 0v2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/><circle cx="8" cy="10.5" r="1.2" fill="currentColor"/></svg>
              <input class="field-input" id="password" type="password" placeholder="At least 8 characters" autocomplete="new-password">
              <button type="button" class="eye-toggle" id="eyePass" aria-label="Toggle password">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="none" color="white"><path d="M1 8.5s3-5 7.5-5 7.5 5 7.5 5-3 5-7.5 5-7.5-5-7.5-5z" stroke="currentColor" stroke-width="1.4"/><circle cx="8.5" cy="8.5" r="2.3" stroke="currentColor" stroke-width="1.4"/></svg>
              </button>
            </div>
            <!-- Strength bars -->
            <div class="strength-bars" id="strengthBars">
              <div class="sbar" id="sb1"></div>
              <div class="sbar" id="sb2"></div>
              <div class="sbar" id="sb3"></div>
              <div class="sbar" id="sb4"></div>
            </div>
            <div class="strength-label">
              <span style="font-size:10.5px;color:var(--muted)">Password strength</span>
              <span id="strengthText" style="color:var(--muted)">—</span>
            </div>
            <div class="field-error">Password must be at least 8 characters.</div>
          </div>
        </div>

        <!-- Requirements -->
        <div style="background:var(--card);border:1px solid var(--border);border-radius:3px;padding:14px 16px;margin-bottom:4px;">
          <div style="font-size:10.5px;letter-spacing:1.2px;text-transform:uppercase;color:var(--muted);margin-bottom:10px;font-weight:500;">Requirements</div>
          <div style="display:flex;flex-direction:column;gap:6px;">
            <div class="req-item" id="req-len" style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:var(--muted)">
              <svg width="13" height="13" viewBox="0 0 13 13" fill="none" id="ri-len"><circle cx="6.5" cy="6.5" r="5.5" stroke="currentColor" stroke-width="1.2"/></svg>
              At least 8 characters
            </div>
            <div class="req-item" id="req-upper" style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:var(--muted)">
              <svg width="13" height="13" viewBox="0 0 13 13" fill="none" id="ri-upper"><circle cx="6.5" cy="6.5" r="5.5" stroke="currentColor" stroke-width="1.2"/></svg>
              One uppercase letter
            </div>
            <div class="req-item" id="req-num" style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:var(--muted)">
              <svg width="13" height="13" viewBox="0 0 13 13" fill="none" id="ri-num"><circle cx="6.5" cy="6.5" r="5.5" stroke="currentColor" stroke-width="1.2"/></svg>
              One number
            </div>
            <div class="req-item" id="req-special" style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:var(--muted)">
              <svg width="13" height="13" viewBox="0 0 13 13" fill="none" id="ri-special"><circle cx="6.5" cy="6.5" r="5.5" stroke="currentColor" stroke-width="1.2"/></svg>
              One special character (!@#$...)
            </div>
          </div>
        </div>

        <!-- Confirm Password -->
        <div class="field-row col-1" style="margin-top:16px">
          <div class="field-group" id="grp-confirm">
            <div class="field-label">Confirm Password <span class="req">*</span></div>
            <div class="input-wrap has-icon">
              <svg class="input-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" color="white"><rect x="2.5" y="7" width="11" height="7.5" rx="2" stroke="currentColor" stroke-width="1.4"/><path d="M5 7V5a3 3 0 1 1 6 0v2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/><path d="M5.5 10.5l2 2 3-3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
              <input class="field-input" id="confirmPass" type="password" placeholder="Re-enter your password" autocomplete="new-password">
              <button type="button" class="eye-toggle" id="eyeConfirm" aria-label="Toggle confirm password">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="none" color="white"><path d="M1 8.5s3-5 7.5-5 7.5 5 7.5 5-3 5-7.5 5-7.5-5-7.5-5z" stroke="currentColor" stroke-width="1.4"/><circle cx="8.5" cy="8.5" r="2.3" stroke="currentColor" stroke-width="1.4"/></svg>
              </button>
            </div>
            <div class="field-error">Passwords do not match.</div>
          </div>
        </div>

        <!-- Terms -->
        <div style="margin-top:20px;display:flex;flex-direction:column;gap:10px;">
          <label class="check-label" id="grp-terms">
            <input class="check-input" type="checkbox" id="terms">
            <div class="check-box">
              <svg class="check-mark" width="10" height="8" viewBox="0 0 10 8" fill="none"><path d="M1 4l3 3 5-5" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <span class="check-text">I agree to REVV's <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span>
          </label>
          <label class="check-label">
            <input class="check-input" type="checkbox" id="promo">
            <div class="check-box">
              <svg class="check-mark" width="10" height="8" viewBox="0 0 10 8" fill="none"><path d="M1 4l3 3 5-5" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <span class="check-text">Send me exclusive deals and rental promotions</span>
          </label>
        </div>

        <div class="form-nav">
          <button class="btn-back" type="button" onclick="goPrev(3)">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="currentColor"><path d="M9 3L5 7l4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Back
          </button>
          <button class="btn-next" type="button" id="submitBtn" onclick="submitForm()">
            <span class="btn-next-text" style="display:flex;align-items:center;gap:8px">
              <svg width="15" height="15" viewBox="0 0 15 15" fill="none" color="white"><path d="M2 7.5l4 4 7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Create My Account
            </span>
            <div class="spinner"></div>
          </button>
        </div>
      </div>

      <!-- ── SUCCESS ── -->
      <div class="success-screen" id="successScreen">
        <div class="success-icon">
          <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
            <path d="M8 20l8 8 16-16" stroke="#3DBE7A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="success-title">You're on the road!</div>
        <div class="success-desc">
          Welcome to REVV. Your account has been created successfully.<br>
          Browse hundreds of premium vehicles and book your first ride.
        </div>
        <a class="btn-go" href="http://localhost/rent/layouts/login.php">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" color="white"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Sign In Now
        </a>
      </div>

    </div><!-- /form-scroll -->
  </main>
</div>

<!-- Toast -->
<div class="toast" id="toast">
  <svg width="15" height="15" viewBox="0 0 15 15" fill="none" id="toastIcon">
    <circle cx="7.5" cy="7.5" r="6" stroke="#E8341A" stroke-width="1.3"/>
    <path d="M7.5 5v3" stroke="#E8341A" stroke-width="1.5" stroke-linecap="round"/>
    <circle cx="7.5" cy="10" r="0.7" fill="#E8341A"/>
  </svg>
  <span id="toastMsg">Please fix the errors above.</span>
</div>

<script src="../javascript/signup.js"></script>
</body>
</html>