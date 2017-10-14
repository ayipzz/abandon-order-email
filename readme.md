# Abandon Order Email

Automaticaly sent email for user who abandon their order (did not complete payment). This plugin need [https://tonjoo.github.io/wordpress-background-worker/](https://tonjoo.github.io/wordpress-background-worker/) to work.


## Usage
# Define Config

```
// The abandon order time
define('WC_ABANDON_ORDER_EMAIL_TIME',1800);
```

# Template Email

filter : `wc_abandon_order_email_template`
file : `template/abandon-order-email.php`
