<?php

class Delivery_And_Pickup_Order
{
    public function __construct()
    {
        add_action('woocommerce_admin_order_data_after_shipping_address', [$this, 'display_order_meta_data'], 10, 1);
    }

    public function display_order_meta_data($order)
    {
        $type = $order->get_meta('delivery_and_pickup_type');

        if ($type == 'take_away') {
            $this->print('Take away', $this->match_shop($order->get_meta('delivery_and_pickup_location')));
        }

        if ($type == 'delivery') {
            $this->print('Delivery', $order->get_meta('delivery_and_pickup_date'));
        }
    }

    private function match_shop($id)
    {
        $shops = [
            1 => 'Pickup Location Nivelle',
            2 => 'Pickup Location Hamburg',
            3 => 'Pickup Location Rome'
        ];

        return $shops[$id];
    }

    private function print($title, $parameter)
    {
        echo sprintf('<h3>%s</h3><div class="shipping_type"><p>%s<br />%s</p></div>', __('Pickup/Delivery', 'dap'), __($title, 'dap'), $parameter);
    }
}
