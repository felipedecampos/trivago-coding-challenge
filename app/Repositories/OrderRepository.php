<?php

namespace App\Repositories;


use App\Models\Order;
use App\Models\Wine;
use App\Models\WineOrder;
use App\Repositories\RepositoryInterface\RepositoryInterface;

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

    function __construct(Order $order) {
        $this->order     = $order;
    }

    public function getAll()
    {
        return $this->order->where('user_id', '=', auth()->user()->getAuthIdentifier())
            ->with('wine_order')
            ->get();
    }

    public function find($id)
    {
        return $this->order->query()->with('wineOrder')->find($id);
    }

    public function put(WineOrder $wineOrder, $orderForm)
    {
        $status = [];

        $this->wineOrder = $wineOrder;

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
            if (isset($wine['guid']) && isset($orderId)) {
                $this->wineOrder = $wineOrder->where('order_id', '=', $orderId)->where('wine_guid', '=', $wine['guid'])
                    ?? new WineOrder();
            }

            $wineOrder = ['order_id' => $orderId] + $wine;

            $this->wineOrder->fill($wineOrder);

            if (isset($orderForm['id'])) {
                $this->wineOrder->setAttribute('id', $orderForm['id']);
            }

            $status[] = $this->wineOrder->save();
        }

        return count(array_filter($status, function ($s){ return $s === true; })) === count($wines) + 1; // 1 = order
    }

    public function delete($id)
    {
        $order = $this->order->find($id);

        return $order->delete();
    }
}