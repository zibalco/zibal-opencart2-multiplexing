<?php

// Heading
$_['heading_title'] = 'Zibal Payment Gateway';

// Text
$_['text_title'] = 'Zibal Payment Gateway';
$_['text_basket'] = 'Shopping Cart';
$_['text_checkout'] = 'Checkout';

// Error
// Shown to the customer for every failed or erroneous payment
$_['error_generic'] = 'The payment failed. If money was deducted from your account, please contact the store support';

// Successful payment (order history)
$_['text_paid'] = 'Zibal payment successful';
$_['text_track_id'] = 'Track ID: ';
$_['text_ref_number'] = 'Reference number: ';
$_['text_card_number'] = 'Card number: ';
$_['text_paid_at'] = 'Transaction date: ';

// Order history notes (admin only)
$_['text_note_request'] = 'Payment request created at Zibal. Track ID: ';
$_['text_note_request_failed'] = 'Creating the payment request at Zibal failed.';
$_['text_note_curl'] = 'The cURL extension is not enabled on the server.';
$_['text_note_cancel'] = 'The customer returned from the Zibal gateway with a failed status (not paid or cancelled).';
$_['text_note_data'] = 'The data returned from the Zibal gateway is incomplete.';
$_['text_note_not_found'] = 'The returned track ID is not registered for this order. Verification was not performed.';
$_['text_note_status'] = 'This transaction has already been processed, status: ';
$_['text_note_amount_changed'] = 'The order total changed after the payment request was created. Verification was not performed.';
$_['text_note_connection'] = 'Could not connect to Zibal to verify the transaction. The customer can reload the page.';
$_['text_note_verify_failed'] = 'Verifying the transaction at Zibal failed.';
$_['text_note_mismatch'] = 'Zibal verified the payment but the amount or order ID does not match. The money has been deducted; manual review and possibly a refund are required.';
$_['text_note_201'] = 'Zibal reported that this transaction was already verified but it is not recorded in the store. Manual review is required.';
$_['text_note_log'] = 'The details of the Zibal response were written to the system error log.';
