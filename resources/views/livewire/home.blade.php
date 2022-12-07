<div>

    <!--  Crear y unirse-->

    <div class="d-grid gap-2 col-5 mx-auto pt-3">
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#formulario">Crear
            Diagrama</button>
        <button class="btn btn-warning" type="button" data-bs-toggle="modal" wire:click="modalUnirse()">Unirse a
            Sala</button>
    </div>
    <!-- Modal Para Crear -->

    <div class="modal fade" id="formulario" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Crear Diagrama</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="limpiar()"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">Nombre:</label>
                        <input type="text" wire:model.defer="name"class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">Descripcion: </label>
                        <input type="text" wire:model.defer="descripcion"class="form-control">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                        wire:click="limpiar()">Cancelar</button>
                    <button type="button" class="btn btn-primary" wire:click="save()"
                        data-bs-dismiss="modal">Crear</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Para Unirse -->

    @if ($modalUnirse)
    <div class="modalunirse" >
        <div class="modalcompartir-contenido">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Unirse a Sala</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">LINK:</label>
                            <input name="link" type="text" wire:model.def="link" class="form-control"
                                id="link">
                        </div>
                    </div>
                    <div class="modal-footer m-2" >
                        <button type="button" class="btn btn-danger m-2" wire:click="cancelar()">Cancelar</button>
                        <button type="button" class="btn btn-primary m-2" wire:click="join()">Unirse</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @endif




