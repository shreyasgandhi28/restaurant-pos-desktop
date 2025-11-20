<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Restaurant POS</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #a5b4fc;
            --accent: #fbbf24;
            --text: #0f172a;
            --text-secondary: #475569;
            --text-light: #94a3b8;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --border-focus: #c7d2fe;
            --gradient-page: radial-gradient(circle at top, #eef2ff 0%, #e0e7ff 35%, #f8fafc 100%);
            --gradient-primary: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--gradient-page);
            color: var(--text);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            margin: 0;
            min-height: 100vh;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .login-container {
            display: flex;
            width: 100%;
            flex: 1;
            min-height: 100vh;
            overflow: hidden;
        }

        /* Left Side - Marketing Panel */
        .hero-panel {
            flex: 1;
            position: relative;
            background: url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            backdrop-filter: blur(2px);
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(15, 23, 42, 0.55) 100%);
        }

        .hero-card {
            position: relative;
            z-index: 2;
            width: min(90%, 520px);
            padding: 3rem;
            background: rgba(15, 23, 42, 0.85);
            border-radius: 36px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.06);
            color: #fff;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-title {
            font-family: 'Poppins', sans-serif;
            font-size: 3rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 1rem;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .welcome-subtitle {
            font-size: 1.05rem;
            margin-bottom: 2rem;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.92);
        }

        .feature-list {
            list-style: none;
            margin-top: 1.5rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.25rem;
            font-size: 1.05rem;
            opacity: 0;
            animation: fadeInLeft 0.6s ease-out forwards;
            color: rgba(255, 255, 255, 0.95);
        }

        .feature-item:nth-child(1) { animation-delay: 0.2s; }
        .feature-item:nth-child(2) { animation-delay: 0.4s; }
        .feature-item:nth-child(3) { animation-delay: 0.6s; }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .feature-icon {
            margin-right: 1rem;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(251, 191, 36, 0.12);
            border-radius: 14px;
            border: 1px solid rgba(251, 191, 36, 0.35);
            box-shadow: inset 0 0 12px rgba(251, 191, 36, 0.15);
        }

        .feature-icon i {
            color: #fbbf24;
            font-size: 1rem;
        }

        /* Right Side - Login Form */
        .login-form-container {
            flex: 0 0 38%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: clamp(3.5rem, 6vh, 5rem) clamp(3rem, 4vw, 4.5rem);
            background: var(--card-bg);
            position: relative;
        }

        .login-form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border-color), transparent);
        }

        .login-card {
            width: 100%;
            max-width: 340px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .logo-container {
            width: 100%;
            display: flex;
            justify-content: center;
            margin: 0 0 1.5rem;
        }

        .logo {
            max-width: 255px;
            height: auto;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: block;
            object-fit: contain;
            mix-blend-mode: multiply;
            filter: contrast(1.2) saturate(1.2);
        }

        .logo:hover {
            transform: translateY(-2px);
        }

        .form-header {
            text-align: center;
            margin-bottom: 1.65rem;
        }

        .form-title {
            font-size: 1.85rem;
            font-weight: 700;
            color: var(--text);
            margin: 0 0 0.4rem;
            letter-spacing: -0.02em;
        }

        .form-subtitle {
            color: var(--text-light);
            margin: 0;
            font-size: 0.9rem;
            font-weight: 400;
            letter-spacing: 0.01em;
        }

        .form-group {
            margin-bottom: 1.25rem;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 0.82rem;
            color: var(--text-secondary);
            margin-bottom: 0.45rem;
            font-weight: 600;
            letter-spacing: 0.03em;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 1rem;
            pointer-events: none;
            transition: color 0.2s;
            z-index: 1;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 0.95rem 0.8rem 2.5rem;
            font-size: 0.9rem;
            border: 2px solid var(--border-color);
            border-radius: 0.7rem;
            background-color: var(--input-bg);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 46px;
            box-sizing: border-box;
            color: var(--text);
            font-weight: 400;
        }

        .form-control::placeholder {
            color: var(--text-light);
            font-weight: 400;
        }

        .form-control:hover {
            border-color: var(--border-focus);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            background-color: #ffffff;
        }

        .input-wrapper:focus-within .input-icon {
            color: var(--primary);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0.25rem 0 1.35rem;
            font-size: 0.85rem;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            cursor: pointer;
            user-select: none;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            border-radius: 0.375rem;
            border: 2px solid var(--border-color);
            background-color: white;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            flex-shrink: 0;
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .form-check-input:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .form-check span {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .forgot-password {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s;
            position: relative;
        }

        .forgot-password::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s;
        }

        .forgot-password:hover {
            color: var(--primary-dark);
        }

        .forgot-password:hover::after {
            width: 100%;
        }

        .btn-login {
            width: 100%;
            padding: 0.8rem 1.5rem;
            background: var(--gradient-primary);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 0 0 1.25rem;
            height: 48px;
            letter-spacing: 0.02em;
            box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.39);
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px 0 rgba(99, 102, 241, 0.5);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer {
            margin-top: 1rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--border-color);
            font-size: 0.78rem;
            color: var(--text-light);
            text-align: center;
            line-height: 1.5;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .login-footer a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .login-footer a:hover {
            color: var(--primary-dark);
        }

        /* Error Messages */
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .error-message::before {
            content: '⚠';
            font-size: 1rem;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .login-form-container {
                padding: 3rem;
            }
            .hero-card {
                padding: 2.75rem;
            }
        }

        @media (max-width: 992px) {
            body {
                height: auto;
                min-height: 100vh;
                overflow: auto;
            }

            .login-container {
                flex-direction: column;
                height: auto;
            }

            .login-form-container,
            .hero-panel {
                flex: unset;
                width: 100%;
            }

            .hero-panel {
                min-height: 320px;
            }

            .login-form-container {
                padding: 3rem 2.5rem;
            }

            .login-card {
                max-width: 420px;
            }

            .hero-card {
                width: min(92%, 520px);
                margin: 3rem 0;
            }

            .welcome-title {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 576px) {
            .login-form-container {
                padding: 2.25rem 1.5rem 2.75rem;
            }

            .login-card {
                max-width: 100%;
            }

            .form-title {
                font-size: 1.75rem;
            }

            .hero-card {
                padding: 2rem;
                margin: 2rem 0;
            }

            .welcome-title {
                font-size: 2rem;
            }

            .welcome-subtitle {
                font-size: 0.95rem;
            }
        }

        /* Scrollbar Styling */
        .login-form-container::-webkit-scrollbar {
            width: 6px;
        }

        .login-form-container::-webkit-scrollbar-track {
            background: transparent;
        }

        .login-form-container::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 3px;
        }

        .login-form-container::-webkit-scrollbar-thumb:hover {
            background: var(--text-light);
        }

    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Login Form -->
        <div class="login-form-container">
            <div class="login-card">
                <div class="logo-container">
                    <img src="{{ asset('images/logo/logo.png') }}" alt="Restaurant POS Logo" class="logo">
                </div>
                
                <div class="form-header">
                    <h2 class="form-title">Welcome Back</h2>
                    <p class="form-subtitle">Please sign in to continue to your account</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Input -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-wrapper">
                            <span class="input-icon"><i class="fas fa-envelope"></i></span>
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                class="form-control" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus 
                                placeholder="Enter your email"
                            >
                        </div>
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="form-group">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <label for="password" class="form-label">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="forgot-password">
                                    Forgot password?
                                </a>
                            @endif
                        </div>
                        <div class="input-wrapper">
                            <span class="input-icon"><i class="fas fa-lock"></i></span>
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                class="form-control" 
                                required 
                                placeholder="Enter your password"
                            >
                        </div>
                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember Me & Submit -->
                    <div class="form-options">
                        <label class="form-check">
                            <input 
                                type="checkbox" 
                                id="remember_me" 
                                name="remember" 
                                class="form-check-input"
                                {{ old('remember') ? 'checked' : '' }}
                            >
                            <span>Remember me</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-login">
                        <span>Sign In</span>
                    </button>
                </form>

                <!-- Footer -->
                <div class="login-footer">
                    <span>Powered by <a href="https://userssoftware.com/" target="_blank" class="powered-by-link">Users Software Systems</a></span>
                    <span>&copy; {{ date('Y') }} Users Software Systems. All rights reserved.</span>
                </div>
            </div>
        </div>

        <!-- Right Side - Marketing Panel -->
        <div class="hero-panel">
            <div class="hero-card">
                <h1 class="welcome-title">Welcome to Restaurant POS</h1>
                <p class="welcome-subtitle">Streamline your restaurant operations with our powerful point of sale platform. Delight guests, empower staff, and stay in control with live insights.</p>
                
                <ul class="feature-list">
                    <li class="feature-item">
                        <span class="feature-icon"><i class="fas fa-check"></i></span>
                        <span>Intuitive experience designed for fast teams</span>
                    </li>
                    <li class="feature-item">
                        <span class="feature-icon"><i class="fas fa-check"></i></span>
                        <span>Real-time order tracking and smart alerts</span>
                    </li>
                    <li class="feature-item">
                        <span class="feature-icon"><i class="fas fa-check"></i></span>
                        <span>Deep reporting & performance dashboards</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
