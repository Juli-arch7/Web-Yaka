<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = ['store_name', 'email', 'phone', 'address'];

    public static function get()
    {
        return self::first() ?? self::create([
            'store_name' => 'Yaka Store'
        ]);
    }
}