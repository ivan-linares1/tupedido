<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Error 404 - Página no encontrada</title>

  <!-- Bootstrap y fuente -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Arvo">

  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Arvo', serif;
      background: #fff;
    }

    .page_404 { 
      padding: 40px 0; 
    }

    .four_zero_four_bg {
      background-image: url(https://cdn.dribbble.com/users/285475/screenshots/2083086/dribbble_1.gif);
      height: 400px;
      background-position: center;
      background-repeat: no-repeat;
    }

    .four_zero_four_bg h1 {
      font-size: 80px;
    }

    .contant_box_404 { 
      margin-top: -50px; 
    }

    .link_404 {			 
      color: #fff !important;
      padding: 10px 20px;
      background: #39ac31;
      margin: 20px 0;
      display: inline-block;
      border-radius: 5px;
      text-decoration: none;
    }

    .link_404:hover {
      background: #2d8f26;
    }
  </style>
</head>

<body>
  <section class="page_404">
    <div class="container">
      <div class="row">	
        <div class="col-sm-12 text-center">
          <div class="four_zero_four_bg">
            <h1 class="text-center">404</h1>
          </div>
          <div class="contant_box_404">
            <h3 class="h2">Parece que estás perdido</h3>
            <p>La página que buscas no está disponible.</p>
            <a href="{{ url('/') }}" class="link_404">Volver al inicio</a>
          </div>
        </div>
      </div>
    </div>
  </section>
</body>
</html>
