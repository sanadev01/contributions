<?php

return [
    'manage-orders' => 'Manage Orders',
    'date' => 'Date',
    'order-id' => 'Order ID',
    'address' => 'Address',
    'amount' => 'Amount',
    'status' => 'Status',
    'type' => 'Type',
    'payment-status' => 'Payment Status',
    'payment-method' => 'Payment Method',
    'actions' => 'Action',
    'view-order' => 'View Order',
    'view-invoice' => 'View Invoice',
    'edit-invoice' => 'Edit Invoice',
    'print-invoice' => 'Print Invoice',
    'track-order' => 'Track Order',
    'pay-order' => 'Pay Order',
    'create-order' => 'Create Order',
    'back-to-list' => 'Back to List',
    'create' => [
        'shipment-and-destination' => 'Shipment & Destination',
        'shipping-service' => 'Shipping Service',
        'Payment' => 'Payment',
        'drag-and-drop-items' => 'Drag and Drop Items to Order List To Order Items',
        'available-shipments' => 'Available Shipments',
        'order-list' => 'Order List',
        'destination-address' => 'Destination Address',
        'select-address' => 'Select Address',
        'create-address' => 'create-address',
        'handling-services' => 'Handling Services',
        'note' => 'To create a consolidated shipment drag more than 1 available shipment to ‘’order list’’.',
        'save' => 'Save',
        'alert-success-consolidate' => 'Your Request for Consolidate Order is Received. it will take 24 hours to process your request. you will be notified',
        'alert-success' => 'Order Created Success fully. Please Select a shipping method to pay',
    ],
    'shipping' => [
        'shipping-services' => 'Shipping Services',
        'please-select-a-shipping-service' => 'Please Select a shipping service',
        'service-name' => 'Service Name',
        'weight' => 'Weight',
        'freight-cost' => 'Freight Cost',
        'total' => 'Total',
        'select-service' => 'Select Service',
        'save-and-checkout' => 'Save & Checkout',
        'alert-success' => 'Your Shipping Method Is Selected Please pay the requested amount against your order',
    ],
    'invoice' => [
        'Upload or Create Invoice' => 'Upload or Create Invoice',
        'Please upload invoice or create one' => 'favor adicionar seu recibo de compra (invoice do local de compra) e/ou crie uma declaração',
        'Upload Invoice' => 'Upload Invoice',
        'Create Invoice' => 'Create Invoice'
    ],
    'create-invoice' => [
        'Create Invoice' => 'Create Invoice',
        'consolidated-alert' => 'You have Consolidated order. please create a new invoice',
        'Description' => 'Description',
        'Quantity' => 'Quantity',
        'NCM/Harmonized Code' => 'select the universal product code (copy and paste the link number in the NCM field)',
        'Value/Unit' => 'Value/Unit',
        'Total' => 'Total',
        'Add Item' => 'Add Item',
        'freight' => 'Freight',
        'Remove' => 'Remove',
        'save' => 'Save',
        'validation' => [
            'ncm-*' => 'The NCM/Harmonized code field must have 6 digits.',
            'qty-*' => 'The quantity field must be at least 1. Fields with 0 will not be accepted.',
            'value-*' => 'The unit price must be at least $1 dollar. Fields with 0 will not be accepted.',
            'freight-*' => 'The freight field cannot be zero.'
        ],
        'alert-success' => 'Invoice Generated Successfully. Please Proceed to checkout',
        'disclaimer' => 'I have read and confirm that all information in this statement has been filled in by me and is correct and I am 100% responsible for the information contained herein.'
    ],
    'payment' => [
        'payment' => 'Payment',
        'amount-due' => 'Your amount due is US$ :amount pay now, Thank you',
        'Change Shipping Method' => 'Change Shipping Method',
        'Payment Information' => 'Payment Information',
        'Select Payment Method' => 'Select Payment Method',
        'Cash On Delivery' => 'Cash On Delivery',
        'Credit Card' => 'Credit Card',
        'Billing Information' => 'Billing Information',
        'Name' => 'First Name',
        'Last Name' => 'Last Name',
        'Phone' => 'Phone',
        'Address' => 'Address',
        'State' => 'State',
        'Zipcode' => 'Zip Code',
        'Country' => 'Country',
        'Card Number' => 'Card Number',
        'Expiration' => 'Expiration',
        'Security Code' => 'Security Code',
        'Continue to checkout' => 'Continue to checkout',
        'Save Billing Information' => 'Save Billing Information',
        'alert-success' => 'Your notice has been successfully created. As soon as your order arrives at our center you will be notified by email within 48 working hours, please wait.',
    ]
];
