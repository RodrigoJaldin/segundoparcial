<head>
    <!-- CSS only -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
</head>
<body style="background-color: #8a371c;">
    <section class="vh-100" >
        <div class="container py-5 h-100">
          <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-xl-10">
              <div class="card" style="border-radius: 1rem;">
                <div class="row g-0">
                  <div class="col-md-6 col-lg-5 d-none d-md-block">
                    <img src="{{asset('logincss/img/login2.jpg')}}"
                      alt="login form" class="img-fluid"  style="border-radius: 1rem 0 0 1rem;" />
                  </div>
                  <div class="col-md-6 col-lg-7 d-flex align-items-center">
                    <div class="card-body p-4 p-lg-5 text-black">

                      <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="d-flex align-items-center mb-3 pb-1">
                          <i class="fas fa-cubes fa-2x me-3" style="color: #ff6219;"></i>
                          <span class="h1 fw-bold mb-0">Diagrama C4</span>
                        </div>

                        <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Inicio de Sesion</h5>
                         <!--Email-->
                        <div class="form-outline mb-4">
                          <input type="email" id="email"   name="email" class="form-control form-control-lg" value="{{ old('email') }}"
                          required autocomplete="email" autofocus placeholder="Ejemplo@gmail.com"
                          />
                          @error('email')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                          <label class="form-label" for="form2Example17">Email</label>
                        </div>
                         <!--PAssword-->

                        <div class="form-outline mb-4">
                          <input type="password" name="password" id="password" class="form-control form-control-lg"
                           @error('password') is-invalid @enderror required
                          autocomplete="current-password"/>

                          @error('password')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror

                          <label class="form-label" for="form2Example27">{{ __('Password') }}</label>


                        </div>

                        <div class="pt-1 mb-4">
                          <button class="btn btn-dark btn-lg btn-block" type="submit" name="login">
                            {{ __('Iniciar Sesion') }}</button>
                        </div>

                        <a class="small text-muted" href="{{ route('password.request') }}">
                            {{ __('¿Olvido su contraseña?') }}</a>
                        <p class="mb-5 pb-lg-2" style="color: #393f81;">No tienes una cuenta? <a href="register"
                            style="color: #393f81;">Resgistrate aqui!</a></p>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
      </section>
</body>

