<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\Document;
use App\Models\UserDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Home extends Component
{

    public $name;
    public $descripcion;
    public $document_id;
    public $link;
    public $diagramajson = null;
    public $users;



    //boton de seleccion
    public $op = true;

    //modals
    public $modaleliminar = false;
    public $modalCompartir = false;
    public $modalMensaje = false;
    public $modalUnirse = false;
    public $modalEditar = false;
    public $modalEliminarCompartido = false;





    protected $listeners = ['render'];



    public function limpiar()
    {
        $this->name = null;
        $this->users = null;
        $this->descripcion = null;
        $this->document_id = null;
        $this->diagramajson = null;
        $this->link = null;
        //modals
        $this->modaleliminar = false;
        $this->modalEliminarCompartido = false;

        $this->modalCompartir = false;
        $this->modalMensaje = false;
        $this->modalUnirse = false;
        $this->modalEditar = false;

        //boton
        $this->op = true;
    }

    public function render()
    {
        $documents = Document::all();
        $documentsShare = UserDocument::all();

        return view('livewire.home', compact('documents', 'documentsShare'));
    }


    public function save()
    {
        $logintudPass = 15;
        $link = substr(md5(microtime()), 1, $logintudPass);
        $document = Document::create([
            'name' => $this->name,
            'descripcion' => $this->descripcion,
            'diagramajson' => $this->diagramajson,
            'link' => $link,
            'user_id' => Auth()->user()->id,
        ]);

        $doc_id = $document->id;
        $part_id = Auth()->user()->id;
        UserDocument::create([
            'user_id' => $part_id,
            'document_id' => $doc_id,
        ]);
        $this->limpiar();
        $this->emitTo('Home', 'render');
    }




    public function modalMensaje()
    {
        $this->modalMensaje = true;
    }
    public function optionMyDocument()
    {
        $this->op = true;
    }
    public function CompartidosDocument()
    {
        $documentsShare = UserDocument::all();

        if ($documentsShare == null) {
            $this->modalMensaje();
        } else {
            $this->op = false;
        }
    }

    //eliminar
    public function modalEliminar1($id)
    {
        $this->modaleliminar = true;
        $this->document_id = $id;
    }

    public function modalEliminarCompartido($users_id,$id_doc)
    {
        $this->modaleliminar = true;
        $this->document_id = $id_doc;
        $this->users = $users_id;

    }

    public function eliminar()
    {
        $document = Document::find($this->document_id);
        $document->delete();
        $this->modaleliminar = false;
        $this->limpiar();
    }
    public function eliminarCompartido()
    {
        $document = UserDocument::find($this->users,$this->document_id);
        $document->delete();
        $this->modalEliminarCompartido = false;
        $this->limpiar();
    }






    /********************************************************************** */

    public function obtenerlink($id)
    {
        $link1 = DB::table('document')
            ->select('link')
            ->where('id', $id)
            ->get();
        $this->link = $link1;
    }
    public function compartirlink2($link)
    {
        $this->modalCompartir = true;
        $this->link = $link;
    }


    public function modalCompartir()
    {
        $this->modalCompartir = true;
    }




    /******************************************************************************************** */
    public function cancelar()
    {
        $this->limpiar();
    }

    //guardar,eliminar,cargar diagrama
    public function saveD(Request $request)
    {

        $document = Document::where('id', $request->id)->get()->first();
        if ($document == null) {
            return response()->json(['ERROR' => "CODIGO NO ENCONTRADO"], Response::HTTP_BAD_REQUEST);
        }
        $document->update([
            'diagramajson' => $request->diagramajson
        ]);
        return response()->json($document, Response::HTTP_OK);
    }



    public function loadD(Request $request)
    {
        $document = Document::where('id', $request->header('id'))->get()->first();

        if ($document == null) {
            return response()->json(['ERROR' => "CODIGO NO ENCONTRADO"], Response::HTTP_BAD_REQUEST);
        }
        return response()->json($document->diagramajson, Response::HTTP_OK);
    }



    public function eliminarcontenido(Request $request)
    {
        $document = Document::where('id', $request->id)->get()->first();
        if ($document == null) {
            return response()->json(['ERROR' => "CODIGO NO ENCONTRADO"], Response::HTTP_BAD_REQUEST);
        }
        $document->update([
            'diagramajson' => $request->diagramajson
        ]);
        return response()->json($document, Response::HTTP_OK);
    }

    /****************************************************************************** */


    // unirse

    public function modalUnirse()
    {
        $this->modalUnirse = true;
    }

    public function join()
    {
        $linkdoc = $this->link;

        $newdoc = Document::where('link', $linkdoc)->get()->first();
        if ($newdoc == null) {
            $this->modalMensaje();
            return;
        }
        $tupla = UserDocument::where('user_id', auth()->user()->id)->where('document_id', $newdoc->id)->get()->first();

        if ($newdoc && $tupla == null) {
            UserDocument::create([
                'user_id' => auth()->user()->id,
                'document_id' => $newdoc->id
            ]);
        }
        $this->limpiar();
    }
    //editar
    public function editar1($id)
    {
        $this->modalEditar = true;
        $this->document_id = $id;
    }

    public function update()
    {
        $documets = Document::find($this->document_id);
        $documets->update([
            'name' => $this->name,
            'descripcion' => $this->descripcion,

        ]);
        $this->limpiar();
    }


}
