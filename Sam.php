<?php

/** Seller
 *  1. Get non-complete buying orders from DB
 *  2. Check order complete or not via API
 *  3. If completed, update DB for buy order
 *      3.1. Use API to place sell limit order
 *      3.2. Update place sell order in DB
 */