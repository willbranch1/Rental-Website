<?php
/*
 * This is a small number of the functions I created in the library for the webpage.
 * I created this to demonstrate some of the MySQL calls I made.
 */

/**
 * Updates available units in database so that the current unit is not available to other customers.
 *
 * @param [int] $cart_id
 * @param [int] $unit_id
 * @return [int]. 1 for success, 0 for failure
 */
function unholdUnitInCart($cart_id, $unit_id)
{
    global $db;
    $success = 1;

    try {
        $sql  = "delete from cart_unit where cart_id =:cart_id and unit_id = :unit_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->bindParam(':unit_id', $unit_id);
        $stmt->execute();

        $sql = "update units set in_cart = 0 where id = :unit_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':unit_id', $unit_id);
        $stmt->execute();
    } catch (PDOException $e) {
        $our_log_message = "freeInCartUnit failed, unit " . $unit_id . "\n";
        error_log($our_log_message, 3, "our.log");
        $success = 0;
    }

    return $success;
}

/**
 * Loads the units from a pre-existing cart.
 *
 * @param [int] $cart_id
 * @return [array] $contact, will be 0 if error has occurred
 */
function loadCartTempContact($cart_id)
{
    global $db;
    $contact_id = 0;
    $contact = [];

    $sql = "select temp_contact_id from cart_contact where cart_id = :cart_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':cart_id', $cart_id);
    $stmt->execute();
    $num = $stmt->rowCount();
    if ($num) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $contact_id = $row['temp_contact_id'];
    }

    $sql = "select * from temp_contact where contact_id = :contact_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':contact_id', $contact_id);
    $stmt->execute();
    $num = $stmt->rowCount();
    if ($num) {
        $contact = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    return $contact;
}

/**
 * Appends log if a unit is occupied or vacated.
 *
 * @param [int] $unit_id
 * @param [int] $customer_id
 * @param [int] $trans_type
 * @param [DATE_TIME] $effective_date
 * @param [varchar] $reason
 * @param [varchar] $notes
 * @return [int] 1 for success, 0 for failure
 */
function logOccupancyChange($unit_id, $customer_id, $trans_type, $effective_date, $reason, $notes)
{
    global $db;
    $success = 1;
    try {
        $sql = "insert into occupancy (id, unit_id, customer_id, trans_type, trans_time, effective_date, reason, notes) values (NULL, :unit_id, :customer_id, :trans_type, now(), :effective_date, :reason, :notes)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':unit_id', $unit_id);
        $stmt->bindParam(':customer_id', $customer_id);
        $stmt->bindParam(':trans_type', $trans_type);
        $stmt->bindParam(':effective_date', $effective_date);
        $stmt->bindParam(':reason', $reason);
        $stmt->bindParam(':notes', $notes);
        $stmt->execute();
    } catch (PDOException $e) {
        $our_log_message = "pdo exception errored logOccupancyChange " . $e->getMessage() . "\n";
        error_log($our_log_message, 3, "our.log");
        $success = 0;
    }

    return $success;
}

/**
 * Assigns a unit to a customer. 
 *
 * @param [int] $unit_id
 * @param [int] $customer_id
 * @return [int] 1 for success, 0 for failure
 */
function assignUnit($unit_id, $customer_id)
{
    global $db;

    try {
        $sql = "insert into cust_unit values (:customer_id, :unit_id)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':unit_id', $unit_id);
        $stmt->bindParam(':customer_id', $customer_id);
        $stmt->execute();
    } catch (PDOException $e) {
        $our_log_message = "pdo exception errored assignUnit " . $e->getMessage() . "\n";
        error_log($our_log_message, 3, "our.log");
        return 0;
    }
    return 1;
}

/**
 * Sets a price for a unit. 
 *
 * @param [int] $unit_id
 * @param [int] $rate_type
 * @return [int] always returns 1
 */
function setProdPriceForUnit($unit_id, $rate_type)
{
    global $db;

    // get the price from the rate table, insert it into inv_prod.retail_price
    try {
        $sql = "select r.rate from rates as r, units as u where u.unit_num = :unit_id and r.id = u.rate_type";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':unit_id', $unit_id);
        $stmt->execute();
        $num = $stmt->rowCount();
        if ($num) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $rate = $row['rate'];

            $sql = "update inv_prod set retail_price = :rate where prod_id = :unit_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':unit_id', $unit_id);
            $stmt->bindParam(':rate', $rate);
            $stmt->execute();
        }
    } catch (PDOException $e) {
        $our_log_message = "pdo exception errored setProdPriceForUnit " . $e->getMessage() . "\n";
        error_log($our_log_message, 3, "our.log");
    }

    return 1;
}

/**
 * Returns an array of all CONTACTS
 *
 * @return [arr] $contacts
 */
function getAllContactDetail()
{
    global $db;

    $contacts = [];

    // pull custcode, contact_id, lastname, firstname, status, vip, areacode, phone (startphone, endphone), email
    $sql = "select co.contact_id, co.lastname, co.firstname, co.areacode, co.phone, substring(co.phone, -7, 3) as startphone, substring(co.phone, 4) as endphone, co.email from customer_contact as cc, contact as co where cc.contact_id = co.contact_id order by co.lastname, co.firstname";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $num = $stmt->rowCount();
    if ($num > 0) {
        $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return $contacts;
}
