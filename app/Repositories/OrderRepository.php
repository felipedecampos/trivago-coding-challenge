<?php

namespace App\Repositories;

use App\Models\Order;
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

    function __construct(Order $order, WineOrder $wineOrder) {
        $this->order     = $order;
        $this->wineOrder = $wineOrder;
    }

    public function getAll()
    {
        return $this->order->where('user_id', '=', auth()->user()->getAuthIdentifier())
            ->with('wine_order')
            ->with('waiter')
            ->with('sommelier')
            ->orderBy('id', 'DESC')
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

        $wines = $orderForm['wines'] ?? [];

        unset($orderForm['wines']);

        $order += $orderForm;

        $this->order->fill($order);

        if (isset($orderForm['id'])) {
            $this->order->setAttribute('id', $orderForm['id']);
        }

        $status[] = $this->order->save();

        $orderId = $this->order->getAttribute('id');

        if (count($wines)) {
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
        }

        return count(array_filter($status, function ($s){ return $s === true; })) === count($wines) + 1; // 1 = order
    }

    public function delete($id)
    {
        $order = $this->order->find($id);

        return $order->delete();
    }

    public function prepareOrder(int $waiterId)
    {
        $this->order->fill([
            'status'    => 'preparing',
            'waiter_id' => $waiterId
        ]);

        return $this->order->save();
    }

    public function sendToSommelier(int $sommelierId)
    {
        $this->order->fill([
            'sommelier_id' => $sommelierId
        ]);

        return $this->order->save();
    }

    public function processAvailabilityOfWines()
    {
        $this->order->wine_order->map(function (&$item) {
            $pubDate = new \DateTime($item->pub_date);
            $tz      = $pubDate->getTimezone();
            $now     = new \DateTime('now', $tz);

            $item->pivot->status = $now->format('Y-m-d') === $pubDate->format('Y-m-d')
                ? 'delivered'
                : 'unavailable';

            $item->pivot->save();
        });

        return $this->order->save();
    }

    public function deliverOrder()
    {
        $this->order->fill(['status' => 'closed']);

        return $this->order->save();
    }
}