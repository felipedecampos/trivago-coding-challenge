<?php

namespace App\Repositories;


use App\Models\Order;
use App\Models\WineOrder;
use App\Repositories\RepositoryInterface\RepositoryInterface;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Class OrderRepository
 * @codeCoverageIgnore
 */
class OrderRepository implements RepositoryInterface
{
    /**
     * @var Order
     */
    public $order;

    /**
     * @var WineOrder
     */
    public $wineOrder;

    function __construct(Order $order, WineOrder $wineOrder) {
        $this->order     = $order;
        $this->wineOrder = $wineOrder;
    }

    public function getAll()
    {
        return $this->order->where('user_id', '=', auth()->user()->getAuthIdentifier())
            ->with('wine_order')
            ->with('waiter')
            ->get();
    }

    public function find($id)
    {
        return $this->order->query()->with('wine_order')->find($id);
    }

    public function put($orderForm)
    {
        $status = [];

        if (isset($orderForm['id'])) {
            $this->order = $this->find($orderForm['id']) ?? new Order();
        }

        $order = ['user_id' => auth()->user()->getAuthIdentifier()];

        $wines = $orderForm['wines'];

        unset($orderForm['wines']);

        $order += $orderForm;

        $this->order->fill($order);

        if (isset($orderForm['id'])) {
            $this->order->setAttribute('id', $orderForm['id']);
        }

        $status[] = $this->order->save();

        $orderId = $this->order->getAttribute('id');

        foreach ($wines as $wine) {
            $query = $this->wineOrder->query()
                ->where('order_id', '=', $orderId)
                ->where('wine_guid', '=', $wine);

            $this->wineOrder = $query->exists()
                    ? $query->first()
                    : new WineOrder();

            $wineOrder = ['order_id' => $orderId] + ['wine_guid' => $wine];

            $this->wineOrder->fill($wineOrder);

            $status[] = $this->wineOrder->save();
        }

        return count(array_filter($status, function ($s){ return $s === true; })) === count($wines) + 1; // 1 = order
    }

    public function delete($id)
    {
        $order = $this->order->find($id);

        return $order->delete();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getNextOrder()
    {
        return $this->order->query()
            ->with('wine_order')
            ->with('waiter')
            ->whereHas('waiter', function($q){
                $q->where('available', '=', true);
            })
            ->where('status', '=', 'open')
            ->orderBy('id' ,'ASC')
            ->first();
    }
}