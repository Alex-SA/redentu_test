@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Load WaterMarks Image / Text</div>
                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{route('upload_text_wm')}}" enctype="multipart/form-data">
                            {{ csrf_field() }}
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
                                </div>
                            </div>

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
                                                    Save New WaterMarks
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection
