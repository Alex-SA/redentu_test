<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Image;
use File;

class LoadImageController extends Controller
{
    //

    public function __construct()
    {
        //         Set available  formats for watermark image
        Config::set('watermark_formats', ['png','gif']);

        //         Set dark and light font colors for watermark text
        Config::set('watermark_color_fonts', ['#333333','#cccccc']);

        //         Set step for luminance function
        Config::set('luminance_step', 10);

        //         Set limit dark/light picture for luminance function
                // assume a medium gray is the threshold, #acacac or RGB(172, 172, 172)
                // this equates to a luminance of 170
        Config::set('luminance_dark_light', 170);

        //         Set watermark opacity
        Config::set('watermark_opacity', 90);

    }


//    View main page
    public function index()
    {
        $watermark = $this->checkWaterMarkFile();

        if (file_exists(public_path() . '/txt/watermark.txt')) {
            $watermark_text = file_get_contents(public_path() . '/txt/watermark.txt');
        }
        $files = File::files(public_path() . '/pictures');

        return view('load.index', compact('watermark', 'watermark_text' , 'files'));
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

    //        Load Image

    /**
     * @param Request $request
     * @return $this
     */
    public function saveImage(Request $request)
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
            $input['imagename'] = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/pictures');
            $image->move($destinationPath, $input['imagename']);

            // open file as image resource

            if($watermark_type != 'without') {
                $imagename = $destinationPath . '/' . $input['imagename'];
                $position = $request->position;
                if (file_exists(public_path() . '/txt/watermark.txt')){
                    $watermark_text = file_get_contents(public_path() . '/txt/watermark.txt');
                }else{
                    if($watermark_type == 'text'){
                        return redirect()->route('load')->with('status', 'Text WaterMark file not found!')->with('type', 'error');
                    }

                }

                $result = $this->insertWaterMark($imagename, $watermark_type, $position, $watermark_text);
                if($result['result'] == 'error') {
                    return redirect()->route('load')->with('status', $result['message'])->with('type', 'error');
                }
            }
        }
        return redirect()->route('load')->with('status', 'The selected image was saved as ' . $input['imagename']);
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

            $img->text($watermark_text, 10, $height - 20, function ($font) use ($color_font, $font_size) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->color($color_font);
                $font->size($font_size);
                //                $font->angle(-45);
            });
        }

        $img->save();
        return  ['result' => 'insert', 'message' => 'The WaterMark is on the Image'];

    }




    public function checkInvertWaterMarkFile($luminance_picture)
    {
        $watermark_file = $this->checkWaterMarkFile();
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

    public function checkWaterMarkFile(){

        foreach (Config::get('watermark_formats') as $ext) {
            if (file_exists(public_path() . '/img/watermark.' . $ext)) {
                return 'watermark.' . $ext;
            }
        }
        return null;

    }

    public function checkWaterMarkFontSize($width)
    {
        return intval(24 * ($width / 480) > 7 ? 24 * ($width / 480) : 7);
    }

    //    Save WaterMark Text
    public function watermarkText(Request $request){

        $v = Validator::make($request->all(), [
            'text' => 'required|string|max:30',
        ]);
        if ($v->fails())
        {
            return redirect()->back()->withInput()->withErrors($v->errors());
        }
        $text = $request->text;
        file_put_contents(public_path() . '/txt/watermark.txt', $text);

        return redirect()->route('load');
    }

    //    Save WaterMark picture
    public function watermark(Request $request){

        $v = Validator::make($request->all(), [
            'picture' => 'required|image|max:50|mimes:gif,png',
        ]);

        if ($v->fails())
        {
            return redirect()->back()->withInput()->withErrors($v->errors());
        }

        if ($request->file('picture')) {
            $image = $request->file('picture');
            $input['imagename'] = 'watermark.' . $image->getClientOriginalExtension();

            //            Remove old watermark
            foreach (Config::get('watermark_formats') as $ext){
                $file = public_path() . '/img/watermark.'. $ext;
                if(file_exists($file)){
                    unlink($file);
                }
            }

            $destinationPath = public_path('/img');
            $image->move($destinationPath, $input['imagename']);
        }

        return redirect()->route('load');

    }

    public function cropImage(Request $request){

        $v = Validator::make($request->all(), [
            'image_height' => 'required|numeric|max:1000|min:20',
            'image_width' => 'required|numeric|max:1000|min:20',
        ]);

        if ($v->fails())
        {
            return redirect()->back()->withInput()->withErrors($v->errors());
        }

        $crop_height = $request->image_height;
        $crop_width = $request->image_width;
        $picture = $request->upload_image;

        $result = $this->smartCropImage($picture, $crop_width, $crop_height);

        if($result['result'] == 'error'){
            return redirect()->route('load')->with('crop status', $result['message'])->with('type' , 'error');
        }elseif($result['result'] == 'crop'){
            return redirect()->route('load')->with('crop status', $result['message']);
        } else{
            return redirect()->route('load')->with('crop status', 'Something went wrong!')->with('type' , 'error');
        }

    }


    public function smartCropImage($picture, $crop_width, $crop_height)
    {
        if (file_exists(public_path() . '/pictures/' . $picture)) {
            $image = Image::make(public_path('/pictures/' . $picture));
            $width = $image->width();
            $height = $image->height();

            if($width <= $crop_width && $height <= $crop_height ){
                // small image, no crop
                return ['result'=>'crop','message'=>'NO CROP: Image ' . $picture . ' is smaller than ' . $crop_width . 'x' . $crop_height];
            }elseif($width <= $crop_width){
                // narrow image, only crop height
                $top = round(($height  - $crop_height)/2);
                $image->crop( $width, $crop_height, 0, $top);
            }elseif ($height <= $crop_height){ //
                // low image, only crop width
                $left = round(($width  - $crop_width)/2);
                $image->crop( $crop_width, $height, $left, 0);
            }else{

                if($width - $crop_width < $height - $crop_height){
                    // resize to crop_width and crop height
                    $image->resize($crop_width, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });

                    $new_height = $image->height();
                    $top = round(($new_height  - $crop_height)/2);
                    $image->crop( $crop_width, $crop_height, 0, $top);

                }else{
                    // resize to crop_height and crop width
                    $image->resize(null, $crop_height, function ($constraint) {
                        $constraint->aspectRatio();
                    });

                    $new_width = $image->width();
                    $left = round(($new_width  - $crop_width)/2);
                    $image->crop( $crop_width, $crop_height, $left, 0);

                }

            }
        }else{
            return ['result'=>'error','message'=>'Image ' . $picture . ' not found'];
        }

        $image->save();

        return ['result'=>'crop','message'=>'Image ' . $picture . ' was cropped to ' . $crop_width . 'x' . $crop_height];
    }
}
