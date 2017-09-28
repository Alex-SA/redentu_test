<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use File;

class ImageController extends Controller
{
    //
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
        $destinationPath = public_path('/pictures');
        $file->move($destinationPath, $imagename);

        // Also can use storage directory for upload files
//        $file->store('public/pictures');

        return response()->json([
            'success' => 'File Uploaded'
        ]);

    }

    public function list()
    {

        $files = File::files(public_path() . '/pictures');
//        $files = Storage::files('public/pictures');

        file_put_contents(public_path() . '/txt/log.txt', $files);

        $output = [];

        foreach($files as $file) {
            $filename = basename($file);
            $output[] = asset('/pictures/' . $filename);
//            $output[] = asset('storage/pictures/' . $filename);
        }

        return $output;
    }

    public function index()
    {
        return view('load.image');
    }

}
