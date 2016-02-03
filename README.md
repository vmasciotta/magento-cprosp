# Magento CPROSP

*CPROSP* stands for **C**atalog **P**rice **R**ules **O**ver **S**pecial **P**rice.

This module changes the default Catalog Price Rule behaviour when it interacts with a product having a special price.

Compatible with **Magento >= 1.9**

**The problem:**

Catalog Price Rules are not applied if the product's special price is less than the  discounted price calculated by the rules.

**Solution:**

Sadly we have to change the Magento core code, that's why I wrote this module.

**ATTENTION**

Having a product with a special price assigned on it, the discount will be calculated on the special price and not on the regular base price.

*For example*

A product having:

* **regular price** € 25.00
* **special price** € 23.00
* **catalog price rule** sets 80% discount on all catalog products

The final price will be € 4.60 and not € 5.00 because the 80% will be deduct from the special price and not from the regular price.

*Notes*

I just wrote a module based on the solution available on [StackOverflow](http://stackoverflow.com/questions/18120342/catalog-price-rules-applied-to-special-price).

Thx StackOverflow!

##Installation

Download the zip package and unzip it in your magento root installation directory.

*or*

You can install it through composer adding the following rules to your composer.json file:

```js
{
    "require":[
        ...
        "vmasciotta/magento-cprosp":"1.8"
        ...
    ],
    "repositories":[
        ...
        {"type": "vcs", "url":"https://github.com/vmasciotta/magento-cprosp.git"}
        ...
    ]
}
```
