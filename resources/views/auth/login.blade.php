<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
            padding: 40px;
        }
        .brand-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 32px;
        }
        .brand-header img {
            height: 40px;
            width: auto;
        }
        .brand-header h1 {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 28px;
            font-weight: 600;
            color: #1a202c;
            margin: 0;
            letter-spacing: -0.02em;
        }
        .login-subheader {
            text-align: center;
            margin-bottom: 24px;
        }
        .login-subheader p {
            color: #718096;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2d3748;
            font-size: 14px;
            font-weight: 500;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #2563eb;
        }
        .form-error {
            color: #e53e3e;
            font-size: 13px;
            margin-top: 6px;
        }
        .remember-group {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
        }
        .remember-group input[type="checkbox"] {
            margin-right: 8px;
            width: 16px;
            height: 16px;
        }
        .remember-group label {
            font-size: 14px;
            color: #4a5568;
            margin: 0;
        }
        .login-button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            letter-spacing: -0.01em;
        }
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .login-button:active {
            transform: translateY(0);
        }
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-danger {
            background-color: #fed7d7;
            color: #c53030;
            border-left: 4px solid #e53e3e;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="brand-header">
            <img src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }} Logo">
            <h1>{{ config('app.name') }}</h1>
        </div>
        
        <div class="login-subheader">
            <p>Please enter your credentials to login</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Error!</strong>
                <ul style="margin-top: 8px; margin-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus
                    placeholder="you@example.com"
                >
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    placeholder="Enter your password"
                >
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="remember-group">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="login-button">
                Login
            </button>
        </form>
    </div>
</body>
</html>
