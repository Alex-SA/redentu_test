<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Image;
use File;

class UploadImageWithWaterMarkController extends Controller
{
    //
    public function __construct()
    {
        //         Set available  formats for watermark image
        Config::set('watermark_formats', env('IMAGE_WATERMARK_FORMATS', ['png','gif']));

        //         Set txt file for text watermark
        Config::set('watermark_txt_file', env('TEXT_WATERMARK_FILE', '/txt/watermark.txt'));

        //         Set dark and light font colors for watermark text
        Config::set('watermark_color_fonts', env('WATERMARK_COLOR_FONTS', ['#333333','#cccccc']));

        //         Set step for luminance function
        Config::set('luminance_step', 10);

        //         Set limit dark/light picture for luminance function
        // assume a medium gray is the threshold, #acacac or RGB(172, 172, 172)
        // this equates to a luminance of 170
        Config::set('luminance_dark_light', 170);

        //         Set watermark opacity
        Config::set('watermark_opacity', env('WATERMARK_OPACITY',80));

//        Directory for uploaded pictures
        Config::set('pictures_dir', env('DIR_FOR_UPLOAD_PICTURES','/pictures'));
    }

    //            View Page
    public function index()
    {
        $watermark = UploadWaterMarkController::checkWaterMarkFile();

        $file_txt = public_path() . Config::get('watermark_txt_file');
        if (file_exists($file_txt)) {
            $watermark_text = file_get_contents($file_txt);
        }

        return view('load.upload_image_with_watermark', compact('watermark', 'watermark_text' ));
    }


        //    Upload Image with WaterMark
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'image' => 'required|image|max:3000|mimes:gif,png,jpeg,jpg',
        ]);
        if ($v->fails()) {
            return redirect()->back()->withInput()->withErrors($v->errors());
        }
        $watermark_type = $request->watermark;

        if ($request->file('image')) {
            $image = $request->file('image');
            $imagename = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path(Config::get('pictures_dir'));
            $image->move($destinationPath, $imagename);

            // open file as image resource

            if($watermark_type != 'without') {
                $position = $request->position;
                $file_txt = public_path() . Config::get('watermark_txt_file');
                if (file_exists($file_txt)){
                    $watermark_text = file_get_contents($file_txt);
                }else{
                    if($watermark_type == 'text'){
                        return redirect()->route('image_with_wm')->with('status', 'Text WaterMark file not found!')->with('type', 'error');
                    }
                }

                $result = $this->insertWaterMark($destinationPath . '/' .$imagename, $watermark_type, $position, $watermark_text);

                if($result['result'] == 'error') {
                    return redirect()->route('image_with_wm')->with('status', $result['message'])->with('type', 'error');
                }
            }
        }else{
            return redirect()->route('image_with_wm')->with('status', 'Error upload file!')->with('type', 'error');
        }
        return redirect()->route('image_with_wm')
            ->with('status', 'The selected image was saved as <a href="' . Config::get('pictures_dir') . '/' . $imagename . '" target="_blank">' . $imagename . '</a>');
    }

    public function insertWaterMark($imagename, $watermark_type, $position, $watermark_text){
        //        Insert WanerMark Image OR WanerMark Text to Picture
        //  $imagename      - Picture File
        //  $watermark_type - WanerMark Type ( picture / text )
        //  $position - (top-left/top-right/center/ .... )
        //  $watermark_text - string
        //

        $img = Image::make($imagename);

        $width = $img->width();
        $height = $img->height();

        $luminance_picture = $this->get_avg_luminance($imagename, Config::get('luminance_step'));
        //            $luminance_picture:  for more correct can to test crop place for $watermark

        if($watermark_type == 'picture'){
            $watermark = $this->checkInvertWaterMarkFile($luminance_picture);
            if(isset($watermark)){
                $img->insert($watermark->opacity(Config::get('watermark_opacity')), $position, 10, 10);
            }else{
                return  ['result' => 'error', 'message' => 'WaterMark Image File not found'];
            }
        }

        if($watermark_type == 'text') {
//                    Check Font Color for WaterMark Text
            if ($luminance_picture > Config::get('luminance_dark_light')) {
                $color_font = Config::get('watermark_color_fonts')[0];
            } else {
                $color_font = Config::get('watermark_color_fonts')[1];
            }

            $font_size = $this->checkWaterMarkFontSize($width);
            // write text to position

//        can move settings for placing text in a config file
//            $img->text($watermark_text, 10, $height - 20, function ($font) use ($color_font, $font_size) {
            $img->text($watermark_text, 20, 20, function ($font) use ($color_font, $font_size) {
                $font->angle(-45);

                $font->file(public_path('fonts/arial.ttf'));
                $font->color($color_font);
                $font->size($font_size);
            });
        }

        $img->save();
        return  ['result' => 'insert', 'message' => 'The WaterMark is on the Image'];

    }

    // get average luminance, by sampling $num_samples times in both x,y directions
    public function get_avg_luminance($filename, $num_samples=10)
    {
        $img = Image::make($filename);

        $width = $img->width();
        $height = $img->height();

        $x_step = intval($width/$num_samples);
        $y_step = intval($height/$num_samples);

        $total_lum = 0;

        $sample_no = 1;

        for ($x=0; $x<$width; $x+=$x_step) {
            for ($y=0; $y<$height; $y+=$y_step) {

                $rgb = $img->pickColor($x, $y );

                list($r, $g, $b) = $rgb;
                // choose a simple luminance formula from here
                // http://stackoverflow.com/questions/596216/formula-to-determine-brightness-of-rgb-color
                $lum = ($r+$r+$b+$g+$g+$g)/6;

                $total_lum += $lum;

                $sample_no++;
            }
        }

        // work out the average
        $avg_lum  = $total_lum/$sample_no;

        return $avg_lum;
    }

        // Check: Need invert WaterMark image or not
    public function checkInvertWaterMarkFile($luminance_picture)
    {
        $watermark_file = UploadWaterMarkController::checkWaterMarkFile();
        if (isset($watermark_file)){
            $luminance_water = $this->get_avg_luminance(public_path('/img/' . $watermark_file), Config::get('luminance_step'));
            $watermark = Image::make(public_path('/img/' . $watermark_file));

            $d_l = Config::get('luminance_dark_light');
            if (($luminance_picture > $d_l && $luminance_water > $d_l) ||  // "both light";
                ($luminance_picture < $d_l && $luminance_water < $d_l))  // "both  dark";
            {
                $watermark->invert();
            }
            return $watermark;
        }else{
            return null;
        }
    }

        //    get WaterMark Text FontSize for  small / large picture
        //    very simple ...
        //     Need check text size in pixels and picture size
    public function checkWaterMarkFontSize($width)
    {
        return intval(24 * ($width / 480) > 7 ? 24 * ($width / 480) : 7);
    }


}
