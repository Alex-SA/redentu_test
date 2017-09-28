@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Load Image with WaterMark</div>
                <div class="panel-body">
                    @if($status = Session::get('status'))
                        @if(Session::get('type') == 'error')
                            <div class="alert alert-danger" role="alert">
                        @else
                            <div class="alert alert-success" role="alert">
                        @endif
                                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                {!!  $status !!}
                            </div>
                    @endif
                    <div class="row">
                        <div class="col-md-5">
                            <label for="picture" class="col-md-6 control-label">WaterMark Picture:</label>
                            <div class="col-md-6">
                                @if(isset($watermark))
                                    <img style="max-height: 50px; padding: 10px;" src="/img/{{$watermark}}" alt="WaterMark Image" >
                                @else
                                    <span class="label label-warning">No WaterMark yet</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label for="picture" class="col-md-4 control-label">WaterMark Text:</label>
                            <div class="col-md-8">
                                @if(isset($watermark_text))
                                    <h4 class="pull-right">{{$watermark_text}}</h4>
                                @else
                                    <span class="label label-warning">No WaterMark Text yet</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <form method="get" action="{{route('upload_wm')}}">
                                <button class="btn btn-success btn-xs pull-right">Upload new WaterMark Picture</button>
                            </form>
                        </div>
                        <div class="col-md-5">
                            <form method="get" action="{{route('upload_text_wm')}}">
                                <button class="btn btn-success btn-xs pull-right">Upload new WaterMark Text</button>
                            </form>
                        </div>
                    </div>
                            <hr size="1">
                    <div class="row">
                        <form class="form-horizontal" method="POST" action="{{route('image_with_wm')}}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="col-md-3">
                                <div class="radio">
                                    <label> <input type="radio" name="watermark" id="watermark" value="without" checked
                                                   onclick="document.getElementById('watermark_position').style.display='none'"
                                        >WithOut WaterMark</label>
                                </div>
                                @if(isset($watermark))
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="watermark" id="watermark" value="picture"
                                                   @if( old('watermark') == 'picture' )
                                                   checked
                                                   @endif
                                                   onclick="document.getElementById('watermark_position').style.display='block'">Use WaterMark Picture
                                        </label>
                                    </div>
                                @endif
                                @if(isset($watermark_text))
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="watermark" id="watermark" value="text"
                                                   @if( old('watermark') == 'text' )
                                                   checked
                                                   @endif
                                                   onclick="document.getElementById('watermark_position').style.display='none'"
                                            >Use WaterMark Text
                                        </label>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-2" style="display: none" id="watermark_position">
                                <label for="image" class="col-md-4 control-label">Position</label>
                                <select name="position" id="position" class="form-control">
                                    <option value="top-left">top-left</option>
                                    <option value="top">top</option>
                                    <option value="top-right">top-right</option>
                                    <option value="left">left</option>
                                    <option value="center">center</option>
                                    <option value="right">right</option>
                                    <option value="bottom-left">bottom-left</option>
                                    <option value="bottom">bottom</option>
                                    <option value="bottom-right">bottom-right</option>
                                </select>
                            </div>
                            <div class="col-md-7">

                                <div class="form-group {{ $errors->has('image') ? ' has-error' : '' }}">
                                    <label for="image" class="col-md-4 control-label">Select Image</label>
                                    <div class="col-md-8">
                                        <input id="image" type="file" class="form-control" name="image" value="">
                                        @if ($errors->has('image'))
                                            <div class="alert alert-danger">
                                                <strong>{{ $errors->first('image') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-6">
                                        <button type="submit" class="btn btn-primary pull-right">
                                            Save Image
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
