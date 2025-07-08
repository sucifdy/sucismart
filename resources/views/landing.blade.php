<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Smart Room</title>
  <style>
    :root {
      --bg: #111827;
      --card: #1f2937;
      --input: #374151;
      --text: #f9fafb;
      --muted: #9ca3af;
      --primary: #3b82f6;
      --primary-hover: #2563eb;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: "Segoe UI", sans-serif;
    }

    body {
      background-color: var(--bg);
      color: var(--text);
      display: flex;
      flex-direction: column;
      height: 100vh;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }

    h1 {
      font-size: 2.4rem;
      margin-bottom: 0.4rem;
      font-weight: 600;
      color: var(--primary);
    }

    p {
      font-size: 1rem;
      color: var(--muted);
      margin-bottom: 2rem;
      text-align: center;
    }

    .login-card {
      background-color: var(--card);
      padding: 2rem;
      border-radius: 1rem;
      max-width: 360px;
      width: 100%;
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.4);
    }

    input {
      width: 100%;
      padding: 0.75rem;
      margin-bottom: 1rem;
      border: none;
      border-radius: 0.5rem;
      background-color: var(--input);
      color: var(--text);
      font-size: 1rem;
    }

    input::placeholder {
      color: var(--muted);
    }

    button {
      width: 100%;
      padding: 0.75rem;
      background-color: var(--primary);
      color: white;
      font-weight: 600;
      border: none;
      border-radius: 0.5rem;
      cursor: pointer;
      transition: background-color 0.2s ease;
    }

    button:hover {
      background-color: var(--primary-hover);
    }

    .error {
      color: #f87171;
      margin-bottom: 1rem;
      text-align: center;
      font-size: 0.9rem;
    }

    footer {
      margin-top: 2rem;
      font-size: 0.85rem;
      color: var(--muted);
      text-align: center;
    }

    @media (max-width: 480px) {
      h1 {
        font-size: 1.8rem;
      }

      .login-card {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <h1>Smart Room</h1>
  <p>Akses sistem otomatisasi rumah. Login hanya untuk penghuni.</p>

  <div class="login-card">
    @if(session('error'))
      <div class="error">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <input type="text" name="username" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Masuk</button>
    </form>
  </div>

  <footer>
    &copy; {{ date('Y') }} SmartRoom. Hak akses terbatas penghuni internal.
  </footer>
</body>
</html>
