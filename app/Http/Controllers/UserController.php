<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);
    }

    public function upload(Request $request){
        $file = $request->file('upload-file');
        if ($file) {
            $filename = $file->getClientOriginalName();
            if(!file_exists(public_path('/uploads'))) mkdir(public_path('/uploads'));
            $location = 'uploads';
            $file->move($location, $filename);
            $users = $this->readCSV(public_path($location . "/" . $filename),  ['delimiter' => ';']);
            foreach ($users as $user){
                if(isset($user->name) && isset($user->email) && $this->validator(['email'=>$user->email])->validate()){
                    $user = [
                        'name'=> $user->name,
                        'email'=> $user->email,
                        'password'=>Hash::make(Str::random(30)),
                    ];
                    User::create($user);
                }
            }
        }
        return redirect()->route('home');
    }

    public static function readCSV($csvFile, $array, $map = null)
    {
        $file_handle = fopen($csvFile, 'r');
        while (!feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, 0, $array['delimiter']);
        }
        $line_of_text = array_map(function($item)use($map){
            if(!is_array($item)) return null;
            if($map){
                $tmp = array();
                foreach ($map as $key => $value){
                    if(array_key_exists($key, $item)){
                        $tmp[$value] = $item[$key];
                    }
                }
                return $tmp;
            }else{
                return $item;
            }
        }, $line_of_text);
        $data = $line_of_text;
        $keys = $line_of_text[0];
        array_splice($data, 0, 1);
        $collect = collect();
        foreach ($data as $item){
            if($item){
                $tmp = [];
                foreach ($keys as $k => $v){
                    if(array_key_exists($k, $item)) $tmp[$v] = $item[$k];
                }
                $collect->push((object) $tmp);
            }
        }
        fclose($file_handle);
        return $collect;
    }

}
