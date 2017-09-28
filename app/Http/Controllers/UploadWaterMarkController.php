<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
Use File;

class UploadWaterMarkController extends Controller
{

    public function __construct()
    {
        //         Set available  formats for watermark image
        Config::set('watermark_formats', env('IMAGE_WATERMARK_FORMATS', ['png','gif']));
    }

    //            View Page
    public function index()
    {
        $watermark = $this->checkWaterMarkFile();

        return view('load.upload_watermark', compact('watermark' ));
    }

//            Save New WaterMark as /img/watermark.*
    public function store(Request $request){

        $watermark_ext = join(',', Config::get('watermark_formats'));

        $v = Validator::make($request->all(), [
            'picture' => 'required|image|max:50|mimes:'.$watermark_ext,
        ]);

        if ($v->fails())
        {
            return redirect()->back()->withInput()->withErrors($v->errors());
        }

        if ($request->file('picture')) {
            $image = $request->file('picture');
            $imagename = 'watermark.' . $image->getClientOriginalExtension();

            //            Remove old watermark
            foreach (Config::get('watermark_formats') as $ext){
                $file = public_path() . '/img/watermark.'. $ext;
                if(file_exists($file)){
                    unlink($file);
                }
            }

            $destinationPath = public_path('/img');
            $image->move($destinationPath, $imagename);
        }

        return redirect()->route('upload_wm')->with('status', 'New WaterMark image saved!');

    }

    static function checkWaterMarkFile(){

        foreach (Config::get('watermark_formats') as $ext) {
            if (file_exists(public_path() . '/img/watermark.' . $ext)) {
                return 'watermark.' . $ext;
            }
        }
        return null;
    }

}
