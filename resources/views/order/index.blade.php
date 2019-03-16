@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-left">
                                Order list
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
                                <a href="{{ route('orders.create') }}" class="btn btn-sm btn-success">
                                    + Place order
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

                        @forelse($orders as $orderKey => $order)
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="row m-0">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="row bg-warning">
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-left">
                                                    Order: {{ (new \DateTime($order->created_at))->format('Y-m-d H:i:s') }}
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center">
                                                    Waiter: {{ $order->waiter->first_name }} {{ $order->waiter->last_name }}
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-right">
                                                    Status: {{ $order->status }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-3">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            @foreach($order->wine_order as $wineKey => $wines)
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right bg-info">
                                                        Item: {{ $wineKey+1 }}
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-left bg-info">
                                                        Status: {{ $wines->pivot->status }}
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left">
                                                        <p>
                                                            Variety: {{ $wines->title }}<br>
                                                            Status: {{ $wines->status }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            {{ 'You don\'t have any order placed yet!' }}
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
