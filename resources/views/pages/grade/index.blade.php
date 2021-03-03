@extends('layouts.master')
@section('css')
    @toastr_css
@section('title')
    @lang('site.Grades_trans.title_page')
@stop
@endsection
@section('page-header')
    <!-- breadcrumb -->
@section('PageTitle')
    @lang('main_trans.Grades')
@stop
<!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">

        <div class="col-xl-12 mb-30">
            <div class="card card-statistics h-100">
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <button type="button" class="button x-small" data-toggle="modal" data-target="#exampleModal">
                        @lang('site.Grades_trans.add_Grade')
                    </button>
                    <br><br>

                    <div class="table-responsive">
                        <table id="datatable" class="table  table-hover table-sm table-bordered p-0"
                               data-page-length="50"
                               style="text-align: center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('site.Grades_trans.Name')</th>
                                <th>@lang('site.Grades_trans.Notes')</th>
                                <th>@lang('site.Grades_trans.Processes')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($Grades as $index=>$Grade)
                                <tr>
                                    <td>{{ $index + 1  }}</td>
                                    <td>{{ $Grade->name }}</td>
                                    <td>{{ $Grade->notes }}</td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                                data-target="#edit{{ $Grade->id }}"
                                                title="@lang('site.Grades_trans.Edit')"><i
                                                class="fa fa-edit"></i></button>
                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                                data-target="#delete{{ $Grade->id }}"
                                                title="@lang('site.Grades_trans.Delete') "><i
                                                class="fa fa-trash"></i></button>
                                    </td>
                                </tr>

                                <!-- edit_modal_Grade -->
                                <div class="modal fade" id="edit{{ $Grade->id }}" tabindex="-1" role="dialog"
                                     aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title"
                                                    id="exampleModalLabel">
                                                    @lang('site.Grades_trans.edit_Grade')
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- add_form -->
                                                <form action="{{route('grade.update','test')}}" method="post">
                                                    {{method_field('patch')}}
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col">
                                                            <label for="Name"
                                                                   class="mr-sm-2">@lang('site.Grades_trans.stage_name_ar')
                                                                :</label>
                                                            <input id="Name" type="text" name="name_ar"
                                                                   class="form-control"
                                                                   value="{{$Grade->getTranslation('name', 'ar')}}"
                                                                   required>
                                                            <input id="id" type="hidden" name="id" class="form-control"
                                                                   value="{{ $Grade->id }}">
                                                        </div>
                                                        <div class="col">
                                                            <label for="Name_en"
                                                                   class="mr-sm-2"> @lang('site.Grades_trans.stage_name_en')
                                                                :</label>
                                                            <input type="text" class="form-control"
                                                                   value="{{$Grade->getTranslation('name', 'en')}}"
                                                                   name="name_en" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label
                                                            for="exampleFormControlTextarea1">{{ trans('site.Grades_trans.Notes') }}
                                                            :</label>
                                                        <textarea class="form-control" name="notes"
                                                                  id="exampleFormControlTextarea1"
                                                                  rows="3">{{ $Grade->notes }}</textarea>
                                                    </div>
                                                    <br><br>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">@lang('site.Grades_trans.Close') </button>
                                                        <button type="submit"
                                                                class="btn btn-success">@lang('site.Grades_trans.submit') </button>
                                                    </div>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- delete_modal_Grade -->
                                <div class="modal fade" id="delete{{ $Grade->id }}" tabindex="-1" role="dialog"
                                     aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title"
                                                    id="exampleModalLabel">
                                                    @lang('site.Grades_trans.delete_Grade')
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{route('grade.destroy','test')}}" method="post">
                                                    {{method_field('Delete')}}
                                                    @csrf
                                                    @lang('site.Grades_trans.Warning_Grade')
                                                    <input id="id" type="hidden" name="id" class="form-control"
                                                           value="{{ $Grade->id }}">
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">@lang('site.Grades_trans.Close') </button>
                                                        <button type="submit"
                                                                class="btn btn-danger">@lang('site.Grades_trans.submit') </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <!-- add_modal_Grade -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title"
                            id="exampleModalLabel">
                            @lang('site.Grades_trans.add_Grade')
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- add_form -->
                        <form action="{{ route('grade.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <label for="Name"
                                           class="mr-sm-2">@lang('site.Grades_trans.stage_name_ar')
                                        :</label>
                                    <input id="Name" type="text" name="name_ar" class="form-control" required>
                                </div>
                                <div class="col">
                                    <label for="Name_en"
                                           class="mr-sm-2">@lang('site.Grades_trans.stage_name_en')
                                        :</label>
                                    <input type="text" class="form-control" name="name_en" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label
                                    for="exampleFormControlTextarea1">@lang('site.Grades_trans.Notes')
                                    :</label>
                                <textarea class="form-control" name="notes" id="exampleFormControlTextarea1"
                                          rows="3"></textarea>
                            </div>
                            <br><br>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                        data-dismiss="modal">@lang('site.Grades_trans.Close') </button>
                                <button type="submit"
                                        class="btn btn-success">@lang('site.Grades_trans.submit') </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- row closed -->
@endsection
@section('js')
    @toastr_js
    @toastr_render
@endsection
