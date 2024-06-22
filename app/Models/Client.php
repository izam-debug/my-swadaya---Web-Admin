<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class Client extends Model implements Authenticatable
{
    use AuthenticatableTrait;
    use HasFactory;
    protected $table = 'client';
    protected $fillable = [
        'nama_client',
        'username',
        'password',
        'no_whatsapp',
        'kode_client'
    ];

    public static function generateUniqueCode($length = 5)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $code = '';

        // Generate unique code
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        // Check uniqueness in the database
        while (self::where('kode_client', $code)->exists()) {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[rand(0, strlen($characters) - 1)];
            }
        }

        return $code;
    }
}
