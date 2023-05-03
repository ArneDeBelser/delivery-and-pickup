<?php

class Delivery_And_Pickup_Checkout_View
{
    public $checkout;

    public function __construct()
    {
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            include_once WP_PLUGIN_DIR . '/woocommerce/woocommerce.php';
            $this->checkout = WC()->checkout();

            add_action('woocommerce_review_order_before_payment', [$this, 'checkout_question_field']);

            add_action('woocommerce_after_checkout_validation', [$this, 'checkout_question_field_validate'], 10, 2);
            add_action('woocommerce_checkout_update_order_meta', [$this, 'checkout_question_field_save']);

            add_filter('woocommerce_package_rates', [$this, 'reinitialize_package_rates'], 9999, 2);
            add_action('woocommerce_checkout_update_order_review', [$this, 'action_change_shipping_method'], 10, 1);

            add_action('wp_ajax_change_shipping_method', [$this, 'change_shipping_method']);
            add_action('wp_ajax_nopriv_change_shipping_method', [$this, 'change_shipping_method']);

            add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_scripts_and_styles'], 20);
        }
    }

    public function checkout_question_field()
    {
        echo "<div class='delivery-and-pickup-wrapper'>";

        echo "<div class='delivery-and-pickup-choice-field-wrapper'>";
        woocommerce_form_field(
            'delivery_and_pickup_type',
            array(
                'type'            => 'radio',
                'required'        => true,
                'class'           => array('delivery-and-pickup', 'form-row-wide'),
                'options'         => array(
                    'take_away' => __('Free pickup', 'dap'),
                    'delivery' => __('Delivery (€ 5,00)', 'dap'),
                ),
                'default'         => 'take_away',
            ),
            $this->checkout->get_value('delivery_and_pickup_type')
        );
        echo "</div>";

        echo "<div class='delivery-and-pickup-take_away-wrapper'>";
        woocommerce_form_field(
            'delivery_and_pickup_location',
            array(
                'type'            => 'select',
                'label'           => 'Select Pickup Point',
                'required'        => true,
                'class'           => array('delivery-and-pickup-take_away', 'form-row-wide'),
                'placeholder'     => __('Choose a pickup point', 'dap'),
                'options'         => [
                    'Choose a pickup location',
                    'Pickup Location Nivelle',
                    'Pickup Location Hamburg',
                    'Pickup Location Rome'
                ],
            ),
            $this->checkout->get_value('delivery_and_pickup_location')
        );
        echo "</div>";

        echo "<div class='delivery-and-pickup-delivery-wrapper'>";
        woocommerce_form_field(
            'delivery_and_pickup_date',
            array(
                'type'            => 'text',
                'label'           => 'Delivery on',
                'required'        => true,
                'class'           => array('delivery-and-pickup-delivery', 'form-row-wide'),
                'id'              => 'delivery-and-pickup-datepicker',
                'custom_attributes' => ['readonly' => true],
                'placeholder'       => __('Choose a date', 'dap')
            ),
            $this->checkout->get_value('delivery_and_pickup_date')
        );
        echo "</div>";

        echo "</div>";
    }

    public function checkout_question_field_validate($data, $errors)
    {
        $field_values = $this->checkout_question_get_field_values();

        if ($field_values['delivery_and_pickup_type'] === 'take_away') {
            if (empty($field_values['delivery_and_pickup_location'])) {
                $errors->add('take_away',  __("Please select a pick-up point"), 'dap');
                return;
            }
        }

        if ($field_values['delivery_and_pickup_type'] === 'delivery') {
            if (empty($field_values['delivery_and_pickup_date'])) {
                $errors->add('delivery',  __("Please enter a delivery date"), 'dap');
                return;
            }
        }
    }

    public function checkout_question_field_save($order_id)
    {
        $field_values = $this->checkout_question_get_field_values();

        foreach ($field_values as $field_name => $value) {
            if (!empty($field_values[$field_name])) {
                update_post_meta($order_id, $field_name, $value);
            }
        }
    }

    public function checkout_question_get_field_values()
    {
        $fields = [
            'delivery_and_pickup_type' => '',
            'delivery_and_pickup_location' => '',
            'delivery_and_pickup_date' => '',
        ];

        foreach ($fields as $field_name => $value) {
            if (!empty($_POST[$field_name])) {
                $fields[$field_name] = sanitize_text_field($_POST[$field_name]);
            } else {
                unset($fields[$field_name]);
            }

            // In case of take_away we don't want delivery data
            if ($fields['delivery_and_pickup_type'] == 'take_away') {
                unset($fields['delivery_and_pickup_date']);
            }

            // In case of delivery we don't want take_away data
            if ($fields['delivery_and_pickup_type'] == 'delivery') {
                unset($fields['delivery_and_pickup_location']);
            }
        }

        return $fields;
    }

    public function reinitialize_package_rates($rates, $package)
    {
        $user_chosen_shipping_method = WC()->session->get('delivery_and_pickup_type');

        $new_rates = [];

        foreach ($rates as $rate_id => $rate) {
            if ('flat_rate' === $rate->method_id && $user_chosen_shipping_method == 'delivery') {
                $new_rates[$rate_id] = $rate;
                break;
            }

            if ('local_pickup' === $rate->method_id && $user_chosen_shipping_method == 'take_away') {
                $new_rates[$rate_id] = $rate;
                break;
            }

            // When users first visit page, default to "Afhalen"
            if ('local_pickup' === $rate->method_id && empty($user_chosen_shipping_method)) {
                $new_rates[$rate_id] = $rate;
                break;
            }
        }

        return empty($new_rates) ? $rates : $new_rates;
    }

    public function change_shipping_method()
    {
        WC()->session->set('delivery_and_pickup_type', sanitize_text_field($_POST['shipping_method_type']));

        echo json_encode([
            'delivery_and_pickup_type' => WC()->session->get('delivery_and_pickup_type'),
        ]);

        exit();
    }

    public function action_change_shipping_method($post_data)
    {
        $packages = WC()->cart->get_shipping_packages();

        foreach ($packages as $package_key => $package) {
            $session_key = 'shipping_for_package_' . $package_key;
            $stored_rates = WC()->session->__unset($session_key);
        }
    }

    public function enqueue_frontend_scripts_and_styles()
    {
        wp_localize_script('dap-checkout', 'dap_checkout', [
            'delivery_and_pickup_type' => WC()->session->get('delivery_and_pickup_type'),
        ]);
        wp_localize_script('dap-checkout', 'dbdp_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    }
}
