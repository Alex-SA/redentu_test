<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use File;

class ImageController extends Controller
{
    //

    public function __construct()
    {
        //        Directory for uploaded pictures
        Config::set('pictures_dir', env('DIR_FOR_UPLOAD_PICTURES', '/pictures'));
    }

    public function upload(Request $request)
    {
        if(!$request->hasFile('file'))
            return response()->json([
                'error' => 'No File Uploaded'
            ]);

        $file = $request->file('file');

        if(!$file->isValid())
            return response()->json([
                'error' => 'File is not valid!'
            ]);;


        $imagename = time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = public_path(Config::get('pictures_dir'));
        $file->move($destinationPath, $imagename);

        // Also can use storage directory for upload files
//        $file->store('public/pictures');

        return response()->json([
            'success' => 'File Uploaded'
        ]);

    }

    public function list()
    {

        $files = File::files(public_path() . Config::get('pictures_dir'));
//        $files = Storage::files('public/pictures');

        $output = [];

        foreach($files as $file) {
            $filename = basename($file);
            $output[] = asset(Config::get('pictures_dir'). '/' . $filename);
//            $output[] = asset('storage/pictures/' . $filename);
        }

        return $output;
    }

    public function index()
    {
        return view('load.image');
    }

}
