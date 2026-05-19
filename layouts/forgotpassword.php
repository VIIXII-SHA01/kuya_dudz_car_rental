<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>REVV — Forgot Password</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/rent/css/forgotpassword.css">
</head>
<body>

<div class="page">

  <!-- ══════════════════════════════════
       LEFT — Visual
  ══════════════════════════════════ -->
  <div class="visual">
    <div class="visual-bg"></div>
    <div class="visual-grid"></div>
    <div class="visual-stripe"></div>

    <!-- Logo -->
    <a class="visual-logo" href="carrental_login.html">
      <div class="logo-hex">
        <svg width="16" height="12" viewBox="0 0 16 12" fill="none">
          <path d="M1 9L4 3h8l3 6" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
          <circle cx="4.5" cy="9.5" r="1.8" fill="white"/>
          <circle cx="11.5" cy="9.5" r="1.8" fill="white"/>
        </svg>
      </div>
      <span class="logo-wordmark">REVV</span>
    </a>

    <!-- Central illustration -->
    <div class="visual-center">
      <div class="lock-scene">
        <div class="lock-ring-outer"></div>
        <div class="lock-ring-mid"></div>
        <div class="lock-ring-inner"></div>

        <!-- Floating dots -->
        <div class="float-dot fd1"></div>
        <div class="float-dot fd2"></div>
        <div class="float-dot fd3"></div>
        <div class="float-dot fd4"></div>

        <!-- Centre lock icon -->
        <div class="lock-icon-wrap">
          <div class="lock-bg">
            <svg width="46" height="52" viewBox="0 0 46 52" fill="none">
              <!-- Shackle -->
              <path d="M10 22V16C10 8.82 15.82 3 23 3s13 5.82 13 13v6" stroke="#E8341A" stroke-width="2.5" stroke-linecap="round"/>
              <!-- Body -->
              <rect x="4" y="22" width="38" height="27" rx="4" fill="#1E242D" stroke="rgba(232,52,26,0.35)" stroke-width="1.5"/>
              <!-- Top shine -->
              <rect x="5" y="23" width="36" height="4" rx="2" fill="rgba(255,255,255,0.04)"/>
              <!-- Keyhole -->
              <circle cx="23" cy="35" r="5" fill="rgba(232,52,26,0.15)" stroke="#E8341A" stroke-width="1.5"/>
              <rect x="21" y="38" width="4" height="6" rx="1" fill="#E8341A" opacity="0.8"/>
              <!-- Dot glow -->
              <circle cx="23" cy="35" r="2" fill="#E8341A" opacity="0.6"/>
            </svg>
          </div>
        </div>

        <!-- Email particles -->
        <div class="particle-wrap">
          <div class="particle"></div>
          <div class="particle"></div>
          <div class="particle"></div>
        </div>
      </div>

      <!-- Copy -->
      <div class="visual-copy">
        <div class="vis-tag"><div class="tag-dot"></div>Account Recovery</div>
        <div class="vis-headline">Reset your<br>access <span class="accent">fast.</span></div>
        <div class="vis-desc">A 6-digit code will be sent to your registered email address. Use it to verify your identity and create a new password.</div>
      </div>
    </div>

    <!-- Bottom pills -->
    <div class="vis-pills">
      <div class="vpill">
        <div class="vpill-icon">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="#E8341A"><rect x="1.5" y="2.5" width="10" height="8" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M1.5 4l5 3.5 5-3.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
        </div>
        Code sent to email
      </div>
      <div class="vpill">
        <div class="vpill-icon">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="#E8341A"><circle cx="6.5" cy="6.5" r="5" stroke="currentColor" stroke-width="1.2"/><path d="M6.5 4v3l2 2" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
        </div>
        Valid for 5 minutes
      </div>
      <div class="vpill">
        <div class="vpill-icon">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="#E8341A"><path d="M2 6.5l3.5 3.5 5.5-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        Safe & encrypted
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════════
       RIGHT — Form Panel
  ══════════════════════════════════ -->
  <div class="form-side">
    <div class="form-box">

      <!-- Theme toggle -->
      <button type="button" class="theme-toggle" id="themeToggle" aria-label="Toggle light mode">
        <span class="theme-toggle-icon">☀️</span>
        <span class="theme-toggle-label">Light mode</span>
      </button>

      <!-- Mobile logo -->
      <div class="mobile-logo">
        <div class="logo-hex">
          <svg width="14" height="10" viewBox="0 0 16 12" fill="none"><path d="M1 9L4 3h8l3 6" stroke="white" stroke-width="1.8" stroke-linecap="round"/><circle cx="4.5" cy="9.5" r="1.8" fill="white"/><circle cx="11.5" cy="9.5" r="1.8" fill="white"/></svg>
        </div>
        <span class="logo-wordmark">REVV</span>
      </div>

      <!-- Back to login -->
      <a class="back-link" href="carrental_login.html">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" color="currentColor">
          <path d="M10 4L6 8l4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Back to Sign In
      </a>

      <!-- Step dots -->
      <div class="step-dots" id="stepDots">
        <div class="step-dot active" id="dot1"></div>
        <div class="step-dot idle"   id="dot2"></div>
        <div class="step-dot idle"   id="dot3"></div>
      </div>

      <!-- ── STAGE 1: Enter Email ── -->
      <div class="stage active" id="stage1">
        <div class="stage-eyebrow">Step 1 of 3</div>
        <div class="stage-title">Forgot Password</div>
        <div class="stage-desc">Enter the email address linked to your REVV account and we'll send you a reset code.</div>

        <div class="field-group" id="grp-email">
          <label class="field-label" for="emailInput">Email Address</label>
          <div class="input-wrap">
            <svg class="input-icon" width="18" height="18" viewBox="0 0 18 18" fill="none" color="white">
              <rect x="2" y="4" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.4"/>
              <path d="M2 6l7 5 7-5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
            </svg>
            <input class="field-input" id="emailInput" type="email" placeholder="you@example.com" autocomplete="email">
          </div>
          <div class="field-error">
            <svg width="11" height="11" viewBox="0 0 11 11" fill="none"><circle cx="5.5" cy="5.5" r="4.5" stroke="#E8341A" stroke-width="1.1"/><path d="M5.5 3.5v2.5" stroke="#E8341A" stroke-width="1.2" stroke-linecap="round"/><circle cx="5.5" cy="7.5" r="0.6" fill="#E8341A"/></svg>
            Please enter a valid email address.
          </div>
        </div>

        <button class="btn-primary" id="sendCodeBtn" type="button" onclick="sendCode()">
          <span class="btn-text" style="display:flex;align-items:center;gap:9px">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" color="white" style="position:relative;z-index:1"><path d="M2 8l12-6-5 12-2-5-5-1z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span style="position:relative;z-index:1">Send Reset Code</span>
          </span>
          <div class="spinner"></div>
        </button>

        <div style="text-align:center;margin-top:20px;font-size:13px;color:var(--muted2)">
          Remember your password? <a href="carrental_login.html" style="color:var(--red);text-decoration:none;font-weight:500">Sign in →</a>
        </div>
      </div>

      <!-- ── STAGE 2: Enter OTP ── -->
      <div class="stage" id="stage2">
        <div class="stage-eyebrow">Step 2 of 3</div>
        <div class="stage-title">Check Your Email</div>
        <div class="stage-desc">We sent a 6-digit code to <strong id="sentToEmail">—</strong>. Enter it below to continue.</div>

        <!-- Email badge -->
        <div class="email-badge">
          <div class="email-badge-icon">
            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" color="#E8341A">
              <rect x="1.5" y="3" width="12" height="9" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
              <path d="M1.5 5l6 4.5L13.5 5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
          </div>
          <div class="email-badge-text">
            <div class="email-badge-label">Code sent to</div>
            <div class="email-badge-addr" id="emailDisplay">—</div>
          </div>
          <a class="email-change" href="#" onclick="goStage(1);return false;">Change</a>
        </div>

        <!-- OTP inputs -->
        <div class="otp-row" id="otpRow">
          <input class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" type="text" autocomplete="one-time-code">
          <input class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" type="text">
          <input class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" type="text">
          <input class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" type="text">
          <input class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" type="text">
          <input class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" type="text">
        </div>
        <div class="otp-hint" id="otpHint">Enter all 6 digits to continue.</div>

        <!-- Resend row -->
        <div class="resend-row">
          <div style="display:flex;align-items:center;gap:10px;color:var(--muted2)">
            Expires in
            <div class="timer-ring">
              <svg width="36" height="36" viewBox="0 0 36 36">
                <circle cx="18" cy="18" r="14" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="2"/>
                <circle cx="18" cy="18" r="14" fill="none" stroke="#E8341A" stroke-width="2"
                  stroke-dasharray="87.96" id="timerCircle" stroke-dashoffset="0" stroke-linecap="round"/>
              </svg>
              <span class="timer-num" id="timerNum">5:00</span>
            </div>
          </div>
          <button class="resend-btn" id="resendBtn" disabled onclick="resendCode()">Resend Code</button>
        </div>

        <button class="btn-primary" id="verifyBtn" type="button" onclick="verifyOTP()">
          <span class="btn-text" style="display:flex;align-items:center;gap:9px">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" color="white" style="position:relative;z-index:1"><path d="M3 8l4 4 6-7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span style="position:relative;z-index:1">Verify Code</span>
          </span>
          <div class="spinner"></div>
        </button>
      </div>

      <!-- ── STAGE 3: New Password ── -->
      <div class="stage" id="stage3">
        <div class="stage-eyebrow">Step 3 of 3</div>
        <div class="stage-title">New Password</div>
        <div class="stage-desc">Create a strong new password for your REVV account.</div>

        <!-- New password -->
        <div class="field-group" id="grp-newpass">
          <label class="field-label">New Password</label>
          <div class="input-wrap">
            <svg class="input-icon" width="18" height="18" viewBox="0 0 18 18" fill="none" color="white">
              <rect x="3" y="8" width="12" height="8" rx="2" stroke="currentColor" stroke-width="1.4"/>
              <path d="M6 8V6a3 3 0 1 1 6 0v2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
              <circle cx="9" cy="12" r="1.3" fill="currentColor"/>
            </svg>
            <input class="field-input" id="newPass" type="password" placeholder="At least 8 characters" autocomplete="new-password">
            <button type="button" class="eye-toggle" id="eyeNew">
              <svg width="17" height="17" viewBox="0 0 17 17" fill="none" color="white"><path d="M1 8.5s3-5 7.5-5 7.5 5 7.5 5-3 5-7.5 5-7.5-5-7.5-5z" stroke="currentColor" stroke-width="1.4"/><circle cx="8.5" cy="8.5" r="2.3" stroke="currentColor" stroke-width="1.4"/></svg>
            </button>
          </div>
          <div class="strength-bars">
            <div class="sbar" id="sb1"></div>
            <div class="sbar" id="sb2"></div>
            <div class="sbar" id="sb3"></div>
            <div class="sbar" id="sb4"></div>
          </div>
          <div class="strength-label">
            <span style="color:var(--muted)">Password strength</span>
            <span id="strengthText" style="color:var(--muted)">—</span>
          </div>
          <div class="field-error">Password must be at least 8 characters.</div>
        </div>

        <!-- Requirements -->
        <div class="req-list">
          <div class="req-list-title">Requirements</div>
          <div class="req-item" id="req-len">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" id="ri-len"><circle cx="6.5" cy="6.5" r="5.5" stroke="currentColor" stroke-width="1.2" color="#6A6E75"/></svg>
            At least 8 characters
          </div>
          <div class="req-item" id="req-upper">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" id="ri-upper"><circle cx="6.5" cy="6.5" r="5.5" stroke="currentColor" stroke-width="1.2" color="#6A6E75"/></svg>
            One uppercase letter
          </div>
          <div class="req-item" id="req-num">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" id="ri-num"><circle cx="6.5" cy="6.5" r="5.5" stroke="currentColor" stroke-width="1.2" color="#6A6E75"/></svg>
            One number
          </div>
          <div class="req-item" id="req-special">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" id="ri-special"><circle cx="6.5" cy="6.5" r="5.5" stroke="currentColor" stroke-width="1.2" color="#6A6E75"/></svg>
            One special character
          </div>
        </div>

        <!-- Confirm password -->
        <div class="field-group" id="grp-confirm" style="margin-top:16px">
          <label class="field-label">Confirm New Password</label>
          <div class="input-wrap">
            <svg class="input-icon" width="18" height="18" viewBox="0 0 18 18" fill="none" color="white">
              <rect x="3" y="8" width="12" height="8" rx="2" stroke="currentColor" stroke-width="1.4"/>
              <path d="M6 8V6a3 3 0 1 1 6 0v2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
              <path d="M6.5 12l2 2 3-3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <input class="field-input" id="confirmPass" type="password" placeholder="Re-enter your password" autocomplete="new-password">
            <button type="button" class="eye-toggle" id="eyeConfirm">
              <svg width="17" height="17" viewBox="0 0 17 17" fill="none" color="white"><path d="M1 8.5s3-5 7.5-5 7.5 5 7.5 5-3 5-7.5 5-7.5-5-7.5-5z" stroke="currentColor" stroke-width="1.4"/><circle cx="8.5" cy="8.5" r="2.3" stroke="currentColor" stroke-width="1.4"/></svg>
            </button>
          </div>
          <div class="field-error">Passwords do not match.</div>
        </div>

        <button class="btn-primary" id="resetBtn" type="button" onclick="resetPassword()" style="margin-top:20px">
          <span class="btn-text" style="display:flex;align-items:center;gap:9px">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" color="white" style="position:relative;z-index:1"><path d="M2 8l12-6-5 12-2-5-5-1z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span style="position:relative;z-index:1">Reset Password</span>
          </span>
          <div class="spinner"></div>
        </button>
      </div>

      <!-- ── SUCCESS ── -->
      <div class="success-box" id="successBox">
        <div class="success-ring">
          <svg width="36" height="36" viewBox="0 0 36 36" fill="none">
            <path d="M8 18l7 7 13-14" stroke="#3DBE7A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="success-title">Password Reset!</div>
        <div class="success-desc">Your REVV password has been updated successfully.<br>You can now sign in with your new credentials.</div>
        <a class="btn-signin" href="carrental_login.html">
          <svg width="15" height="15" viewBox="0 0 15 15" fill="none" color="white" style="position:relative;z-index:1"><path d="M3 7.5h9M8.5 4l4 3.5-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          <span style="position:relative;z-index:1">Back to Sign In</span>
        </a>
      </div>

    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast">
  <svg width="15" height="15" viewBox="0 0 15 15" fill="none" id="toastIcon">
    <circle cx="7.5" cy="7.5" r="6" stroke="#E8341A" stroke-width="1.3"/>
    <path d="M7.5 5v3" stroke="#E8341A" stroke-width="1.5" stroke-linecap="round"/>
    <circle cx="7.5" cy="10" r="0.7" fill="#E8341A"/>
  </svg>
  <span id="toastMsg">Something went wrong.</span>
</div>

<script src="/rent/javascript/forgotpassword.js"></script>
</body>
</html>