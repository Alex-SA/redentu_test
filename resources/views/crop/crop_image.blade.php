@extends('layouts.app')

@section('content')

<div class="row">
    <div class="container">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-primary">
                <div class="panel-heading">Smart image cropping</div>
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
                            <form class="form-horizontal" method="POST" action="{{route('image_crop')}}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="col-md-6">
                                    <div class="col-md-6">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="image" class="col-md-4 control-label">Picture</label>
                                        <select name="upload_image" id="upload_image" class="form-control">
                                            @foreach($files as $file)
                                                <option value="{{pathinfo($file)['basename']}}">{{pathinfo($file)['basename']}}</option>
                                            @endforeach

                                        </select>
                                    </div>
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
</div>

@endsection
