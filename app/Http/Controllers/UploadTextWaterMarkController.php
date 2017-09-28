<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
Use File;

class UploadTextWaterMarkController extends Controller
{

    public function __construct()
    {
        //         Set available  formats for watermark image
        Config::set('watermark_formats', env('IMAGE_WATERMARK_FORMATS', ['png','gif']));

        //         Set txt file for text watermark
        Config::set('watermark_txt_file', env('TEXT_WATERMARK_FILE', '/txt/watermark.txt'));
    }

    //            View Page
    public function index()
    {
        $watermark = UploadWaterMarkController::checkWaterMarkFile();

        $file_txt = public_path() . Config::get('watermark_txt_file');
        if (file_exists($file_txt)) {
            $watermark_text = file_get_contents($file_txt);
        }

        return view('load.upload_text_watermark', compact('watermark', 'watermark_text' ));
    }

//            Save New WaterMarks as /img/watermark.* & *.txt
    public function store(Request $request){

        $watermark_ext = join(',', Config::get('watermark_formats'));

        $v = Validator::make($request->all(), [
            'picture' => 'image|max:50|mimes:'.$watermark_ext,
            'text' => 'nullable|string|max:30',
        ]);

        if ($v->fails())
        {
            return redirect()->back()->withInput()->withErrors($v->errors());
        }

        $message = '';

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
            $message = 'New WaterMark image saved! ';
        }

        if($text = $request->text){
            $file_txt = public_path() . Config::get('watermark_txt_file');
            file_put_contents($file_txt, $text);
            $message .= 'New Text WaterMark saved!';
        }
        if(!$message){
            return redirect()->route('upload_text_wm')->with('status', 'There is nothing to save!')->with('type', 'error');
        }
        return redirect()->route('upload_text_wm')->with('status', $message);

    }

}
