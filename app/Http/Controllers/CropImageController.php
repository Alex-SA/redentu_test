<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Image;
use File;

class CropImageController extends Controller
{
    //
    public function __construct()
    {
        //        Directory for uploaded pictures
        Config::set('pictures_dir', env('DIR_FOR_UPLOAD_PICTURES', '/pictures'));
    }

    //    View main page
    public function index()
    {
//        Get list of files from picture directory
        $files = File::files(public_path() . '/pictures');

        return view('crop.crop_image', compact('files'));
    }

    //    Crop and Save Image
    public function store(Request $request)
    {
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
            return redirect()->route('image_crop')->with('crop status', $result['message'])->with('type' , 'error');
        }elseif($result['result'] == 'crop'){
            return redirect()->route('image_crop')->with('crop status', $result['message']);
        } else{
            return redirect()->route('image_crop')->with('crop status', 'Something went wrong!')->with('type' , 'error');
        }

    }

    public function smartCropImage($picture, $crop_width, $crop_height)
    {
        $file = public_path() . Config::get('pictures_dir') .'/' . $picture;
        if (file_exists($file)) {
            $image = Image::make($file);
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

        $finish_width = $image->width();
        $finish_height = $image->height();

        return ['result'=>'crop','message'=>'The Image  <a href="' . Config::get('pictures_dir') . '/' . $picture . '" target="_blank">' . $picture . '</a>' . ' is cropped up to ' . $finish_width . 'x' . $finish_height];
    }

}
