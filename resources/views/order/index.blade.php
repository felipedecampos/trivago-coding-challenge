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
                                            <div class="row bg-info">
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-left">
                                                    <strong>Order:</strong>
                                                    {{ (new \DateTime($order->created_at))->format('Y-m-d H:i:s') }}
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
                                                    <strong>Status:</strong>
                                                    @switch($order->status)
                                                        @case('open')
                                                            <span class="badge badge-light">Open</span>
                                                        @break

                                                        @case('preparing')
                                                            <span class="badge badge-warning">Preparing</span>
                                                        @break

                                                        @case('closed')
                                                            <span class="badge badge-dark">Closed</span>
                                                        @break

                                                        @default
                                                            <span class="badge badge-light">Undefined status</span>
                                                    @endswitch
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left">
                                                    <strong>Waiter:</strong>
                                                    {{
                                                        $order->waiter->first_name
                                                        ?? 'Waiting waiter process the order'
                                                    }}
                                                    {{$order->waiter->last_name ?? ''}}
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left">
                                                    <strong>Sommelier:</strong>
                                                    {{
                                                        $order->sommelier->first_name
                                                        ?? 'Waiting sommelier check the availibility of wines'
                                                    }}
                                                    {{ $order->sommelier->last_name ?? '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-3">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            @foreach($order->wineOrder as $wineKey => $wines)
                                                <div class="row">
                                                    <div class="
                                                        col-lg-6
                                                        col-md-6
                                                        col-sm-6
                                                        col-xs-6
                                                        text-left
                                                        bg-warning
                                                    ">
                                                        <strong>Item:</strong> {{ $wineKey+1 }}
                                                    </div>
                                                    <div class="
                                                        col-lg-6
                                                        col-md-6
                                                        col-sm-6
                                                        col-xs-6
                                                        text-right
                                                        bg-warning
                                                    ">
                                                        <strong>Status:</strong>
                                                        @switch($wines->pivot->status)
                                                            @case('placed')
                                                                <span class="badge badge-light">Placed</span>
                                                            @break

                                                            @case('delivered')
                                                                <span class="badge badge-primary">Delivered</span>
                                                            @break

                                                            @case('unavailable')
                                                                <span class="badge badge-danger">Unavailable</span>
                                                            @break

                                                            @default
                                                            <span class="badge badge-light">Undefined status</span>
                                                        @endswitch
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left">
                                                        <p>
                                                            <strong>Variety:</strong> {{ $wines->title }}<br>
                                                            <strong>Link:</strong>
                                                            <a href="{{ $wines->link }}" target="_blank">
                                                                {{ $wines->link }}
                                                            </a>
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
