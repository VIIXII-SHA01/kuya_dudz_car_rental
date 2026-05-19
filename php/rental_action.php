<?php
http_response_code(410);
header('Content-Type: application/json');
echo json_encode([ 'error' => 'Rental API has been retired. Use /rent/php/booking_action.php for bookings and rental records.' ]);
exit;
