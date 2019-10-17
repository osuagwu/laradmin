# Billing
Billing is implemented using Laravel Cashier. Follow Laravel Cashire guide to define env variables.

In addition to the webhooks defined by Cashier, Billing includes its own webhooks (by extending Cashier's Webhook Controller) which listens to plans and product updated, deleted and created events.  These helps to update the cached versions of plans and products. You should create a a webhook end point 
```
//{{domain}}}}/{{stripe-path}}/extra-webhook
```
 in Stripe for these to work. 

You should follow Cashier's guide to add more webhooks. 

## Stripe products
When creating a product in Stripe, make sure to include the following metadata keys for a richer display of plans:
+ *full_description* : A full description
+ *tagline* : Snappy headline e.g. 'Perfect for a beginner'
+ *icon_class* : e.g 'fas fa-user' :TODO
+ *feature_list* : A comma separated list of features of the product

## Stripe plans
When creating a plan in Stripe, make sure to include the following metadata keys:
+ *title* : A short title
+ *tagline* : Snappy headline e.g. 'Perfect for a beginner'
+ *icon_class* : e.g 'fas fa-user' :TODO
+ *feature_list* : A comma separated list of features of the plan

## Stripe subscription
Billing can identify a subscription using:
``` 
{product-name}:{{plan-nickname}
```
entered in `name` filed of Cashhier's `subscriptions` table. Use this pattern with Cashier's methods to identify a what a user subscribes to, and other subscription related checks. For the reason, a customer should not actively sub to the same product and plan more than once. 