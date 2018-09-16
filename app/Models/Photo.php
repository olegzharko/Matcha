<?php
/**
 * Created by PhpStorm.
 * User: ozharko
 * Date: 9/5/18
 * Time: 11:55 AM
 */

namespace Matcha\Models;


use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'photo';
    /* найти способ передать имя таблицы с контейнера */
    /* protected $table = $db['db']['dbtable']['users']; */



    /* ???
     * по какому шыблону мы создаем польщователя
     * */
    protected $fillable = [
        'user_id',
        'photo_src',
    ];

    public static function setUserPhoto($src)
    {
        Photo::create([
            'user_id' => $_SESSION['user'],
            'photo_src' => $src,
        ]);
    }

    public static function delUserPhoto($src)
    {
        Photo::where([
            'photo_src' => $src,
        ])->delete();
    }

    public static function allPhoto()
    {
        if (isset($_SESSION['user']))
            return Photo::all();
    }

    public static function getUserPhoto()
    {
        $allPhoto = self::allPhoto();
        foreach($allPhoto as $row) {
            if ($row->user_id == $_SESSION['user']) {

                $photoRow = Photo::where('photo_src', $row->photo_src)->first();
                $photoResult[] = $photoRow->photo_src;
            }
        }
        return $photoResult;
    }
}