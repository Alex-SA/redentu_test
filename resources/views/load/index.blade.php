@extends('layouts.app')

@section('content')
    <div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Load WaterMark Image</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if(isset($watermark))
                                <img style="max-height: 50px;" src="/img/{{$watermark}}" alt="WaterMark Image">
                            @else
                                <span class="label label-warning">No WaterMark yet</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <form class="form-horizontal" method="POST" action="{{route('watermark')}}" enctype="multipart/form-data">
                                {{ csrf_field() }}

                                <div class="form-group {{ $errors->has('picture') ? ' has-error' : '' }}">
                                    <label for="picture" class="col-md-4 control-label">Picture</label>

                                    <div class="col-md-8">
                                        <input id="picture" type="file" class="form-control" name="picture" value="">
                                        @if ($errors->has('picture'))
                                            <div class="alert alert-danger">
                                                <strong>{{ $errors->first('picture') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-6">
                                        <button type="submit" class="btn btn-primary pull-right">
                                            Save New WaterMark
                                        </button>

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">Load WaterMark Text</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if(isset($watermark_text))
                                <h3>{{$watermark_text}}</h3>
                            @else
                                <span class="label label-warning">No WaterMark Text yet</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <form class="form-horizontal" method="POST" action="{{route('watermark.text')}}" >
                                {{ csrf_field() }}

                                <div class="form-group {{ $errors->has('text') ? ' has-error' : '' }}">
                                    <label for="text" class="col-md-4 control-label">Text</label>

                                    <div class="col-md-8">
                                        <input id="text" class="form-control" name="text" value="">
                                        @if ($errors->has('text'))
                                            <div class="alert alert-danger">
                                                <strong>{{ $errors->first('text') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-6">
                                        <button type="submit" class="btn btn-primary pull-right">
                                            Save New WaterMark Text
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

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
                        <form class="form-horizontal" method="POST" action="{{route('image')}}" enctype="multipart/form-data">
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


            <div class="panel panel-default">
                <div class="panel-heading">Crop Image</div>
                <div class="panel-body">
                    @if($status = Session::get('crop status'))
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
                            <form class="form-horizontal" method="POST" action="{{route('crop')}}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="col-md-4">
                                    <label for="image" class="col-md-4 control-label">Picture</label>
                                    <select name="upload_image" id="upload_image" class="form-control">
                                        @foreach($files as $file)
                                            <option value="{{pathinfo($file)['basename']}}">{{pathinfo($file)['basename']}}</option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="col-md-6">

                                    <div class="form-group {{ $errors->has('image') ? ' has-error' : '' }}">
                                        <label for="image_width" class="col-md-4 control-label">Width</label>
                                        <div class="col-md-8">
                                            <input id="image_width" class="form-control" name="image_width" value="{{old('image_width')}}">
                                            @if ($errors->has('image_width'))
                                                <div class="alert alert-danger">
                                                    <strong>{{ $errors->first('image_width') }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group {{ $errors->has('image') ? ' has-error' : '' }}">
                                        <label for="image_height" class="col-md-4 control-label">Height</label>
                                        <div class="col-md-8">
                                            <input id="image_height" class="form-control" name="image_height" value="{{old('image_height')}}">
                                            @if ($errors->has('image_height'))
                                                <div class="alert alert-danger">
                                                    <strong>{{ $errors->first('image_height') }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-6">
                                            <button type="submit" class="btn btn-primary pull-right">
                                                Crop Image
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
