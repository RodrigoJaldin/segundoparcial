<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    protected $table='userdocument';

    protected $fillable = [
        'user_id',
        'document_id',
    ];
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function documents()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }
}
