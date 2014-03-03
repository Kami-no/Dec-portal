<?php

// Socket write function
function sWrite($socket, $data)
{
    // Send command to socket
    fputs($socket, $data);
    // Get the first bit of response
    $answer = fread($socket, 1);
    // Get socket status
    $remains = socket_get_status($socket);
    // Get the rest of response
    if ($remains-- > 0) $answer .= fread($socket, $remains['unread_bytes']);
    // Return the result
    return htmlentities($data.$answer);
}

// Mail check function
function mCheck($to, $from, $srv, $bool = FALSE, $debug = FALSE)
{
    // Get the MX record for the mail
    $mx_array = explode("@", $to);
    $mx = dns_get_record(end($mx_array), DNS_MX);
    $mx = $mx[0]['target'];
    if ($ms = NULL)
        // No MX records found
        $return = 5;
    else {
        // Open socket
        $socket = fsockopen($mx, 25, $errno, $errstr, 10);
        if (!$socket) {
            // Connection failure
            $msg = "$errstr ($errno)\n";
            $return = 4;
        } else {
            // Blank line to begin
            $msg = sWrite($socket, "");
            // Introduction
            $data = "EHLO " . $srv . "\r\n";
            $msg .= sWrite($socket, $data);
            $data = "MAIL FROM: <" . $from . ">\r\n";
            $msg .= sWrite($socket, $data);
            // Try the recipient
            $data = "RCPT TO: <" . $to . ">\r\n";
            $response = sWrite($socket, $data);
            $msg .= $response;
            // Close the connection
            $data = "QUIT\r\n";
            $msg .= sWrite($socket, $data);
            fclose($socket);
            // Parse the response
            if (substr_count($response, "550") > 0) {
                // Required email address does not exist
                $return = 3;
            } else if (substr_count($response, "250") > 0) {
                if (substr_count(mb_strtoupper($response), "OK") > 0) {
                    // Required email address exists
                    $return = 0;
                } else {
                    // Email address accepted but it looks like the server is working as a relay host
                    $return = 1;
                }
            } else {
                // Required email address existence was not recovered
                $return = 2;
            }
        }
    }
    if ($bool) {
        if ($return < 3)
            $return = TRUE;
        else
            $return = FALSE;
    }
    if ($debug) echo '<br>' . nl2br($msg);
    return $return;
}
