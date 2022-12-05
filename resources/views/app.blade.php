<!doctype html>
<html lang="en">

<head>
    <base href="../..">
    <title>MiDiagramador-c4</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    <div class="wrapper d-flex align-items-stretch">
        <nav id="sidebar">
            <div class="p-4 pt-5">
                <a href="{{ route('home') }}" class="img logo rounded-circle mb-5"
                    style="background-image: url(images/logo.jpg);"></a>

                <div class="user-p">
                    <h6> Bienvenid@ {{ auth()->user()->name }}</h6>
                    <br>
                    <br>
                </div>
                <ul class="list-unstyled components mb-5">

                    <li class="active">
                        <a href="#diagrama" data-toggle="collapse" aria-expanded="false"
                            class="dropdown-toggle">Diagramas</a>
                        <ul class="collapse list-unstyled" id="diagrama">
                            <li>
                                <a href="{{ route('home') }}">Mis diagramas</a>
                            </li>
                            <li>
                                <a href="{{ route('diagrama.diagramasCompartidos') }}">Diagramas compartidos</a>
                            </li>
                            <li>
                                <a href="{{ route('diagrama.create') }}">Crear nuevo diagrama</a>
                            </li>
                        </ul>
                    </li>

                    <li class="active">
                        <a href="#diagrama" data-toggle="collapse" aria-expanded="false"
                            class="dropdown-toggle">Compartir</a>
                        <ul class="collapse list-unstyled" id="diagrama">
                            <li>
                                <a href="{{ route('diagrama.create') }}">Crear nueva sala</a>
                            </li>
                            <li>
                                <a href="{{ route('diagrama.index') }}">Unirme a sala</a>
                            </li>

                            <li>
{{--                                 <a href="{{ route('diagramador') }}">EDITOR DE DIAGRAMA</a>
 --}}                            </li>
                        </ul>
                    </li>
                    <br>
                    <br>

                    <li>
                        <a class="nav-link" id="link" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i>
                            Cerrar Sesion<span class="sr-only">(current) </span></a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                </ul>


                <div class="footer">
                    <p>
                        <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                        Camila Reyes Duran &copy;
                        <script>
                            document.write(new Date().getFullYear());
                        </script> Red social <i class="icon-heart" aria-hidden="true"></i> by <a
                            href="https://m.facebook.com/100001640112928/" target="_blank">Fb</a>
                        <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                    </p>
                </div>

            </div>
        </nav>

        <!-- Page Content  -->
        <div id="content" class="p-4 p-md-5">

            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">

                    <button type="button" id="sidebarCollapse" class="btn btn-primary">
                        <i class="fa fa-bars"></i>
                        <span class="sr-only">Toggle Menu</span>
                    </button>
                    <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse"
                        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fa fa-bars"></i>
                    </button>


                </div>
            </nav>

            <section class="section-1" id="content">
                @yield('content')
                @include('sweetalert::alert')
            </section>

        </div>
    </div>

    <script src={{ asset('js/jquery.min.js') }}></script>
    <script src={{ asset('js/popper.js') }}></script>
    <script src={{ asset('js/bootstrap.min.js') }}></script>
    <script src={{ asset('js/main.js') }}></script>


    </body>


</html>
