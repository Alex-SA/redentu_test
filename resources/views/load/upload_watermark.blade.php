@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-primary">
                    <div class="panel-heading">Load WaterMark Image</div>
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
                            <div class="col-md-4">
                                @if(isset($watermark))
                                    <img style="max-height: 50px;" src="/img/{{$watermark}}" alt="WaterMark Image">
                                @else
                                    <span class="label label-warning">No WaterMark yet</span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <form class="form-horizontal" method="POST" action="{{route('upload_wm')}}" enctype="multipart/form-data">
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
            </div>
        </div>
    </div>

@endsection
