@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-left">
                                Order form
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
                                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-primary">
                                    Back
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <h2>Place an order</h2><br/>
                        <form method="post" action="{{ route('orders.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <lable> <strong>Wine:</strong> <br>
                                        <select name="wines[]" multiple style="height:150px;">
                                            @foreach($wines as $wine)
                                                <option
                                                    value="{{ $wine->link }}"
                                                    available="@if(new \DateTime('today', new \DateTimeZone('-05:00')) == new \DateTime($wine->pub_date)){{ '1' }}@else{{ '0' }}@endif"
                                                >
                                                    {{ $wine->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </lable>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center mt-5">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
