# Abandon Order Email

WooCommerce plugin to automaticaly sent email for user who abandon their order (did not complete payment). This plugin need [https://tonjoo.github.io/wordpress-background-worker/](https://tonjoo.github.io/wordpress-background-worker/) to work.


## Usage
# Define Config

```
// The abandon order threshold
define('AOE_ABANDON_ORDER_THRESHOLD',500);

// The abandon order time
define('AOE_ABANDON_ORDER_EMAIL_TIME',1800);

// Start Date (YYYY-MM-DD) , only sent email to order created after this date
define('AOE_ABANDON_ORDER_EMAIL_START_DATE','2017-08-01');
```

# Template Email

filter : `aoe_order_email_template`
file : `template/abandon-order-email.php`


# Post Meta

- aoe_abandoned
- aoe_abandoned_emailed

# Filter
- aoe_abandon_email_template