{{-- /******************************************************************************************/ --}}
    <div class="container justify-content-center text-center pt-3 mb-2">
        <h1>Diagramas</h1>

    </div>


    <div class="container  pt-3">
        <button class="btn btn btn-primary" wire:click="optionMyDocument()">Mis Diagramas</button>
        <button class="btn btn btn-success" wire:click="CompartidosDocument()">Diagramas Compartidos</button>

    </div>

    <!--tabla de diagramas-->
    {{-- tabla de mis diagramas --}}
    @if ($op)
        <div class="container  mt-2 justify-content-center">
            <table class="table table-dark table-hover table-bordered border-dark mt-5 justify-content-center">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Descripcion</th>
                        <th scope="col">Creado por:</th>
                        <th scope="col">Link</th>
                        <th scope="col">Opciones</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($documents as $documents)
                        @if ($documents->user_id == auth()->user()->id)
                            <tr class="table-light">
                                <td>{{ $documents->id }}</td>
                                <td>{{ $documents->name }}</td>
                                <td>{{ $documents->descripcion ?? 'Sin descripcion' }}</td>
                                <td>{{ $documents->user_document->name }}</td>
                                <td>{{ $documents->link }}</td>

                                <!--opciones -->
                                <td>
                                    <div class="justify-content-center">
                                        <a href="{{ route('diagramador', $documents->id) }}"
                                            class="btn btn-sm btn-primary">Ingresar</a>
                                            <a href="#"
                                                class="btn btn-sm btn-primary"
                                                 wire:click="editar1('{{ $documents->id }}')">Editar</a>
                                        <a href="#" data-bs-toggle="modal" class="btn btn-sm btn-success"
                                            type="button" wire:click="compartirlink2('{{ $documents->link }}')">
                                            Compartir
                                        </a>
                                        <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            wire:click="modalEliminar1({{ $documents->id }})">Borrar</a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach


                </tbody>
            </table>
        </div>
    @endif
    {{-- tabla de diagramas  compartidos --}}
    @if (!$op)
        <div class="container  mt-2 justify-content-center">
            <table class="table table-dark table-hover table-bordered border-dark mt-5 justify-content-center">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Descripcion</th>
                        <th scope="col">Creado por:</th>
                        <th scope="col">Link</th>
                        <th scope="col">Opciones</th>

                    </tr>
                </thead>
                <tbody>

                    @foreach ($documentsShare as $compartidos)
                        @if ($compartidos->user_id == auth()->user()->id)
                        @if ($compartidos->document_id==$compartidos->documents->id &&  auth()->user()->id!=$compartidos->documents->user_id)


                            <tr class="table-light">
                                <td>{{ $compartidos->documents->id }}</td>
                                <td>{{ $compartidos->documents->name }}</td>
                                <td>{{ $compartidos->documents->descripcion ?? 'Sin descripcion' }}</td>
                                <td>{{ $compartidos->documents->user_document->name }}</td>
                                <td>{{ $compartidos->documents->link }}</td>

                                <!--opciones -->
                                <td>


                                    <div class="justify-content-center">
                                        <a href="{{ route('diagramador', $compartidos->documents->id) }}"
                                            class="btn btn-sm btn-primary">Ingresar</a>
                                        <a href="#" data-bs-toggle="modal" class="btn btn-sm btn-success"
                                            type="button"
                                            wire:click="compartirlink2('{{ $compartidos->documents->link }}')">
                                            Compartir
                                        </a>
                                        <a href="#" class="btn btn-sm btn-danger"
                                        wire:click="modalEliminarCompartido({{auth()->user()->id}},{{$compartidos->documents->id }})"
                                           data-bs-toggle="modal">Borrar</a>

                                    </div>
                                </td>
                            </tr>
                            @endif
                        @endif
                    @endforeach


                </tbody>
            </table>
        </div>
    @endif


    <!-- Modal Para obtener el link de compartir -->
    @if ($modalCompartir)
        <div class="modalcompartir">
            <div class="modal-dialog">
                <div class="modalcompartir-contenido">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-2" id="exampleModalLabel">Comparte este Link</h1>
                            <button type="button" wire:click="cancelar()" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body m-2">
                            <form>
                                <div class="mb-3">
                                    <label for="recipient-name" class="col-form-label fs-3">LINK:</label>
                                    <!--GENERAR CODIGO Y PONERLO AQUIIIIIII-->
                                    <p for="recipient-name" class="col-form-label fs-3" wire:model="link">
                                        {{ $link }}
                                    </p>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" wire:click="cancelar()" class="btn btn-danger mx-2"
                                data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif


    <!-- Modal Para Eliminar el Diagrama -->
    @if ($modaleliminar)
        <div class="modaleliminar">
            <div class="modaleliminar-contenido">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h1 class="modal-title fs-2">Eliminar Proyecto</h1>

                        </div>
                        <br>
                        <div class="modal-body m-2">
                            <div class="mb-3">
                                <h4 class="fs-3">¿Estás seguro?</h4>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success m-2 "
                                wire:click="cancelar()">Cancelar</button>
                            <button wire:click="eliminar()" class="btn btn-danger m-2">Eliminar</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($modalEliminarCompartido)
    <div class="modaleliminar">
        <div class="modaleliminar-contenido">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h1 class="modal-title fs-2">Eliminar Proyecto</h1>

                    </div>
                    <br>
                    <div class="modal-body m-2">
                        <div class="mb-3">
                            <h4 class="fs-3">¿Estás seguro?</h4>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success m-2 "
                            wire:click="cancelar()">Cancelar</button>
                        <button wire:click="eliminarCompartido()" class="btn btn-danger m-2">Eliminar</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endif





    <!-- Modal de Mensaje -->
    @if ($modalMensaje)
        <div class="modalcompartir">
            <div class="modal-dialog">
                <div class="modalcompartir-contenido">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Hay un Problema</h1>
                            <button type="button" wire:click="cancelar()" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body m-2">
                            <form>
                                <div class="mb-3">
                                    <label for="recipient-name" class="col-form-label">Usted no tiene ningun Diagrama
                                        Compartido</label>
                                    <!--GENERAR CODIGO Y PONERLO AQUIIIIIII-->
                                    <label for="recipient-name" class="col-form-label">Por favor Intente de
                                        nuevo</label>

                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" wire:click="cancelar()" class="btn btn-danger mx-2"
                                data-bs-dismiss="modal">Cancelar</button>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif



    <!-- Modal Para Editar-->
    @if ($modalEditar)
    <div class="modalcompartir">
        <div class="modal-dialog">
            <div class="modalcompartir-contenido">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Editar</h1>
                        <button type="button" wire:click="cancelar()" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body m-2">
                        <form>
                            <div class="mb-3">
                                <label for="recipient-name" class="col-form-label">Nombre:</label>
                                <input type="text" wire:model.defer="name"class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="recipient-name" class="col-form-label">Descripcion: </label>
                                <input type="text" wire:model.defer="descripcion"class="form-control">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="cancelar()" class="btn btn-danger mx-2"
                            data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" wire:click="update()"
                        data-bs-dismiss="modal">Guardar</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endif

</div>
