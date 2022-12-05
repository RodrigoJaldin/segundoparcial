<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'document';

    protected $fillable = [
        'name',
        'descripcion',
        'diagramajson',
        'link',
        'user_id',
    ];
    use HasFactory;

    //creador
    public function user_document()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //participantes
    public function user_documents()
    {
        return $this->hasMany(UserDocument::class, 'document_id');
    }
}
