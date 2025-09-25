<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>403 - Acceso denegado</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://fonts.googleapis.com/css?family=Montserrat:700,900" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Montserrat', sans-serif;
      background: linear-gradient(135deg, #232526, #414345);
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      text-align: center;
    }

    .container { max-width: 500px; }

    h1 {
      font-size: 8rem;
      margin: 0;
      animation: shake 1s infinite;
      color: #ff4d4d;
    }

    h2 { font-size: 2rem; margin: 20px 0; }
    p { font-size: 1.2rem; margin-bottom: 20px; }

    a, button {
      text-decoration: none;
      background: #ff4d4d;
      color: white;
      padding: 12px 30px;
      border-radius: 25px;
      font-weight: bold;
      border: none;
      cursor: pointer;
      transition: 0.3s;
      margin: 5px;
      display: inline-block;
    }

    a:hover, button:hover { background: #cc0000; }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      20% { transform: translateX(-10px); }
      40% { transform: translateX(10px); }
      60% { transform: translateX(-10px); }
      80% { transform: translateX(10px); }
    }

    .lock {
      font-size: 5rem;
      margin-top: 20px;
      animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.2); }
      100% { transform: scale(1); }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>403</h1>
    <h2>Acceso denegado</h2>
    <p>No tienes permiso para ver esta página 🔒</p>
    <i class="fas fa-lock lock"></i>
    <br><br>
    <a href="{{ url('/') }}">Volver al inicio</a>

    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
  </div>
</body>
</html>