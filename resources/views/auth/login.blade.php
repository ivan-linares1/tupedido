<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TuPedido - Login</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- iconos bootsrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">


    <!-- Vite CSS -->
    @vite(['resources/css/variables.css', 'resources/css/login.css'])
</head>

<body class="bg-gradient-custom d-flex align-items-center justify-content-center min-vh-100">

    <div class="card-login p-5 col-11 col-sm-8 col-md-6 col-lg-5 col-xl-4">
        <h2 class="text-center mb-4">Bienvenido a la plataforma de pedidos de Kombitec</h2>

        {{-- Mensajes de error --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <p class="mb-0"> Correo y/o Contraseña invalidas</p>
            </div>
        @endif
        
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope-fill"></i> {{--icono del carta email --}}
                    </span>
                    <input id="email" type="email" name="email" class="form-control input-custom" value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3 position-relative">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock-fill" ></i>{{--icono del candado password--}}
                    </span>
                    <input id="password" type="password" name="password" class="form-control input-custom" required>
                    <span class="input-group-text password-toggle" onclick="togglePassword()" style="cursor: pointer">
                        <i id="eye-icon" class="bi bi-eye-fill"></i> {{--icono del ojo abierto --}}
                    </span>
                </div>
            </div>

            <div class="form-check mb-3">
                <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
                <label for="remember_me" class="form-check-label">Recordarme</label>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary-custom btn-lg">Ingresar</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<script>
function togglePassword() {
    const password = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');

    if (password.type === 'password') {
        password.type = 'text';
        eyeIcon.classList.remove('bi-eye-fill'); //icono del ojo abierto
        eyeIcon.classList.add('bi-eye-slash-fill');//icono del ojo cerrado
    } else {
        password.type = 'password';
        eyeIcon.classList.remove('bi-eye-slash-fill');//icono del ojo cerrado
        eyeIcon.classList.add('bi-eye-fill');//icono del ojo abierto
    }
}
</script>