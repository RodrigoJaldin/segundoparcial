<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    {{--  stylescss --}}
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

    @livewireStyles

</head>

<body class="body2">
    <nav class="navbar navbar-expand-lg" style="background: rgb(205, 206, 206) ">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('logincss/img/c4logo.png')}}" width="50px" height="50px">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav m-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active ml-auto" aria-current="page" href="/home">Diagramador C4</a>
                    </li>
                </ul>

                <div class="btn-group ">
                    <button class="btn btn-secondary dropdown-toggle btn-dark" type="button" id="dropdownMenu2"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        {{ auth()->user()->name }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu2">

                        <li><button class="dropdown-item" type="button" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Cerrar Sesion</button>
                            <i class="fas fa-sign-out-alt"></i>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </nav>

    <section class="section-1" id="content">
        @yield('content')
    </section>

    @livewireScripts

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"
     integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg=="
     crossorigin="anonymous" referrerpolicy="no-referrer"></script>

</body>

</html>
