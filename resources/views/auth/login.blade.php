@php
    $companyName = \App\Models\Setting::get('company_name', 'TrendHub');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Welcome to {{ $companyName }} - Enterprise ERP Login</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #000000;
            --primary-dark: #18181b;
            --primary-light: #27272a;
            --accent: #ffffff;
            --dark-bg: #050505;
            --dark-surface: #0f0f11;
            --text-main: #09090b;
            --text-muted: #71717a;
            --border-color: #e4e4e7;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            min-height: 100vh;
            background-color: #050505;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient Glow Background Effects (Monochrome) */
        .ambient-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            z-index: 0;
            pointer-events: none;
            opacity: 0.18;
            animation: orbFloat 16s ease-in-out infinite alternate;
        }
        .orb-1 {
            top: -10%;
            left: -5%;
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, #ffffff 0%, rgba(255, 255, 255, 0) 70%);
        }
        .orb-2 {
            bottom: -15%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, #a1a1aa 0%, rgba(161, 161, 170, 0) 70%);
            animation-duration: 20s;
        }
        .orb-3 {
            top: 40%;
            left: 45%;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, #71717a 0%, rgba(113, 113, 122, 0) 70%);
            opacity: 0.12;
            animation-duration: 22s;
        }

        @keyframes orbFloat {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -40px) scale(1.08); }
            100% { transform: translate(-30px, 30px) scale(0.95); }
        }

        /* Main Container */
        .login-master-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1140px;
            min-height: 640px;
            margin: 24px;
            background: rgba(15, 15, 17, 0.85);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            box-shadow: 0 30px 70px -15px rgba(0, 0, 0, 0.9), 0 0 0 1px rgba(255, 255, 255, 0.06);
            display: grid;
            grid-template-columns: 1.15fr 1fr;
            overflow: hidden;
        }

        /* Left Hero Panel */
        .hero-panel {
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: linear-gradient(135deg, rgba(20, 20, 23, 0.9) 0%, rgba(9, 9, 11, 0.95) 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            position: relative;
        }

        /* Brand Header */
        .brand-header {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-logo-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: #000000;
            border: 1.5px solid rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 20px;
            box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.7);
        }

        .brand-text h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
            line-height: 1.1;
        }

        .brand-text span {
            font-size: 11px;
            font-weight: 600;
            color: #a1a1aa;
            letter-spacing: 1.8px;
            text-transform: uppercase;
        }

        /* Hero Content */
        .hero-content {
            margin: 36px 0;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 50px;
            color: #f4f4f5;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .hero-badge .pulse-dot {
            width: 7px;
            height: 7px;
            background-color: #ffffff;
            border-radius: 50%;
            box-shadow: 0 0 10px #ffffff;
            animation: pulseGlow 2s infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0.6; }
        }

        .hero-title {
            font-family: 'Outfit', sans-serif;
            font-size: 36px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
            letter-spacing: -0.8px;
            margin-bottom: 14px;
        }

        .hero-title span {
            background: linear-gradient(135deg, #ffffff 0%, #e4e4e7 40%, #a1a1aa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            color: #a1a1aa;
            font-size: 14.5px;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        /* Feature List */
        .feature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 12px;
            padding: 12px;
            transition: all 0.2s;
        }
        .feature-item:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-1px);
        }

        .feature-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .feature-text h4 {
            font-size: 12.5px;
            font-weight: 700;
            color: #f4f4f5;
            margin-bottom: 2px;
        }

        .feature-text p {
            font-size: 11px;
            color: #71717a;
            line-height: 1.3;
        }

        /* Hero Footer */
        .hero-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 12px;
            color: #71717a;
        }

        .hero-status {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #e4e4e7;
            font-weight: 600;
        }

        /* Right Form Panel (Clean White / Light Crisp Contrast) */
        .form-panel {
            background: #ffffff;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .form-header {
            margin-bottom: 28px;
        }

        .form-header h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: #09090b;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .form-header p {
            color: #71717a;
            font-size: 14px;
        }

        /* Alerts */
        .auth-alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .auth-alert-danger {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }
        .auth-alert-success {
            background-color: #f4f4f5;
            border: 1px solid #e4e4e7;
            color: #18181b;
        }

        /* Form Group & Floating Inputs */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 700;
            color: #18181b;
            margin-bottom: 7px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: #71717a;
            font-size: 15px;
            transition: color 0.2s;
            pointer-events: none;
        }

        .form-control-custom {
            width: 100%;
            height: 48px;
            padding: 0 42px 0 42px;
            background-color: #fafafa;
            border: 1.5px solid #d4d4d8;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            color: #09090b;
            outline: none;
            transition: all 0.2s ease-in-out;
        }

        .form-control-custom:focus {
            background-color: #ffffff;
            border-color: #000000;
            box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.08);
        }

        .form-control-custom:focus + .input-icon,
        .input-box:focus-within .input-icon {
            color: #000000;
        }

        .password-toggle-btn {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: #71717a;
            font-size: 15px;
            cursor: pointer;
            padding: 4px;
            transition: color 0.2s;
        }
        .password-toggle-btn:hover {
            color: #09090b;
        }

        .input-error-msg {
            color: #dc2626;
            font-size: 12px;
            font-weight: 600;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Remember & Forgot Row */
        .auth-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            font-size: 13px;
        }

        .remember-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: #3f3f46;
            font-weight: 500;
            user-select: none;
        }

        .remember-checkbox input {
            width: 16px;
            height: 16px;
            accent-color: #000000;
            cursor: pointer;
        }

        .forgot-link {
            color: #09090b;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        .forgot-link:hover {
            color: #52525b;
            text-decoration: underline;
        }

        /* Submit Button (Black & White Theme) */
        .btn-auth-submit {
            width: 100%;
            height: 50px;
            background: #000000;
            border: 1px solid #000000;
            border-radius: 12px;
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            font-size: 15.5px;
            font-weight: 700;
            letter-spacing: 0.3px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
            transition: all 0.25s ease;
        }

        .btn-auth-submit:hover {
            background: #18181b;
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.55);
        }

        .btn-auth-submit:active {
            transform: translateY(0);
        }

        /* Form Footer */
        .form-footer {
            margin-top: 28px;
            text-align: center;
            font-size: 12px;
            color: #71717a;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        /* Mobile Breakpoints */
        @media (max-width: 991px) {
            .login-master-wrapper {
                grid-template-columns: 1fr;
                max-width: 520px;
                min-height: auto;
            }
            .hero-panel {
                padding: 32px 28px 24px;
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            }
            .hero-content {
                margin: 18px 0;
            }
            .hero-title {
                font-size: 24px;
            }
            .hero-desc,
            .feature-grid,
            .hero-footer {
                display: none;
            }
            .form-panel {
                padding: 36px 28px;
            }
        }

        @media (max-width: 480px) {
            .login-master-wrapper {
                margin: 12px;
                border-radius: 18px;
            }
            .hero-panel {
                padding: 24px 20px 18px;
            }
            .form-panel {
                padding: 28px 20px;
            }
            .form-header h2 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

    <!-- Ambient Glowing Orbs -->
    <div class="ambient-orb orb-1"></div>
    <div class="ambient-orb orb-2"></div>
    <div class="ambient-orb orb-3"></div>

    <div class="login-master-wrapper">
        
        <!-- Left Hero Showcase Panel -->
        <div class="hero-panel">
            <div class="brand-header">
                <div class="brand-logo-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="brand-text">
                    <h1>{{ $companyName }}</h1>
                    <span>Management System</span>
                </div>
            </div>

            <div class="hero-content">
                <div class="hero-badge">
                    <span class="pulse-dot"></span>
                    <span>Enterprise ERP Suite</span>
                </div>
                <h2 class="hero-title">
                    Welcome to <br>
                    <span>{{ $companyName }}</span>
                </h2>
                <p class="hero-desc">
                    Next-generation business management platform powering smart POS, automated multi-warehouse inventory, and instant financial ledgers.
                </p>

                <div class="feature-grid">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Fast POS & Sales</h4>
                            <p>Real-time billing & receipts</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-boxes-stacked"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Smart Inventory</h4>
                            <p>Multi-variant tracking</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Double Ledgers</h4>
                            <p>Automated journal balance</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Role Security</h4>
                            <p>Granular access controls</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hero-footer">
                <div class="hero-status">
                    <i class="fas fa-circle" style="font-size: 8px;"></i>
                    <span>Cloud Servers Operational</span>
                </div>
                <span>{{ $companyName }} OS &bull; {{ date('Y') }}</span>
            </div>
        </div>

        <!-- Right Login Form Panel -->
        <div class="form-panel">
            <div class="form-header">
                <h2>Sign in to Workspace</h2>
                <p>Enter your authorized credentials to access your {{ $companyName }} workspace.</p>
            </div>

            <!-- Session Status Alert -->
            @if (session('status'))
                <div class="auth-alert auth-alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Error Message Alert -->
            @if (session('error'))
                <div class="auth-alert auth-alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('software.login') }}" autocomplete="on">
                @csrf

                <!-- Email / Username Input -->
                <div class="form-group">
                    <label for="email" class="form-label">Email or Username</label>
                    <div class="input-box">
                        <input id="email" 
                               type="email" 
                               name="email" 
                               class="form-control-custom" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               autocomplete="username" 
                               placeholder="admin@example.com">
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                    @error('email')
                        <div class="input-error-msg">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-box">
                        <input id="password" 
                               type="password" 
                               name="password" 
                               class="form-control-custom" 
                               required 
                               autocomplete="current-password" 
                               placeholder="••••••••••••">
                        <i class="fas fa-lock input-icon"></i>
                        <button type="button" class="password-toggle-btn" id="togglePasswordBtn" title="Toggle password visibility">
                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="input-error-msg">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="auth-options">
                    <label for="remember_me" class="remember-checkbox">
                        <input id="remember_me" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember my session</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-auth-submit" id="btnSubmitLogin">
                    <span>Sign in to Workspace</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <div class="form-footer">
                <i class="fas fa-lock" style="font-size: 10px;"></i>
                <span>256-Bit SSL Encrypted Connection &bull; {{ $companyName }} &copy; {{ date('Y') }}</span>
            </div>
        </div>

    </div>

    <!-- Interactive Scripts -->
    <script>
        // Password Visibility Toggle
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        if (toggleBtn && passwordInput && toggleIcon) {
            toggleBtn.addEventListener('click', function() {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                toggleIcon.classList.toggle('fa-eye', !isPassword);
                toggleIcon.classList.toggle('fa-eye-slash', isPassword);
            });
        }
    </script>
</body>
</html>
